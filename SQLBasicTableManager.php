<?php

class SQLBasicTableManager{
  Protected $con = NULL; #SQLConnector->Connection
  Protected $Query = NULL;

  public $DatabaseTablesNames = NULL;

  public $TableName = ""; #Or View's Name
  public $TableFields = NULL;
  public $maskedFields = [];
  public $fieldsMask = NULL;

  Protected $ErrorManager;

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    $this->ErrorManager = new ErrorManager();
    $this->con = $con;
    $this->TableName = $TableName;
    $this->fieldsMask = $fieldsMask;

    if( $this->isFieldsMaskOn() ){
      $this->maskFields();
    }

  }

  public function getTableFields( $reloadFields = False ){
		if( $this->isTableFieldsNULL() or $reloadFields==True ){
      $this->DESCRIBE();
		}
		return $this->TableFields;
  }
  public function getTableId( $reloadFields = False ){
    $this->getTableFields( $reloadFields );
    return $this->TableFields[0];
  }
  protected function DESCRIBE(){
    try{
      $this->Query = $this->con['handler']->prepare("DESCRIBE $this->TableName ");
      $this->Query->execute();
      $this->TableFields = $this->Query->fetchAll(PDO::FETCH_COLUMN);
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error in DESCRIBE $this->TableName.", $e );
    }
    return $this->TableFields;
  }

  protected function maskFields(){
    if( $this->isFieldsMaskOn() ){
      $this->maskedFields = self::maskArray( $this->fieldsMask, $this->getTableFields() );
    }else{
      $this->maskedFields = $this->getTableFields();
    }
  }
  public function updateFieldsMask( array $newMask ){
    $this->fieldsMask = $newMask;
    $this->maskFields();
  }
  public function deleteFieldsMask(){
    $this->fieldsMask = NULL;
    $this->maskFields();
  }
  protected function isFieldsMaskOn(){
    return !is_null($this->fieldsMask);
  }

  public static function maskArray(array $maskArray, array $validKeysArray){
    $maskedArray = [];
    foreach($maskArray as $key){
      if( in_array($key,$validKeysArray) ){
        $maskedArray[] = $key;
      }
    }
    return $maskedArray;
  }
  public static function maskAssocArray(array $maskAssocArray, array $validKeysArray){
    $maskedArray = array();
    foreach($maskAssocArray as $key => $value ){
      if( in_array($key,$validKeysArray) ){
        $maskedArray[$key] = $value;
      }
    }
    return $maskedArray;
  }
  public static function multi_str_replace( array $search_replace_assoc, $subject){
    foreach($search_replace_assoc as $search => $replace){
      $subject = str_replace($search,$replace,$subject);
    }
    return $subject;
  }
  public static function isSafeSQLString($string){
  	$isValid = True;
    $forbiddenCharacters = ["'",'"',"%","&","=","!","|","¡","/",":",";"];
  	$Characters = str_split($string);
  	foreach($Characters as $char){
  		if( in_array($char,$forbiddenCharacters) ){
        $isValid = False;
        break;
      }
  	}
  	return $isValid;
  }

  public function retrieveDatabaseTablesNames( $reloadFields = False ){
    if( $this->isTablesNamesNULL() or $reloadFields==True ){
      $this->TABLES();
		}
		return $this->DatabaseTablesNames;
  }
	protected function TABLES(){
		try{
			$this->Query = $this->con['handler']->prepare("SHOW TABLES ");
			$this->Query->execute();
			$this->DatabaseTablesNames = $this->Query->fetchAll(PDO::FETCH_COLUMN);
		}catch(Exception $e){
			$this->ErrorManager->handleError("Error in SHOW TABLES.", $e );
		}
		return $this->DatabaseTablesNames;
	}
  public function isValidTable(){
    return in_array( $this->TableName, $this->retrieveDatabaseTablesNames(True) );
  }
  public function assertValidTable(){
    try{
      if( !$this->isValidTable() ){
        throw new Exception("#Custom exception in assertValidTable.");
      }
    } catch (Exception $e){
      $this->ErrorManager->handleError("Table $this->TableName is not in the Database.", $e );
    }
  }

  protected function isTableFieldsNULL(){
    return is_null($this->TableFields);
  }
  protected function isTablesNamesNULL(){
    return is_null($this->DatabaseTablesNames);
  }

  public function status(){
    return $this->ErrorManager->getStatus();
  }
  public function message(){
    return $this->ErrorManager->getMessage();
  }

}
