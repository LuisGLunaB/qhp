<?php
class SQLBasicTableManager extends SQLObject{
  protected $DatabaseTablesNames = NULL;

  public $TableName = ""; #Table or View Name
  protected $TableFields = NULL;
  protected $maskedFields = [];
  protected $fieldsMask = NULL;

  Public $WHERE_query = "";
  Public $WHERE_binds = [];

  public $lastId = NULL;

  public function __construct($TableName, array $fieldsMask = NULL, $con=NULL ){
    parent::__construct($con);
    $this->setTableName($TableName);
    $this->updateFieldsMask($fieldsMask);
  }

  # Getters
  public function getTableNames(){return $this->DatabaseTablesNames;}
  public function getTableName(){return $this->TableName;}
  public function getFields(){return $this->TableFields;}
  public function getMaskedFields(){return $this->maskedFields;}
  public function getFieldsMask(){return $this->fieldsMask;}

  # Setters
  public function setTableName($TableName){
    $this->TableName = self::simpleStringWhiteListing($TableName,"");
  }

  # TableNames Retrievers Methods
  public function getAllTableNames( $reload = False ){
    if( $this->isTablesNamesNULL() or $reload==True ){
      $this->TABLES();
		}
		return $this->DatabaseTablesNames;
  }
	protected function TABLES(){
    $this->DatabaseTablesNames = $this->executeFetchColumn( "SHOW TABLES;" );
		return $this->DatabaseTablesNames;
	}
  public function isValidTable(){
    return in_array( $this->TableName, $this->getAllTableNames(True) );
  }
  public function isValidForeignTable($TableName){
    return in_array( $TableName, $this->getAllTableNames(True) );
  }
  public function assertValidTable(){
    try{
      if( !$this->isValidTable() ){
        throw new Exception("#Custom exception in assertValidTable.");
      }
    } catch (Exception $e){
      $this->ErrorManager->handleError("Table $this->TableName does not exist.", $e );
    }
  }
  protected function isTablesNamesNULL(){
    return is_null($this->DatabaseTablesNames);
  }

  # Table Fields Retrievers Methods
  public function getTableFields( $reload = False ){
		if( $this->isTableFieldsNULL() or $reload ){
      $this->DESCRIBE();
		}
		return $this->TableFields;
  }
  protected function DESCRIBE(){
    $this->TableFields = $this->executeFetchColumn( "DESCRIBE $this->TableName;" );
    return $this->TableFields;
  }
  public function getTableId( $reload = False ){
    $this->getTableFields( $reload );
    return $this->TableFields[0];
  }
  protected function isTableFieldsNULL(){
    return is_null($this->TableFields);
  }
  public function isValidField($foreignField){
    return in_array($foreignField,$this->getTableFields());
  }

  # Checking for specifcic Data Methods
  public static function simpleWhere($assocWhere=[],$symbols="="){
    $validSymbols = ["=",">",">=","<","<=","IN","LIKE"];
    $binds = [];
    $wheres = [];
    $c = 0; # Binded values count
    $s = 0; # Current Symbol count
    $symbols = self::inputAsArray($symbols);
    foreach($assocWhere as $field => $value){
      $value = self::inputAsArray($value);
      $s = ( sizeof($symbols)==1 ) ? 0 : $s;
      $symbolOrIN = ( sizeof($value)==1 ) ? $symbols[$s] : "IN";
      $symbolOrIN = ( in_array($symbolOrIN,$validSymbols) ) ? $symbolOrIN : "=";

      $currentbinds = [];
      $value = self::inputAsArray($value);
      foreach($value as $v){
        $binds[":$field$c"] = $v;
        $currentbinds[":$field$c"] = $v;
        $c++;
      }

      $whereString = implode(", ", array_keys($currentbinds) );
      $wheres[] = "$field $symbolOrIN ( $whereString )";
      $s++;
    }
    $wheres = implode(" AND ", $wheres);
    $WHERE = ($wheres=="") ? "" : "WHERE $wheres";

    return [$WHERE,$binds];
  }
  public function WhereCounter($assocWhere=[],$symbols="="){
    $this->maskWithMyFields($assocWhere);
    list($WHERE,$WHEREbinds) = self::simpleWhere($assocWhere,$symbols);

    $query = "SELECT COUNT(*) AS count FROM $this->TableName ";
    $query .= $WHERE.";";

    $this->executeFetchTable( $query , $WHEREbinds );
    $data = $this->fetchTable();
    return $data[0]["count"];
  }
  public function EXISTS($assocWhere=[],$symbols="="){
    $count = $this->WhereCounter($assocWhere,$symbols);
    return ($count>0);
  }
  public function NOTEXISTS($assocWhere=[],$symbols="="){
    return ( ! $this->EXISTS($assocWhere,$symbols) );
  }
  public function BASICWHERE($assocWhere,$symbols="="){
    $this->maskWithMyFields($assocWhere);
    list($this->WHERE_query,$this->WHERE_binds) = $this->simpleWhere($assocWhere,$symbols);
  }

  # Fields Maskers Methods
  protected function maskFields(){
    if( $this->isFieldsMaskOn() ){
      $this->maskedFields = self::maskArray( $this->fieldsMask, $this->getTableFields() );
    }else{
      $this->maskedFields = $this->getTableFields();
    }
  }
  public function updateFieldsMask( $newMask ){
    $this->fieldsMask = $newMask;
    $this->maskFields();
  }
  public function deleteFieldsMask(){
    $this->updateFieldsMask(NULL);
  }
  protected function isFieldsMaskOn(){
    return !is_null($this->fieldsMask);
  }
  protected function maskWithMyMask(&$array){
    if( self::is_assoc($array) ){
      $array = self::maskAssocArray($array, $this->fieldsMask );
    }else{
      $array = self::maskArray($array, $this->fieldsMask );
    }
    return $array;
  }
  protected function maskWithMyFields(&$array){
    if( self::is_assoc($array) ){
      $array = self::maskAssocArray($array, $this->getTableFields() );
    }else{
      $array = self::maskArray($array, $this->getTableFields() );
    }
    return $array;
  }

}
