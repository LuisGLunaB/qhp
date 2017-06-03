<?php
# Database Access
$db_host = "localhost";
$db_name = "test";
$db_user = "root";
$db_pass = "";

$SQLConnection1 = new SQLConnector($db_host,$db_name,$db_user,$db_pass);
$con = $SQLConnection1->getConnector();

class SQLConnector{
	Protected $Connection = NULL;

	Protected $DatabaseHost = "";
	Protected $DatabaseName = "";
	Protected $Username = "";
	Protected $Password = "";

	Protected $ErrorManager;

	public function __construct($DatabaseHost=NULL,$DatabaseName=NULL,$Username=NULL,$Password=NULL){
		$this->ErrorManager = new ErrorManager();
		$this->setConnectionVariables($DatabaseHost,$DatabaseName,$Username,$Password);
		if( $this->isConnectionDataComplete() ){
			$this->createNewConnection();
		}
	}
	public function setConnectionVariables($DatabaseHost,$DatabaseName,$Username,$Password){
		$this->DatabaseHost = $DatabaseHost;
		$this->DatabaseName = $DatabaseName;
		$this->Username = $Username;
		$this->Password = $Password;
	}

	Protected function isConnectionDataComplete(){
		return (
			($this->DatabaseHost !="" ) and
			($this->DatabaseName !="" ) and
			($this->Username !="")
		);
	}

	public function createNewConnection(){
		$this->assertConnectionData();

		$this->createConnectionHandler();
		$this->setPDOErrorMode();

    return $this->getConnector();
	}
	Protected function createConnectionHandler(){
		try{
			$this->Connection['handler'] =
				new PDO(
					"mysql:host=$this->DatabaseHost;
					dbname=$this->DatabaseName;",
					$this->Username,
					$this->Password,
					array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
			);
		}catch(PDOException $e){
			$this->ErrorManager->handleError("Error when creating Connection Handler.", $e );
		}
	}
	Protected function setPDOErrorMode(){
		try {
			if( $this->ErrorManager->weAreDebugging() ){
				$this->Connection['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			}else{
				$this->Connection['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			}
		} catch (PDOException $e){
			$this->ErrorManager->handleError("Error when setting PDO ERRMODE.", $e );
		}
	}

	public function assertConnectionData(){
		try{
			if( !$this->isConnectionDataComplete() ){
				throw new Exception("#Custom exception: Missing database fields.");
			}
		} catch (Exception $e){
			$this->ErrorManager->handleError("Connection data is incomplete.", $e, $exitExecution=False );
		}
	}
	public function assertOpenConnection(){
		try{
			if( is_null($this->Connection) or !$this->status() ){
				throw new Exception("#Custom exception: NULL Connection or False status detected.");
			}
		} catch (Exception $e){
			$this->ErrorManager->handleError("Connection is not open.", $e, $exitExecution=False );
		}
	}
	public function getConnector(){
		$this->assertOpenConnection();
		return $this->Connection;
	}

  public function status(){
    return $this->ErrorManager->getStatus();
  }
  public function message(){
    return $this->ErrorManager->getMessage();
  }

}
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
  public static function string_to_url($string){
    $validCharacters =
    ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r",
    "s","t","u","v","w","x","y","z","-",0,1,2,3,4,5,6,7,8,9,
    "0","1","2","3","4","5","6","7","8","9"];
    $replaceWithDashCharacters =
    [" ","/","_"];

    $characters = str_split( strtolower($string) );
    $return_string = "";

    foreach( $characters as $char){
      if( in_array($char,$validCharacters,True) ){
        $return_string .= $char;
      }else{
        if( in_array($char,$replaceWithDashCharacters) ){
          $return_string .= "-";
        }
      }
    }
    return $return_string;
  }
  public static function is_url_safe($string){
    return ( $string==self::string_to_url($string) );
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
    if( $this->status() ){
      $fetch = ( substr($query,0,6) == "SELECT" ) ? True : $fetch;
      return ($fetch) ? $this->tryFetchTable() : NULL;
    }else{
      return NULL;
    }
  }
  # Query execution Methods
  public function executeQuery($query, $binds=[] ){
    try{
      $this->Query = $this->con['handler']->prepare( $query );
      $this->Query->execute( $binds );
      //Catch Warnings: "Warning: PDOStatement::execute(): "
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error in executeQuery $this->Query.", $e );
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
      $this->ErrorManager->handleError( "Error in executeQuery $this->Query.", $e );
    }
  }
  public function executeFetchId( $query, $binds=[] ){
    try{
			$this->Query = $this->con['handler']->prepare( $query );
			$this->Query->execute( $binds );
      $this->lastId = $this->con['handler']->lastInsertId();
    }catch (Exception $e){
      $this->lastId = NULL;
      $this->ErrorManager->handleError("Error in INSERT $this->Query.", $e );
		}
    return $this->status();
  }

  public function lastInsertId(){
    return $this->con['handler']->lastInsertId();
  }

  public function VariableOrErrorvalue($Variable,$Errorvalue=NULL){
    if( $this->status() ){
      return $Variable;
    }else{
      return $Errorvalue;
    }
  }
  # Fetching Methods
  protected function tryFetchTable(){
    try{
      return $this->fetchTable();
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error when fetching Query $this->Query.", $e );
      return NULL;
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
class ErrorManager{
	Public $debugging = False;
	Protected $status = True;
	Protected $e = NULL;
	Public $exitExecution = False;

	Protected $errorMessage = ""; //String
	Protected $BackTraceString = ""; //String
	Protected $BackTraceData = ""; //JSON
	Protected $State = ""; //JSON

	// Protected $DateTime = "";
	// Sense User and Catch It

	public function __construct(){
		$this->matchGlobalDebuggingStatus();
	}

	private function matchGlobalDebuggingStatus(){
		if( array_key_exists( "debugging", $GLOBALS) ){
			$this->debugging = $GLOBALS["debugging"];
		}
	}
	public function weAreDebugging(){
		return $this->debugging;
	}
	public function handleError($errorMessage, $e=NULL, $exitExecution = NULL ){
		$this->status = False;
		$this->errorMessage = utf8_encode($errorMessage);
		$this->e = $e;

		$this->errorMessage .= ( is_null($e) ) ? "" : ( " | system: " . $e->getMessage() );
		$this->BackTraceString = self::debug_string_backtrace();
		$this->BackTraceData = json_encode( debug_backtrace() );
		$this->State = json_encode( $_REQUEST );
		// $this->$StateJSON = json_encode( $_REQUEST, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		alert_if_debugging( $errorMessage );
		$this->manageExecutionEnd( $exitExecution );
	}

	protected function manageExecutionEnd( $exitExecution ){
		# If $exitExecution is NULL, use current object state for the exit; decision.
		if( is_null($exitExecution) ){
			$exitExecution = $this->exitExecution;
		}
		if( $exitExecution ){ exit; }

		return $exitExecution;
	}

	protected static function debug_string_backtrace() {
			ob_start();
			debug_print_backtrace();
			$trace = ob_get_contents();
			ob_end_clean();

			// Remove first item from backtrace as it's this function which
			// is redundant.
			$trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

			// Renumber backtrace items.
			$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);

			return $trace;
	}

	public function getErrorObject(){
		return $this->e;
	}

	public static function showErrorMessage($errorMessage){
		echo $errorMessage;
	}
	public static function alertErrorMessage($errorMessage){
		printf("<script>alert('%s');</script>", $errorMessage);
	}

	public function getStatus(){
		return $this->status;
	}
	public function getMessage(){
		return $this->errorMessage;
	}

}
function alert_if_debugging($string){
	if( array_key_exists("debugging", $GLOBALS) ){
		$debugging = $GLOBALS["debugging"];
	}else{
		$debugging = False;
	}
	if($debugging){ alert($string); }
}
function alert($string){
	printf("<script>alert('%s');</script>", $string);
}

class FormObject extends SQLObject{
  public $FormTableName = "forms";
  public $ValidFormFields = ["email","name","phone","message","others"];

  public $FormData = [];
  public $email_web= "web@mkti.mx";
  public $email_manager = "contacto@mkti.mx";
  public $email_customer= NULL;

  public function __construct($FormData, $con=NULL){
    parent::__construct($con);
    $this->FormData = $FormData;
  }
  public function SaveData(){
    $MaskedFormData = $this->MaskFormData( $this->FormData );
    $BindedFormData = $this->BindFormData( $this->FormData );

    $FieldsPlaceholder = self::getPlaceholders($MaskedFormData); //field1,field2
    $BindsPlaceholder = self::getPlaceholders($BindedFormData); //:field1,:field2

    $INSERTQUERY =
      "INSERT INTO $this->FormTableName ( $FieldsPlaceholder )
       VALUES ( $BindsPlaceholder ); ";

    $this->QUERY( $INSERTQUERY , $BindedFormData );

    return $this->status();
  }
  protected function BindFormData($FormData){
    $FormData = $this->MaskFormData($FormData);
    $binds = array();
    foreach($FormData as $field => $value){
      $binds[":$field"] = $value;
    }
    return $binds;
  }
  protected function MaskFormData($FormData){
    $MaskedFormData = [];
    foreach($FormData as $field => $value){
      if( $this->isValidField($field) ){
        $MaskedFormData[$field] = $value;
      }
    }
    return $MaskedFormData;
  }
  protected static function getPlaceholders($AssocArray){
    return implode(", ", array_keys($AssocArray) );
  }
  protected function isValidField($field){
    return in_array( $field, $this->ValidFormFields );
  }

}
