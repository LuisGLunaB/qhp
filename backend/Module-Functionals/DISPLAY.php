<?php
class DISPLAY{
  protected static function showData($text,$display){
    if($display){
      echo $text;
    }
  }
  protected static function NoDataMessage(){
    return
    '<div style="display: block; clear: both;">
      <strong style="color: red !important;">
        No hay datos para ésta búsqueda.
      </strong>
    </div>';
  }
  public static function asJSON($data,$setHeader=True,$display=True){
    	if($setHeader){
        header('Content-Type: application/json');
      }
      $JSON = json_encode($data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    	self::showData($JSON,$display);
    	return $JSON;
  }
  public static function asUglyJSON($data,$setHeader=True,$display=True){
    	if($setHeader){
        header('Content-Type: application/json');
      }
      $JSON = json_encode($data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    	self::showData($JSON,$display);
    	return $JSON;
  }
  public static function asArray($data,$display=True){
    if($display){
      print_r($data);
    }
  }
  public static function asTable($data,$class="",$display=True){
    if( sizeof($data)>0){
    $table = '<table class="'.$class.'">';
        # First row (headers)
        $columns = array_keys($data[0]);
        $table .= "<thead><tr>";
        foreach($columns as $column){
          $table .= "<th>$column</th>";
        }
        $table .= "</tr></thead>";

        # All other rows
      $table .= "<tbody>";
        foreach($data as $row){
          $table .= "<tr>";
          foreach($columns as $column){
            $table .= "<td>$row[$column]</td>";
          }
          $table .= "</tr>";
        }
      $table .= "</tbody>";
      $table .= "</table>";
    }else{
      $table = self::NoDataMessage();
    }
    self::showData( $table,$display);
    return $table;
  }
  public static function asAPI($object,$display=True){
    $data["status"] = $object->status();
    $data["message"] = $object->message();
    $data["data"] = $object->data;
    self::asJSON($data,$setHeader=True,$display=True);
  }
}

function showFormError($form_error=NULL){
  if(is_null($form_error)){
    if( array_key_exists( "form_error", $GLOBALS) ){
      echo $GLOBALS["form_error"];
    }else{
      // NULL was given, no form has been called.
    }
  }else{
    if($form_error != ""){
      echo $form_error;
    }else{
      // There is was no error with the form.
    }
  }
}

function array_sort($array, $on, $order=SORT_ASC){

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
function was_form_submitted($form_name,$request_type="POST"){
  $REQ = get_request_type_data($request_type);
  if( array_key_exists("form",$REQ) ){
    $submitted_form = $REQ["form"];
    if( $submitted_form == $form_name ){
      return True;
    }else{
      return False;
    }
  }else{
    return False;
  }
}
function get_request_type_data($request_type="POST"){
  switch ($request_type) {
      case "POST":
          $REQ = $_POST;break;
      case "GET":
          $REQ = $_GET;break;
      default:
          $REQ = $_POST;
  }
  return $REQ;
}

function notEmptyString($x){
  return ( ! isEmptyString($x) );

}
function isEmptyString($x){
  $x = trim($x);
  return (
       ( is_null($x) )
    or ( $x == "" )
    or ( $x == "-" )
    or ( $x == "." )
  );
}
