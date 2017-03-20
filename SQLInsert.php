<?php
class SQLInsert extends SQLBasicTableManager{
  public $INSERT_query = "";
  public $ONDUPLICATE_query = "";
  public $binds = array();
  public $bindsCount = 0;
  public $lastId = NULL;

  // saveAsTable() Methods Default field sizes
  public $VARCHARsize = 200;
  public $FLOATsize = 30;
  public $INTsize = 30;

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    parent::__construct($con,$TableName,$fieldsMask);
  }

  // $INSERT_query String Parsing
  public function INSERT($valuesTable){
    $this->parseInsertIntoRow();

    $valuesTable = self::inputAsTable($valuesTable);
    $allRowsStrings = $this->valuesParser($valuesTable);

    $this->INSERT_query .= "VALUES $allRowsStrings ";
  }
  protected function parseInsertIntoRow(){
    $commaSeparatedFields = implode(", ", $this->maskedFields );
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

  public function ONDUPLICATE($fields=NULL){
    // If $fields=NULL then update all fields.
    $fields = (is_null($fields)) ?
      $this->maskedFields : self::maskArray($fields,$this->maskedFields);

    // Don't include primary key on the update
    self::unset_byvalue($this->getTableId(), $fields);

    // Loop fields to be updated and build query
    $fieldsArray = [];
    foreach($fields as $field){
      $fieldsArray[] = "$field = VALUES( $field )";
    }
    $fieldsString = implode(", ",$fieldsArray);

    //Return Query fragment
    $this->ONDUPLICATE_query ="ON DUPLICATE KEY UPDATE $fieldsString";
    return $this->ONDUPLICATE_query;
  }

  // Execution Methods
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
  public function getQuery(){
    return "$this->INSERT_query $this->ONDUPLICATE_query;";
  }
  public function getLastId(){
    return $this->lastId;
  }

  // Saving Input as Independent Table Methods
  public function saveAsTable($TableName,$data,$firstIsKey=True){
    if( self::isSafeSQLString($TableName) ){
      // Get Main Variables Right
      $data = self::inputAsTable($data);
      $fields = array_keys($data[0]);
      $firstIsKey = ( is_integer($data[0][$fields[0]]) ) ? $firstIsKey : False;

      // Build fields configuration String
      $exampleRow = $data[0];
      $fieldConfigList =$this->buildTableConfiguration($fields,$firstIsKey,$exampleRow);
      $fieldsConfigurationString = implode(", ", $fieldConfigList);

      //Try to execute query and Insert Data
      $query = "DROP TABLE IF EXISTS $TableName;CREATE TABLE $TableName ( $fieldsConfigurationString );";
      if( $this->tryToCreateTable($query) ){
        $TableName = strtolower($TableName);
        $this->insertDataToNewTable($TableName,$data);
      }
    }else{
      $this->ErrorManager->handleError("$TableName is not a valid table name." );
    }

    return $query;
  }
  protected function buildTableConfiguration($fields,$firstIsKey,$exampleRow){
    $fieldConfigList = [];

    if( $firstIsKey ){
      $fieldConfigList[] = "$fields[0] INT($this->INTsize) AUTO_INCREMENT PRIMARY KEY ";
      unset($fields[0]);
    }

    foreach($fields as $field){
      $valueInField = $exampleRow[$field];
      $fieldType = $this->getSQLFieldTypeAndSize($valueInField);
      $fieldConfigList[] = "$field $fieldType";
    }

    return $fieldConfigList;
  }
  protected function getSQLFieldTypeAndSize($valueInField){
    $fieldType = "";
    if(is_numeric($valueInField)){ $fieldType = "FLOAT($this->FLOATsize)"; }
    if(is_integer($valueInField)){ $fieldType = "INT($this->INTsize)"; }
    if(is_string($valueInField)){ $fieldType = "VARCHAR($this->VARCHARsize)"; }
    if(is_null($valueInField)){ $fieldType = "FLOAT($this->FLOATsize)"; }
    return $fieldType;
  }
  protected function tryToCreateTable($query){
    try{
      $this->Query = $this->con['handler']->prepare( $query );
      $this->Query->execute();
    }catch (Exception $e){
      $this->ErrorManager->handleError("Error in CREATE $TableName.", $e, $exitExecution=False);
    }
    return $this->status();
  }
  protected function insertDataToNewTable($TableName,$data){
    $this->TableName = $TableName;
    $this->getTableFields($reload=True);
    $this->deleteFieldsMask();
    $this->INSERT($data);
    $this->execute();
  }

  public function clear(){
    $this->INSERT_query = "";
    $this->ONDUPLICATE_query = "";
    $this->binds = array();
    $this->bindsCount = 0;
    $this->lastId = NULL;
  }

}
