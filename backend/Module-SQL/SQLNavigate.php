<?php
class SQLNavigate extends SQLObject{
  public static function getTables(){
    $SQL = new SQLObject();
    $response = $SQL->QUERY("SHOW TABLES;",[],True);
    $tables = [];
    foreach($response as $row){
      foreach($row as $key => $value){
        $tables[] = $value;
      }
    }
    return $tables;
  }
  public static function getFieldsLists($TableName){
    $SQL = new SQLObject();
    $response = $SQL->QUERY("DESCRIBE $TableName;",[],True);
    $fields = [];
    foreach($response as $row){
      $fields[] = $row["Field"];
    }
    return $fields;
  }

  public static function getDistinctValues($TableName,$field,$ORD="ASC"){
    $SQL = new SQLObject();
    $response = $SQL->QUERY(
      "SELECT DISTINCT $field FROM $TableName ORDER BY $field $ORD;"
    ,[],True);
    return $response;
  }
  public static function getDistinctValuesCount($TableName,$field,$WHERE=NULL,$ORD="DESC"){
    $SQL = new SQLObject();
    $response = $SQL->QUERY(
      "SELECT DISTINCT
        $field,
        COUNT(*) AS count
       FROM $TableName
       $WHERE
       GROUP BY $field
       ORDER BY count $ORD;"
    ,[],True);
    return $response;
  }
  public static function getValueLabels($TableName,$values,$labels,$ORD="ASC"){
    $SQL = new SQLObject();
    $response = $SQL->QUERY(
      "SELECT $values, $labels, (NULL) AS count FROM $TableName ORDER BY $labels $ORD;"
    ,[],True);
    return $response;
  }
  public static function getValueLabelsCount($TableName,$values,$labels,$WHERE=NULL,$ORD="DESC"){
    $SQL = new SQLObject();
    $response = $SQL->QUERY(
      "SELECT
        $values,
        $labels,
        COUNT(*) AS count
      FROM $TableName
      $WHERE
      GROUP BY $values
      ORDER BY count $ORD;"
    ,[],True);
    return $response;
  }

  public static function InputSelectDistinct($TableName,$field,$selected_value=NULL,$label="",$ORD="ASC"){
    $selected_value = (is_array($selected_value)) ? $selected_value["$field"] : $selected_value;

    $data = self::getDistinctValues($TableName,$field,$ORD);
    $Options = "";
    foreach ($data as $row) {
      $key = $row["$field"];
      $selected = ( ($key==$selected_value) and ! is_null($selected_value) ) ? "selected" : "";
      $Options .= '<option value="'.$key.'"  '.$selected.'  >'.$key.'</option>';
    }

    $Select  = self::InputLabel($field,$label);
    $Select .= '<select id="'.$field.'" name="'.$field.'">';
    $Select .= '<option value="-">-</option>';
    $Select .= $Options;
    $Select .= '</select>';

    return $Select;
  }
  public static function InputSelectDistinctCount($TableName,$field,$selected_value=NULL,$WHERE=NULL,$label="",$ORD="DESC"){
    $selected_value = (is_array($selected_value)) ? $selected_value["$field"] : $selected_value;

    $data = self::getDistinctValuesCount($TableName,$field,$WHERE,$ORD);
    $Options = "";
    foreach ($data as $row) {
      $key = $row["$field"];
      $count = $row["count"];
      $selected = ( ($key==$selected_value) and ! is_null($selected_value) ) ? "selected" : "";
      $Options .= '<option value="'.$key.'"  '.$selected.'  >'."$key ($count)".'</option>';
    }

    $Select  = self::InputLabel($field,$label);
    $Select .= '<select id="'.$field.'" name="'.$field.'">';
    $Select .= '<option value="-">-</option>';
    $Select .= $Options;
    $Select .= '</select>';

    return $Select;
  }
  public static function InputSelectValueLabels($TableName,$values,$labels,$selected_value=NULL,$label="",$ORD="ASC"){
    $selected_value = (is_array($selected_value)) ? $selected_value["$values"] : $selected_value;

    $data = self::getValueLabels($TableName,$values,$labels,$ORD);
    $Options = "";
    foreach ($data as $row) {
      $value = $row["$values"];
      $label_text = $row["$labels"];
      $selected = ( ($value==$selected_value) and ! is_null($selected_value) ) ? "selected" : "";
      $Options .= '<option value="'.$value.'"  '.$selected.'  >'.$label_text.'</option>';
    }

    $Select  = self::InputLabel($labels,$label);
    $Select .= '<select id="'.$values.'" name="'.$values.'">';
    $Select .= '<option value="-">-</option>';
    $Select .= $Options;
    $Select .= '</select>';

    return $Select;
  }
  public static function InputSelectValueLabelsCount($TableName,$values,$labels,$selected_value=NULL,$WHERE=NULL,$label="",$ORD="DESC"){
    $selected_value = (is_array($selected_value)) ? $selected_value["$values"] : $selected_value;

    $data = self::getValueLabelsCount($TableName,$values,$labels,$WHERE,$ORD);
    $Options = "";
    foreach ($data as $row) {
      $value = $row["$values"];
      $label_text = $row["$labels"];
      $count = $row["count"];
      $selected = ( ($value==$selected_value) and ! is_null($selected_value) ) ? "selected" : "";
      $Options .= '<option value="'.$value.'"  '.$selected.'  >'."$label_text ($count)".'</option>';
    }

    $Select  = self::InputLabel($labels,$label);
    $Select .= '<select id="'.$values.'" name="'.$values.'">';
    $Select .= '<option value="-">-</option>';
    $Select .= $Options;
    $Select .= '</select>';

    return $Select;
  }

  public static function InputText($name,$value="",$label="",$placeholder=""){
    $value = (is_array($value)) ? $value["$name"] : $value;

    $Input = self::InputLabel($name,$label);
    $Input .= ' <input id="'.$name.'" type="text" name="'.$name.'" value="'.$value.'" placeholder="'.$placeholder.'"> ';
    return $Input;
  }
  public static function InputDisabledText($name,$value="",$label="",$placeholder=""){
    $value = (is_array($value)) ? $value["$name"] : $value;

    $Input = self::InputLabel($name,$label);
    $Input .= ' <input type="text" value="'.$value.'" placeholder="'.$placeholder.'" disabled> ';
    return $Input;
  }
  public static function InputHiddenText($name,$value=""){
    $value = (is_array($value)) ? $value["$name"] : $value;
    $Input = ' <input id="'.$name.'" type="hidden" name="'.$name.'" value="'.$value.'"> ';
    return $Input;
  }
  public static function InputLabel($name,$label=""){
    $label = ($label=="") ? self::LabelNamer($name) : $label;
    $Input = ' <label for="'.$name.'">'.$label.'</label> ';
    return $Input;
  }
  public static function LabelNamer($name){
    $exploded = explode("_",$name);
    if( sizeof($exploded) == 1){
      $label = ucfirst($name);
    }else{
      if( sizeof($exploded) == 2 ){
        $name = $exploded[1];
        $label = ucfirst($name);
      }else{
        $name = implode(" ",$exploded);
        $label = ucwords($name);
      }
    }
    return $label;
  }
}
?>
