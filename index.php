<?php
$debugging = True;
if($debugging){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}

function is_assoc(array $array){
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
}
function str_has($word,$text){
	if (strpos($text, $word) !== false) {
	  return True;
		}else{
		return False;
	}
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
	public $q = NULL;
	public $table = "";
	public $status = True;
	public $message = "";

	public $FIELDS = NULL;
	public $SELECTFIELDS = [];
	public $IDFIELD = NULL;

	public $datos = array();

	public $QUERY = "";
	public $SELECT_query = "";
	public $SELECT_binds = array();
	public $WHERE_query = "";
	public $WHERE_binds = array();
	public $WHERE_count = 0;
	public $INSERT_query = "";
	public $INSERT_binds = array();
	public $GROUPBY_query = "";

	public $last = 0;
	public $indexFirst = True;
	public $time = 0;
	public $memory = 0;
	public $m = 0;
	public $n = 0;
	public $isEmpty = True;

	public function __construct($con,$table,$data=NULL){
		global $debugging;
		if(!isset($debugging)){$debugging = False;}
		if(!is_null($data)){$this->setData($data);}

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
	public static function multimask($data,$mask){
		$c = 0;
		$r = array();
		foreach($data as $row){
			foreach($mask as $key){
				$r[$c][$key] = $data[$c][$key];
			}
			$c++;
		}
		return $r;
	}
	public static function binding(&$binds,$data,$text = NULL,$posttext="",$init = 0){
		$associative = is_assoc($data);
		$nulltext = is_null($text);

		if($associative){
			$c = $init;
			foreach($data as $key => $value){
				  $name = ($nulltext) ? ":$key$posttext" : ":$text$c";
					if(!is_array($value)){
						$binds[$name] = $value;
					}else{
						$i = 0;
						foreach($value as $v){
							$binds[$name."_$i"] = $v;
							$i++;
						}
					}
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
	public static function multibinding(&$binds,$data,$text = NULL,$posttext="",$init = 0){
		$c = $init;
		$text = (is_null($text)) ? "VAL_" : $text;
		foreach($data as $row){
			foreach($row as $key => $value){
				$binds["$text$key$c$posttext"] = $value;
			}
			$c++;
		}
		return $binds;
	}
	public function buildQuery(){
		$query = &$this->QUERY;
		$query = "";

		$query .= $this->SELECT_query;
		$query .= $this->WHERE_query;
		$query .= $this->GROUPBY_query;
		$query .= ";";

		return $query;
	}
	public function macroBinds(){
		return $this->WHERE_binds;
	}
	public function buildBinder(&$q){
		$where = &$this->WHERE_binds;
		self::bindValues($q,$where);
		return $q;
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

		if(is_null($this->FIELDS)){
			try{
				$q = $con['handler']->prepare("DESCRIBE $table ");
				$q->execute();
				$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
				$this->FIELDS = $table_fields;
				}catch(Exception $e){
				$this->error("* Error en DESCRIBE $table.", $e);
			}
		}else{
			$table_fields = $this->FIELDS;
		}

		return $table_fields;
	}
	public function DESCRIBEID(){
		$fields = $this->DESCRIBE();
		$this->IDFIELD = $fields[0];
		return $this->IDFIELD;
	}

	public function INSERT($data, $mask = NULL,$text = "insert_", $posttext=""){
		# Reference variables
		$table = &$this->table;
		$query = &$this->INSERT_query;
		$binds = &$this->INSERT_binds;
		$con = &$this->con;
		$q = &$this->q;
		$this->status = True;
		$last = &$this->last;

		# Mask data depending on Input rows to insert
		$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
		$multi = (isset($data[0])) ? True : False;
		$fields = ($multi) ? array_keys($data[0]) : array_keys($data);
		if($multi){$datos = &$data;}else{$datos[0] = &$data;}

		# Apply mask to fields to be inserted and generate string
		$fields = self::mask($fields,$mask);
		$fields_text = implode(", ",$fields);

		# Mask and then bind data to be inserted (multidimensional)
		$datos = self::multimask($datos,$fields);
		self::multibinding($binds,$datos,$text,$posttext);

		# Turn binded data (multidimensional) into query
		$query = "INSERT INTO $table ( $fields_text ) VALUES ";
		$rows_array = [];$c = 0;
		foreach($datos as $row){
			$values_array = [];
			foreach($fields as $key){$values_array[] = ":$text$key$c$posttext";}
			$rows_array[] = "( ".implode(", ",$values_array)." )";
			$c++;
		}
		$query .= implode(", ",$rows_array).";";

		# Try to execute
		try{
			$q['handle'] = $con['handler']->prepare($query);
			$q['handle']->execute( $binds );
			$last = $con['handler']->lastInsertId();
			}catch(PDOException $e){
			$this->status = False;
			$last = 0;
			$this->error("* Error en INSERT.", $e);
		}

		return $query;
	}
	public function SELECT($fields = NULL, $mask = NULL){
		$table = &$this->table;
		$query = &$this->SELECT_query;

		$query = "";
		if(is_null($fields)){
			$selection = "*";
			$this->SELECTFIELDS = $this->DESCRIBE();
		}else{
			$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
			$fields = self::mask($fields,$mask);
			$selection = implode(", ",$fields);
			$this->SELECTFIELDS = $fields;
		}
		$query = "SELECT $selection FROM $table ";
		return $query;
	}
	public function DISTINCT($fields = NULL, $mask = NULL){
		$this->SELECT($fields, $mask);
		$query = &$this->SELECT_query;
		$query = str_replace("SELECT", "SELECT DISTINCT" ,$query);
		return $query;
	}

	public function OPERATION($OPERATION = "COUNT",$fields = NULL, $mask = NULL, $isDistinc = False){
		$table = &$this->table;
		$query = &$this->SELECT_query;

		$validOperations = ["COUNT","SUM","AVG","MAX","MIN","LENGTH","UPPER","LOWER"];
		$OPERATION = (in_array($OPERATION,$validOperations)) ? $OPERATION : "COUNT";

		$ALL = is_null($fields);
		if(!$ALL){
			$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
			$fields = self::mask($fields,$mask);
		}

		$fields = ($ALL) ? ["*"] : $fields;
		$isDistinc = ($ALL) ? False : $isDistinc;

		$OPERATION_query = [];
		$distinct = ($isDistinc) ? "DISTINCT" : "";
		foreach($fields as $key){
			$AS = ($ALL) ? "$OPERATION" : "$key"."_$distinct$OPERATION";
			$this->SELECTFIELDS[] = $AS;
			$OPERATION_query[] = "$OPERATION( $distinct $key ) AS $AS";
		}
		$OPERATION_query = implode(", ",$OPERATION_query);

		if($query==""){
			$query = "SELECT $OPERATION_query FROM $table ";
		}else{
			$query = str_replace("FROM", ", $OPERATION_query FROM" ,$query);
		}

		return $query;
	}
	public function COUNT($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("COUNT",$fields,$mask,$isDistinc);
	}
	public function SUM($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("SUM",$fields,$mask,$isDistinc);
	}
	public function AVG($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("AVG",$fields,$mask,$isDistinc);
	}
	public function MAX($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("MAX",$fields,$mask,$isDistinc);
	}
	public function MIN($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("MIN",$fields,$mask,$isDistinc);
	}
	public function LENGTH($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("LENGTH",$fields,$mask,$isDistinc);
	}
	public function UPPER($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("UPPER",$fields,$mask,$isDistinc);
	}
	public function LOWER($fields = NULL, $mask = NULL, $isDistinc = False){
		return $this->OPERATION("LOWER",$fields,$mask,$isDistinc);
	}

	public function WHERE($where, $mask = NULL, $symbol = "="){
		$query = &$this->WHERE_query;
		$binds = &$this->WHERE_binds;
		$count = &$this->WHERE_count;
		$binds = ($query=="") ? array() : $binds;
		$count = ($query=="") ? 0 : $count;
		$query = ($query=="") ? "WHERE" : $query;

		$validSymbols = ["=","<",">","<=",">=","LIKE","IN","BETWEEN"];
		$symbol = ( in_array($symbol,$validSymbols) ) ? $symbol : "=";
		$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
		$where = self::mask($where,$mask);

		if( sizeof($where)>0 ){
			$posttext = "_where$count";
			self::binding($binds,$where,NULL,$posttext);
			$keys = array_keys($where);$keystext = "";
			$AND = ( $query=="WHERE" ) ? "" : "AND";
			foreach($keys as $key){
				$keystext .= "$AND $key $symbol ";

				if(!is_array($where[$key])){
					$keystext .= ":$key$posttext ";
				}else{
					$binded = []; $i = 0;
					foreach($where[$key] as $v){$binded[] = ":$key$posttext"."_$i"; $i++;}
					$keystext .= "( " . implode(", ",$binded) . " ) ";
				}

				$AND = "AND";
			}
			$query .= $keystext;
		}
		$count++;
		return $query;
	}
	public function BETWEEN($where,$mask=NULL,$inclusive=True){
		if(is_bool($mask)){$inclusive = $mask;$mask = NULL;}
		$lower = array(); $upper = array();
		foreach($where as $key => $value){
			$lower[$key] = $value[0];
			$upper[$key] = $value[1];
		}
		$symbol = ($inclusive) ? "<=" : "<";
		$this->WHERE($lower,$mask,">=");
		return $this->WHERE($upper,$mask,$symbol);
	}
	public function GREATER($where,$mask=NULL,$inclusive=True){
		if(is_bool($mask)){$inclusive = $mask;$mask = NULL;}
		$symbol = ($inclusive) ? ">=" : ">";
		return $this->WHERE($where,$mask,$symbol);
	}
	public function LESSER($where,$mask=NULL,$inclusive=True){
		if(is_bool($mask)){$inclusive = $mask;$mask = NULL;}
		$symbol = ($inclusive) ? "<=" : "<";
		return $this->WHERE($where,$mask,$symbol);
	}
	public function IN($where,$mask=NULL){
		return $this->WHERE($where,$mask,"IN");
	}
	public function WHEREID($id){
		$id = (int) $id;
		$field = $this->DESCRIBEID();
		$this->WHERE( array( $field => $id), [$field] );
		return $this->EXECUTE();
	}

	public function GROUPBY($fields = NULL, $mask = NULL){
		$query = &$this->GROUPBY_query;
		$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
		$fields = self::mask($fields,$mask);
		$query = [];
		foreach($fields as $key){$query[] = $key;}
		$query = implode(", ",$query);
		$query = "GROUP BY $query ";
		return $query;
	}

	public function EXECUTE(&$q = NULL){
		# Reference $q properly
		if(is_null($q)){$q = &$this->q;}
		$con = &$this->con;
		$this->status = True;
		$datos = &$this->datos;
		$datos = array();
		$fields = &$this->SELECTFIELDS;

		# Query statistics
		$startT = microtime(true); // Measure time
		$startM = memory_get_usage(); // Measure memory
		if($this->SELECT_query==""){$this->SELECT();}
		try{
			# Execution and handling
			$this->buildQuery();
			$macroBinds = $this->macroBinds();
			$q['handle'] = $con['handler']->prepare($this->QUERY);
			$q['handle']->setFetchMode(PDO::FETCH_ASSOC);
			//$this->buildBinder($q);
			$q['handle']->execute( $macroBinds );
			//echo $this->QUERY;print_r($macroBinds);
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
	public function setData($data){
		if(is_array($data)){
			$this->datos = $data;
			}else{
			$this->datos = json_decode($data);
		}
	}
	public function json($show=True,$isJSON=True){
		$datos['status'] = $this->status;
		$datos['message'] = $this->message;
		$datos["data"] = $this->datos;
		if($isJSON){header('Content-Type: application/json');}
		$json = json_encode($datos, JSON_PRETTY_PRINT );
		if($show){echo $json;}
		return $json;
	}
	public function show($show=True){
		$datos = &$this->datos;
		$IF = &$this->indexFirst;
		if($IF){
			$lines = array_keys($datos);
			$fields = array_keys($datos[$lines[0]]);
			}else{
			$fields = array_keys($datos);
			$lines = array_keys($datos[$lines[0]]);
		}

		$table = "";
		$table .= '<table class="phptable">';

			$table .= '<tr style="background-color: black; color: white;">';
			foreach($fields as $f){$table .="<td>$f</td>";}
			$table .= "</tr>";

				foreach($lines as $c){
					$table .= "<tr>";
					foreach($fields as $f){
						$value = ($IF) ? $datos[$c][$f] : $datos[$f][$c];
						$table .="<td>".$value."</td>";
					}
					$table .= "</tr>";
				}

		$table .= "</table>";

		if($show){echo $table;}
		return $table;
	}
}

$con = SQL_Connection::Qconnect("mkti.mx","ventallanta","grupollancarsa","Grupollancarsa22",$debugging);
$PRUEBA = new SQL($con,"prueba");

$PRUEBA->SELECT( ["nombre","activo"] );
$PRUEBA->AVG(["numero"]);
$PRUEBA->GROUPBY( ["nombre","activo"] );
//$PRUEBA->MAX( ["numero"],NULL );
//$PRUEBA->AVG( ["activo"] );
$PRUEBA->WHERE( array("nombre"=>"Luis") );
$PRUEBA->GREATER( array("id_prueba"=>5) );
$PRUEBA->EXECUTE();
echo $PRUEBA->QUERY;
$PRUEBA->show();
?>
