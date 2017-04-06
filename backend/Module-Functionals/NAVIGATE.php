<?php

class NAVIGATE{

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
  public static function money_format($x){
    return "$".number_format( $x, 2, ".", ",");
  }

  public static function buildFormSelect($Table,$name="",$selected_value=NULL){
    $keys = array_keys($Table[0]);
    $value_field = $keys[0];
    $text_field = $keys[1];

    $name = ($name=="") ? "" : "name='$name'";

    $select = "<select $name >";
      foreach( $Table as $row ){
        $value = $row[$value_field];
        $text = $row[$text_field];
        $selected = ( is_null($selected_value) or ($selected_value != $value) )
          ? "" : "selected='selected'";
        $select .= "<option $selected value='$value'>$text</option>";
      }
    $select .= "</select>";

    return $select;
  }

  public static function buildDistinctSelect($TableName,$field,$selected_value=NULL,$ORD="ASC"){
    $SELECT = new SQLBasicSelector($TableName);
    return $SELECT->free("SELECT DISTINCT $field FROM $TableName ORDER BY $field $ORD");
  }

  public static function getProductCategories($id){
    $id = (int) $id;
    $Category = new SQLBasicSelector("product_categories_$id");
    $Category->execute();
    if( ! $Category->status() ){
      $Category->data = NULL;
    }
    return $Category->data;
  }
  public static function buildCategorySelect($id,$selected_value=NULL){
    $id = (int) $id;
    $Table = self::getProductCategories($id);
    return self::buildFormSelect($Table,"category_".$id."_id",$selected_value);
  }
}
