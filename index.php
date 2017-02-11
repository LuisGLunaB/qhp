<?php
$salt = "viveriveniversumvivusvici";

$debugging = True;
if($debugging){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}
# $q['handle']->bindValue(':'.$key, $value);
# $q['handle']->bindValue(':table', $table);

function is_assoc(array $array){
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
}

class SQL_Connection{
	public $con = NULL;
  protected $debugging = True;
  protected $host = NULL;
  protected $database = NULL;
  protected $user = NULL;
  protected $password = NULL;
  protected $status = False;
	protected $type = "mysql";
	protected $path = "";
  protected $message = "";

  public function __construct($host = NULL, $database = NULL, $user = NULL, $password = NULL){
      $this->host = $host;
      $this->database = $database;
      $this->user = $user;
      $this->password = $password;
  }
	public static function alert($text){
		if($text!=""){echo "<script>alert('$text')</script>";}
  }
	public static function Qconnect($host,$database,$user,$password,$debugging = False,$type="mysql",$path=""){
		//$type = [mysql,mssql,sybase,sqlite]
    $status = True;
    //Try to connect
    try {
      //$PDOstring = "$type:host=$host;dbname=$database;";if($type="sqlite"){$PDOstring = "$type:$path;";}
			$con['handler'] = new PDO("mysql:host=$host;dbname=$database;", $user, $password,
		  array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

  	  if($debugging){
  	  	$con['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );//Debugging
  	  }else{
  		  $con['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Final
  	  }
  	}
  	catch(PDOException $e) {
      $status = False;
      $con['handler'] = NULL;
  		$message = "* Error al conectar a la base de datos: ".$e->getMessage()."<br />";
      self::alert($message);
  	}

    return $con;
	}
	public function connect(){
		$this->con = $this->Qconnect(
			$this->host,$this->database,$this->user,$this->password,$this->debugging,$this->type,$this->path
			);
		return $this->con;
  }
}
class SQL{
	public $con = NULL;
	public $debugging = False;
	public $status = True;
	public $q = NULL;
	public $table = "";
	public $message = "";

	public $FIELDS = [];
	public $datos = array();

	public $QUERY = "";
	public $SELECT_query = "";
	public $SELECT_binds = array();

	public $indexFirst = True;
	public $time = 0;
	public $memory = 0;
	public $m = 0;
	public $n = 0;
	public $isEmpty = True;

	public function __construct($con,$table){
		global $debugging;
		if(!isset($debugging)){$debugging = False;}

		$this->debugging = $debugging;
		$this->con = $con;
		$this->table = $table;
	}
	public static function alert($text, $debugging = False){
		if($text!=""){
			if($debugging){echo "<script>alert('BackEnd: $text')</script>";}
			echo "BackEnd: $text </br>";
		}
  }
	public function error($text, $e=NULL){
		$debugging = &$this->debugging;
		$hidden = "";
		if( (!is_null($e)) and ($debugging) ){$hidden = $e->getMessage();}
		$this->message = "$text $hidden";
		self::alert( $text, $debugging );
	}
	public static function mask($data,$mask){
		$associative = is_assoc($data);

		if($associative){
			$masked = array();
			foreach($data as $key => $value){
				if(in_array($key,$mask)){
					$masked[$key] = $value;
				}
			}
		}else{
			$masked = [];
			foreach($data as $key){
				if(in_array($key,$mask)){
					$masked[] = $key;
				}
			}
		}

		return $masked;
	}
	public static function binding(&$binds,$data,$text = NULL,$init = 0){
		$associative = is_assoc($data);
		$nulltext = is_null($text);

		if($associative){
			$c = $init;
			foreach($data as $key => $value){
				  $name = ($nulltext) ? ":$key" : ":$text$c";
					$binds[$name] = $value;
					$c++;
			}
		}else{
			$c = $init;
			$text = ($nulltext) ? "val" : $text;
			foreach($data as $key){
					$binds[":$text$c"] = $key;
					$c++;
			}
		}
		return $binds;
	}
	public static function implode($binds,$text=", "){
		$keys = array_keys($binds);
		return implode($text,$keys);
	}
	public function buildQuery(){
		$query = &$this->QUERY;
		$query = "";

		$query .= $this->SELECT_query;
		return $query;
	}
	public function buildBinder(&$q){
		$select = &$this->SELECT_binds;
		self::bindValues($q,$select);
		return $q;
	}
	public function macroBinds(){
		return array();
	}
	public static function bindValues(&$q,$binds){
		foreach($binds as $key => $value){
			$q['handle']->bindValue($key,$value);
		}
		return $q;
	}

	public function DESCRIBE(){
		$con = &$this->con;
		$table = &$this->table;
		$table_fields = [];
		try{
			$q = $con['handler']->prepare("DESCRIBE $table ");
			$q->execute();
			$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
		} catch(Exception $e){
			$this->error("* Error en DESCRIBE $table.", $e);
		}
		$this->FIELDS = $table_fields;
		return $table_fields;
	}
	public function SELECT($fields = NULL, $mask = NULL){
		$table = &$this->table;
		$query = &$this->SELECT_query;

		$query = "";
		if(is_null($fields)){
			$selection = "*";
			$this->FIELDS = $this->DESCRIBE();
		}else{
			$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
			$fields = self::mask($fields,$mask);
			$selection = implode(", ",$fields);
			$this->FIELDS = $fields;
		}
		$query = "SELECT $selection FROM $table ";
		return $query;
	}
	public function EXECUTE(&$q = NULL){
		# Reference $q properly
		if(is_null($q)){$q = &$this->q;}
		$con = &$this->con;
		$datos = &$this->datos;
		$datos = array();
		$fields = &$this->FIELDS;
		$this->status = True;
		# Query statistics
		$startT = microtime(true); // Measure time
		$startM = memory_get_usage(); // Measure memory
		try{
			# Execution and handling
			$this->buildQuery();
			$q['handle'] = $con['handler']->prepare($this->QUERY);
			$q['handle']->setFetchMode(PDO::FETCH_ASSOC);
			$q['handle']->execute( $this->macroBinds() ); //$this->buildBinder($q);
			# Turn data into Array
			$c = 0;
			while( $row=$q['handle']->fetch() ) {
				foreach($fields as $f){
					if($this->indexFirst){
						$datos[$c][$f] = $row[$f];
						}else{
						$datos[$f][$c] = $row[$f];
					}
				}
				$c++;
			}
		}catch (Exception $e){
			$this->status = False;
			$this->error("* Error en EXECUTE.", $e);
		}
		# Query statistics
		$this->time = microtime(true) - $startT;
		$this->memory = (memory_get_usage() - $startM)/10000000;
		$this->m = $c;
		$this->n = sizeof($fields);
		$this->isEmpty = ($c==0) ? True : False;

		return $datos;
	}
}

$con = SQL_Connection::Qconnect("mkti.mx","ventallanta","grupollancarsa","Grupollancarsa22",$debugging);
$PRODUCTOS = new SQL($con,"productos");
$PRODUCTOS->SELECT( ["alto","ancho","rin"] );
print_r($PRODUCTOS->EXECUTE());

?>
