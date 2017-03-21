<?php
class SQLBasicTableManager{
  Protected $con = NULL; #SQLConnector->Connection
  Protected $Query = NULL;

  public $DatabaseTablesNames = NULL;

  public $TableName = ""; #Table or View Name
  public $TableFields = NULL;
  public $maskedFields = [];
  public $fieldsMask = NULL;

  Protected $ErrorManager;

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    $this->ErrorManager = new ErrorManager();
    $this->con = $con;
    $this->TableName = $TableName;
    $this->updateFieldsMask($fieldsMask);
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
      $symbolOrIN = ( sizeof($value)==1 ) ? $symbols[$s] : "IN";
      $symbolOrIN = ( in_array($symbolOrIN,$validSymbols) ) ? $symbolOrIN : "=";
      $value = self::inputAsArray($value);
      foreach($value as $v){
        $binds[":$field$c"] = $v;
        $c++;
      }
      $whereString = implode(", ", array_keys($binds) );
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

  # Static Useful Masking and String Methods
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
    $forbiddenCharacters = ["'",'"',"%","&","=","!","|","¡","/",":",";","-"];
  	$Characters = str_split($string);
  	foreach($Characters as $char){
  		if( in_array($char,$forbiddenCharacters) ){
        $isValid = False;
        break;
      }
  	}
  	return $isValid;
  }
  public static function str_has($word,$text){
  	if (strpos($text, $word) !== false) {
  	  return True;
  	}else{
  		return False;
  	}
  }

  # Static Type-validating Methods
  public static function is_assoc($array){
      $keys = array_keys($array);
      return array_keys($keys) !== $keys;
  }
  public static function is_table($array){
    if( self::is_assoc($array) ){
      return False;
    }else{
      $firstRow = $array[0];
      if( self::is_assoc($firstRow) ){
        return True;
      }else{
        return False;
      }
    }
  }
  public static function unset_byvalue($value, &$array){
    if( ($key = array_search($value, $array)) !== false) {
        unset($array[$key]);
    }
    return $array;
  }
  public static function inputAsAssoc($regularArray,$key){
    $assoc = [];
    foreach($regularArray as $value){
      $assoc[] = array($key => $value);
    }
    return $assoc;
  }
  public static function inputAsArray($array){
    return ( is_array($array) ) ? $array : [$array];
  }
  public static function inputAsTable($array){
    return ( self::is_table($array) ) ? $array : [$array];
  }

  # Query execution Methods
  public function executeQuery($query, $binds=[] ){
    try{
      $this->Query = $this->con['handler']->prepare( $query );
      $this->Query->execute( $binds );
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error in executeQuery $this->TableName.", $e );
    }
  }
  public function executeQueryWithBinds($query,$binds){
    $this->executeQuery($query,$binds);
  }
  public function executeFetchColumn($query){
    $this->executeQuery($query);
    return $this->fetchColumn($query);
  }
  public function executeFetchTable( $query, $binds ){
    try{
			$this->Query = $this->con['handler']->prepare( $query );
			$this->Query->setFetchMode(PDO::FETCH_ASSOC);
			$this->Query->execute( $binds );
    }catch (Exception $e){
      $this->ErrorManager->handleError("Error in FetchTable $this->TableName.", $e );
		}
  }
  protected function fetchTable(){
    $data = array();
    if( $this->status() ){
      $row = 0;
      while( $QueryRow=$this->Query->fetch() ){
        $QueryFields = ($row==0) ? array_keys($QueryRow) : $QueryFields;
        foreach($QueryFields as $field){
          $data[$row][$field] = $QueryRow[$field];
        } //foreach
        $row++;
      } //while fetching
    } //if status
    return $data;
  }
  public function fetchColumn(){
    try{
      return $this->Query->fetchAll(PDO::FETCH_COLUMN);
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error in FETCH_COLUMN $this->TableName.", $e );
      return NULL;
    }
  }

  # ErrorManager state callers
  public function status(){
    return $this->ErrorManager->getStatus();
  }
  public function message(){
    return $this->ErrorManager->getMessage();
  }

}
