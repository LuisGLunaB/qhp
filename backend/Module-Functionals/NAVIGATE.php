<?php

class NAVIGATE{
  public static $emptyValues =
    [""," ","  ","-","--","ALL","All","Todos","TODOS","Todas","TODAS",".","_",NULL,
    "NONE","None","Ninguno","NINGUNO","Ninguna","NINGUNA",
    "Ningunos","Ningunas","NINGUNOS","NINGUNAS","Nada","NADA","Sin filtro","Sin filtros",
    "No filter","No filters","Without filter","Without filters"];

  public static function PaginationBar($page,$limit,$total,$options=5){
    list($start_page, $current_page, $end_page, $last_page) = self::PaginationVariables($page,$limit,$total,$options);
    return "$start_page - $current_page - $end_page : $last_page";
  }
  public static function PaginationVariables($page,$limit,$total,$options=5){
    $step = ceil( ($options-1) / 2 );

    $current_page = ($page + 1);
    $last_page = ceil( $total / $limit );
    $start_page = max( 1 , ($current_page - $step) );
    $end_page = min( $last_page , $start_page + ($step * 2) );
    return [$start_page, $current_page, $end_page, $last_page];
  }
  public static function PaginationDescription($page,$limit,$total){
    $min_record = ( $limit * $page ) + 1;
    $max_record = ( $limit * ($page + 1) );

    $min_record = self::thousands_format($min_record);
    $max_record = self::thousands_format($max_record);
    $total = self::thousands_format($total);
    return "Resultados: $min_record a $max_record de $total" ;
  }

  public static function thousands_format($x){
    return number_format( $x, 0 );
  }
  public static function money_format($x,$symbol="$"){
    return $symbol.number_format( $x, 2, ".", ",");
  }

  public static function getCategoriesCount($ViewName,$id=1,$WHERE_query="",$WHERE_binds = []){
    return NULL;
  }
  public static function getFieldCount($ViewName,$id_field,$field,$WHERE_query="",$WHERE_binds = []){
    $COUNT = new SQLBasicSelector($ViewName);
    if( $COUNT->isValidTable() and $COUNT->isValidField($field) and $COUNT->isValidField($id_field)){
      $field_name = ($id_field==$field) ? ($field."_name") : $field;
      $data = $COUNT->free("
        SELECT
          $id_field,
          $field AS $field_name,
          COUNT($field) AS ".$field."_count
        FROM $ViewName
        $WHERE_query
        GROUP BY $id_field
        ORDER BY ".$field."_count DESC
      " , $WHERE_binds );
      if ( ! $COUNT->status() ){
        $data = NULL;
      }
    }else{
      $data = NULL;
    }

    return $data;
  }

  public static function buildFormSelect($Table,$selected_value=NULL,$default="--"){
    $keys = array_keys($Table[0]);
    $name = "name=$keys[0]";
    $value_field = $keys[0];
    $text_field = $keys[1];
    $count_field = ( sizeof($keys) > 2) ? $keys[2] : NULL;

    $has_selected = False;
    $selected = "";
    $select = "";

    foreach( $Table as $row ){
      $value = $row[$value_field];
      $text = $row[$text_field];
      $count = ( is_null($count_field) ) ? "" : " ($row[$count_field])";
      if( self::isEmptyValue($value) ){ continue; }

      if( is_null($selected_value) or ($selected_value != $value) ){
        $selected = "";
      }else{
        $selected = "selected='selected'";
        $has_selected = True;
      }

      $select .= "<option $selected value='$value'>$text$count</option>";
    }

    if( ! $has_selected ){
      $select = "<option selected value=''>$default</option>".$select;
    }

    return "<select $name >$select</select>";
  }

  public static function buildDistinctSelect($TableName,$field,$selected_value=NULL,$default="--",$ASC=True){
    $Table = self::getDistinctOptions($TableName,$field,$ASC);
    return self::buildFormSelect($Table,$selected_value,$default);
  }
  public static function buildFieldSelect($TableName,$field,$selected_value=NULL,$default="--",$ASC=True){
    $Table = self::getFieldOptions($TableName,$field,$ASC);
    return self::buildFormSelect($Table,$selected_value,$default);
  }
  public static function buildCategorySelect($id,$selected_value=NULL,$default="--",$ASC=True){
    $id = (int) $id;
    $Table = self::getCategories($id,$ASC);
    return self::buildFormSelect($Table,$selected_value,$default);
  }

  public static function getDistinctOptions($TableName,$field,$ASC=True){
    $ASC = ($ASC==True) ? "ASC" : "DESC";
    $SELECT = new SQLBasicSelector($TableName);
    return $SELECT->free("SELECT DISTINCT $field, $field AS $field"."_name"." FROM $TableName ORDER BY $field $ASC");
  }
  public static function getFieldOptions($Tablename,$field,$ASC=True){
    $ASC = ($ASC==True) ? "ASC" : "DESC";
    $SELECT = new SQLBasicSelector($Tablename);
    if( $SELECT->isValidTable() and $SELECT->isValidField($field) ){
      $id_field = $SELECT->getTableId();
      $SELECT->free("SELECT $id_field, $field FROM $Tablename ORDER BY $field $ASC ");
      if( ! $SELECT->status() ){
        $SELECT->data = NULL;
      }
    }else{
      $SELECT->data = NULL;
    }
    return $SELECT->data;
  }
  public static function getCategories($id,$ASC=True){
    $id = (int) $id;
    return self::getFieldOptions("product_categories_$id","category_".$id."_name",$ASC);
  }

  public static function hasValue($value){
    return ( ! self::isEmptyValue($value) );
  }
  public static function isEmptyValue($value){
    return in_array($value,self::$emptyValues);
  }

}
