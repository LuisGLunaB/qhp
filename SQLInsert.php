<?php

class SQLInsert extends SQLBasicTableManager{
  public $INSERT_query = "";
  public $ONDUPLICATE_query = "";
  public $binds = array();
  public $bindsCount = 0;
  protected $lastId = NULL;

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    parent::__construct($con,$TableName,$fieldsMask);
  }

  public function INSERT($values){
    $this->parseInsertIntoRow();

    # Make sure to pass a Table to the valuesParser()
    $valuesTable = ( self::is_table($values) ) ? $values : [$values];

    $allRowsStrings = $this->valuesParser($valuesTable);

    $this->INSERT_query .= "VALUES $allRowsStrings ";
  }
  public function ONDUPLICATE($fields=NULL){
    $fields = (is_null($fields)) ?
      $this->maskedFields : self::maskArray($fields,$this->maskedFields);

    self::unset_byvalue($this->getTableId(), $fields);

    $fieldsArray = [];
    foreach($fields as $field){
      $fieldsArray[] = "$field = VALUES( $field )";
    }
    $fieldsString = implode(", ",$fieldsArray);

    $this->ONDUPLICATE_query ="ON DUPLICATE KEY UPDATE $fieldsString";

    return $this->ONDUPLICATE_query;
  }

  protected function parseInsertIntoRow(){
    $this->maskFields();
    $commaSeparatedFields = implode(", ",$this->maskedFields);
    $this->INSERT_query  = "INSERT INTO $this->TableName ( $commaSeparatedFields ) ";
  }
  protected function valuesParser($valuesTable){
    $rowsStrings = [];
    foreach($valuesTable as $insertRow){
      $singleRowArray = $this->parseSingleRow($insertRow);
      $rowsStrings[] = "( ". implode(", ",$singleRowArray)." )";
    }
    return implode(", ", $rowsStrings);
  }
  protected function parseSingleRow($insertRow){
    $singleRowArray = [];
    foreach ($this->maskedFields as $key) {
      $bindKey = ":insert$this->bindsCount";
      $this->binds[$bindKey] =
        ( array_key_exists($key,$insertRow) ) ? $insertRow[$key] : NULL;
      $singleRowArray[] = $bindKey;
      $this->bindsCount++;
    }
    return $singleRowArray;
  }
  public function getQuery(){
    return "$this->INSERT_query $this->ONDUPLICATE_query;";
  }

  public function execute(){
    try{
			$this->Query = $this->con['handler']->prepare( $this->getQuery() );
			$this->Query->execute( $this->binds );
      $this->lastId = $this->con['handler']->lastInsertId();
    }catch (Exception $e){
      $this->ErrorManager->handleError("Error in INSERT $this->TableName.", $e );
		}
    return $this->status();
  }

  public function getLastId(){
    return $this->lastId;
  }

  public function clear(){
    $this->INSERT_query = "";
    $this->binds = array();
    $this->bindsCount = 0;
  }

}
