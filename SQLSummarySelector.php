<?php
# OPERATIONS & GROUP BY: DISTINCT, COUNT, SUM, AVG, MAX, MIN, LENGTH, UPPER, LOWER
class SQLSummarySelector extends SQLBasicSelector{
  public $OPERATIONS_query = "";
  public $GROUPBY_query = "";
  public $operationsArray = [];
  public $validOperations =
    ["DISTINCT", "COUNT", "SUM", "AVG", "STD", "MAX", "MIN", "LENGTH", "UPPER", "LOWER"];
  public $maskORDERBY = False;

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    parent::__construct($con,$TableName,$fieldsMask);
    $this->buildSELECT();
  }

  public function OPERATION($fields, $tag=NULL, $OPERATION="AVG"){
    $this->getTableFields();
    $fields = self::maskArray($fields, $this->TableFields);
    $tag = ( is_null($tag) ) ? "_$OPERATION" : $tag;

    if( $this->isValidOperation($OPERATION) ){
      foreach($fields as $field){
        $this->operationsArray[] = "$OPERATION( $field ) AS $field$tag";
      }
    }else{
      $this->ErrorManager->handleError( "Operation '$OPERATION' is not valid." );
    }

    $this->OPERATIONS_query = implode(", ", $this->operationsArray );
    return $this->OPERATIONS_query;
  }

  # All the following functions just call the OPERATION method.
  public function DISTINCT($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "DISTINCT");
  }
  public function COUNT($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "COUNT");
  }
  public function SUM($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "SUM");
  }
  public function STD($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "STD");
  }
  public function AVG($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "AVG");
  }
  public function MAX($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "MAX");
  }
  public function MIN($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "MIN");
  }
  public function LENGTH($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "LENGTH");
  }
  public function UPPERCASE($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "UPPER");
  }
  public function LOWERCASE($fields, $tag=NULL){
    return $this->OPERATION($fields, $tag, "LOWER");
  }

  public function GROUPBY($fields){
    $this->getTableFields();
    $fields = self::maskArray($fields, $this->TableFields);
    $fieldsString = implode(", ", $fields);
    $this->GROUPBY_query = "GROUP BY $fieldsString ";
    return $this->GROUPBY_query;
  }

  public function isValidOperation($operation){
    return ( in_array($operation,$this->validOperations) );
  }
  public function clearOperations(){
    $this->$operationsArray = [];
  }

  public function getQuery(){
    $this->SELECT_query = $this->parseOperations();
    $this->COMPLETE_query =
      $this->SELECT_query . " " .
      $this->WHERE_query . " " .
      $this->GROUPBY_query . " " .
      $this->ORDERBY_query . " " .
      $this->PAGINATION_query;
    return $this->COMPLETE_query;
  }
  protected function parseOperations(){
    if( $this->commaSeparatedFields=="" or self::str_has("*",$this->SELECT_query) ){
      return "SELECT $this->OPERATIONS_query FROM $this->TableName ";
    }else{
      return str_replace("FROM",", $this->OPERATIONS_query FROM",$this->SELECT_query);
    }
  }

}
