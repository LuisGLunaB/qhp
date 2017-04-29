<?php

class SQLObject{
  protected $con = NULL; #SQLConnector->Connection
  protected $Query = NULL;

  protected $ErrorManager;

  public function __construct( $con=NULL ){
    $this->ErrorManager = new ErrorManager();
    $this->setConnection($con);
  }

  # Connection Methods #
  public function setConnection($con){
    if( is_null($con) ){
      $this->matchWithGlobalConnection();
    }else{
      $this->connect($con);
    }
  }
  private function matchWithGlobalConnection(){
		if( isset($GLOBALS["con"]) ){
			$this->connect($GLOBALS["con"]);
		}else{
      $this->ErrorManager->handleError("No Global Connection detected." );
    }
	}
  public function connect($con){
    $this->con = $con;
  }
  public function isConnected(){
    $is_connected = False;

    if ( ! is_null($this->con) ){
      try{
        $this->con['handler']->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        $is_connected = True;
      }catch(exception $e){
        $is_connected = False;
      }
    }else{
      $is_connected = False;
    }

    return $is_connected;
  }
  public function disconnect(){
    $this->con = NULL;
  }
  public function getConnection(){
    return $this->con;
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
  public static function stringWhiteListing($string,array $validCharacters,$wildcard=""){
    $whiteString = [];
  	$Characters = str_split($string);
  	foreach($Characters as $char){
  		if( in_array($char,$validCharacters) ){
        $whiteString[] = $char;
      }else{
        $whiteString[] = $wildcard;
      }
  	}
  	return implode("",$whiteString);
  }
  public static function simpleStringWhiteListing($string,$wildcard=""){
    $validCharacters = "
      ABCDEFGHIJKLMNOPQRSTUVWXYZ
      abcdefghijklmnopqrstuvwxyz
      ÑÁÉÍÓÚ
      ñáéíóú
      0123456789 +*-_.,
    ";
    $validCharacters = str_split($validCharacters);
    return self::stringWhiteListing($string,$validCharacters,$wildcard);
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

  # CALL methods
  public function CALL($ProcedureOrFunctionName, $ArgumentsArray, $fetch=True){
    if( self::isSafeSQLString($ProcedureOrFunctionName) ){
        $Arguments = [];
        $binds = array();
        $c = 0;
        $ArgumentsArray = self::inputAsArray($ArgumentsArray);
        foreach($ArgumentsArray as $SingleArgument){
          $Arguments[] = ":arg$c";
          $binds[":arg$c"] = $SingleArgument;
          $c++;
        }

        $Arguments = implode( ", ", $Arguments );
        $query = "CALL $ProcedureOrFunctionName( $Arguments );";
        $this->executeFetchTable( $query, $binds );

        $OUT[0]["OUT"] = NULL;
        return ($fetch) ? $this->fetchTable() : $OUT;
    }else{
        $OUT[0]["OUT"] = NULL;
        return $OUT;
    }
  }
  public function CALL_ASSOC($ProcedureOrFunctionName, $ArgumentsArray){
    $response = $this->CALL($ProcedureOrFunctionName, $ArgumentsArray, True);
    return $response[0];
  }
  public function CALL_SINGLE($ProcedureOrFunctionName, $ArgumentsArray ){
    $response = $this->CALL($ProcedureOrFunctionName, $ArgumentsArray, True);
    $response = $response[0];
    $keys = array_keys($response);
    $firstkey = $keys[0];
    return $response[$firstkey];
  }

  public function QUERY( $query, $binds=[], $fetch=False ){
    $this->executeFetchTable($query,$binds);
    $fetch = ( substr($query,0,6) == "SELECT" ) ? True : $fetch;
    return ($fetch) ? $this->fetchTable() : NULL;
  }
  # Query execution Methods
  public function executeQuery($query, $binds=[] ){
    try{
      $this->Query = $this->con['handler']->prepare( $query );
      $this->Query->execute( $binds );
      //Catch Warnings: "Warning: PDOStatement::execute(): "
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
      //Catch Warnings: "Warning: PDOStatement::execute(): "
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error in executeQuery $this->TableName.", $e );
    }
  }
  public function executeFetchId( $query, $binds=[] ){
    try{
			$this->Query = $this->con['handler']->prepare( $query );
			$this->Query->execute( $binds );
      $this->lastId = $this->con['handler']->lastInsertId();
    }catch (Exception $e){
      $this->lastId = NULL;
      $this->ErrorManager->handleError("Error in INSERT $this->TableName.", $e );
		}
    return $this->status();
  }

  # Fetching Methods
  protected function fetchTable(){
    $data = array();
    if( $this->status() ){
      $row = 0;
      while( $QueryRow=$this->Query->fetch() ){
        $QueryFields = ($row==0) ? array_keys($QueryRow) : $QueryFields;
        foreach($QueryFields as $field){
          $data[$row][$field] = $QueryRow[$field];
        } //foreach fields
        $row++;
      } //while foreach row
      $this->Query->closeCursor();
    } //if status
    return $data;
  }
  public function fetchColumn(){
    try{
      return $this->Query->fetchAll(PDO::FETCH_COLUMN);
      $this->Query->closeCursor();
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
