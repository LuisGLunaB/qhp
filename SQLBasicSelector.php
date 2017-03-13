<?php

class SQLBasicSelector extends SQLBasicTableManager{
  public $COMPLETE_query = ""; # Complete query to be executed

  public $SELECT_query = ""; # Just the first row of the query
  public $commaSeparatedFields = "";

  public $WHERE = NULL; # WHEREObject
  public $WHERE_query = "";

  public $data = array();

  # Performance Statistics
  public $DataSize = 0; # in MB
  public $ExecutionTime = 0; # in Seconds
  public $BuildTime = 0; # in Seconds
  public $TotalTime = 0; # in Seconds

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    parent::__construct($con,$TableName,$fieldsMask);
    $this->buildSELECT();
  }

  public function buildSELECT(){
    $this->getCommaSeparatedFields();
    $this->SELECT();
  }
  public function SELECT(){
    if( $this->isFieldsMaskOn() ){
      $this->SELECT_query = "SELECT $this->commaSeparatedFields FROM $this->TableName ";
    }else{
      $this->SELECT_query = "SELECT * FROM $this->TableName ";
    }
  }
  public function getCommaSeparatedFields(){
    $this->maskFields();
    $this->commaSeparatedFields = implode(", ", $this->maskedFields );
  }

  public function WHERE( array $assocWhere, $fieldsMask = NULL, $symbol = "=" ){
    /* This method is not refactored in order to keep all the coupling to
    the WHEREObject in a single place. */

    # Create WHERE Object if necessary
    if( is_null($this->WHERE) ){
      $this->WHERE = new WHEREObject();
    }

    # Mask Where with valid Table Fields
    if( is_null($fieldsMask) ){
      $this->getTableFields();
      $maskedAssocWhere = self::maskAssocArray($assocWhere, $this->TableFields);
    }else{
      $maskedAssocWhere = self::maskAssocArray($assocWhere, $fieldsMask );
    }

    # Build Where and return it to this objects as a String.
    $this->WHERE->buildWhere($maskedAssocWhere, $symbol);
    $this->WHERE_query = $this->WHERE->get();
    return $this->WHERE_query;
  }
  public function GREATER( array $assocWhere, $fieldsMask = NULL ){
    return $this->WHERE( $assocWhere, $fieldsMask, $symbol=">");
  }
  public function LOWER( array $assocWhere, $fieldsMask = NULL ){
    return $this->WHERE( $assocWhere, $fieldsMask, $symbol="<");
  }
  public function GREATER_EQUAL( array $assocWhere, $fieldsMask = NULL ){
    return $this->WHERE( $assocWhere, $fieldsMask, $symbol=">=");
  }
  public function LOWER_EQUAL( array $assocWhere, $fieldsMask = NULL ){
    return $this->WHERE( $assocWhere, $fieldsMask, $symbol="<=");
  }
  public function WHEREID( $id ){
    $idField = $this->getTableId();
    return $this->WHERE( array($idField=>$id), [$idField] );
  }
  //public function IN(){
  public function whereExists(){
    return ( !is_null($this->WHERE) );
  }

  public function execute(){
    $this->data = array();

    $this->recordStatistics("START");
    $this->tryToExecuteQuery();

    $this->recordStatistics("BUILD");
		$this->buildArrayFromExecution();

    $this->recordStatistics("STOP");

		return $this->data;
  }

  protected function tryToExecuteQuery(){
    try{
			$this->Query = $this->con['handler']->prepare( $this->getQuery() );
			$this->Query->setFetchMode(PDO::FETCH_ASSOC);
			$this->Query->execute( $this->getBinds() );
    }catch (Exception $e){
      $this->ErrorManager->handleError("Error in SELECT $this->TableName.", $e );
		}
  }
  public function getQuery(){
    $this->COMPLETE_query = $this->SELECT_query . " " . $this->WHERE_query;
    return $this->COMPLETE_query;
  }
  public function getBinds(){
    if( $this->whereExists() ){
      return $this->WHERE->binds;
    }else {
      return array();
    }
  }
  protected function buildArrayFromExecution(){
    if( $this->status() ){
      $row = 0;
      while( $QueryRow=$this->Query->fetch() ){
        $QueryFields = ($row==0) ? array_keys($QueryRow) : $QueryFields;
        foreach($QueryFields as $field){
          $this->data[$row][$field] = $QueryRow[$field];
        } //foreach
        $row++;
      } //while fetching
    } //if status
  }
  protected function recordStatistics($command){
    if($command=="START"){
      $this->ExecutionTime = microtime(true);
    }

    if($command=="BUILD"){
      $this->ExecutionTime = microtime(true) - $this->ExecutionTime ;
      $this->BuildTime = microtime(true);
      $this->DataSize = memory_get_usage();
    }

    if($command=="STOP"){
      $this->BuildTime = microtime(true) - $this->BuildTime ;
  		$this->DataSize = (memory_get_usage() - $this->DataSize )/10000000;
      $this->TotalTime = ($this->ExecutionTime + $this->BuildTime);
    }
  }

  public function hasData(){
    return ( $this->rowSize() > 0 );
  }
  public function rowSize(){
    return sizeof($this->data);
  }
  public function columnSize(){
    return sizeof( $this->getFields() );
  }
  public function getFields(){
    if( $this->hasData() ){
      return array_keys( $this->data[0] );
    }else {
      return [];
    }
  }

  public function getStatistics(){
    $x["DataSize"] = $this->DataSize;
    $x["ExecutionTime"] = $this->ExecutionTime;
    $x["BuildTime"] = $this->BuildTime;
    $x["TotalTime"] = $this->TotalTime;
    return $x;
  }

}

class WHEREObject{
  public $query = "";
  public $queryElements = [];
  public $binds = array();
  protected $WhereCounter = 0;

  public function __construct(array $assocWhere = NULL, $symbol = "="){
    if( !is_null($assocWhere) ){
      $this->buildWhere($assocWhere, $symbol);
    }
  }

  public function buildWhere( array $assocWhere, $symbol = "=" ){
    foreach($assocWhere as $key => $value){
      $symbol = ( is_array($value) ) ? "IN" : $symbol;
      $value = ( is_array($value) ) ? $value : [$value];

      $bindNames = [];
      foreach($value as $v){
        $bindName = ":where$this->WhereCounter";
        $this->binds[$bindName] = $v;
        $bindNames[] = $bindName;
        $this->WhereCounter++;
      }

      $bindNames = implode(", ", $bindNames);
      $this->queryElements[] = "$key $symbol ( $bindNames )";
    }
  }

  public function clearWhere(){
    $this->query = "";
    $this->queryElements = [];
    $this->binds = array();
    $this->WhereCounter = 0;
  }

  protected function hasQueryElements(){
    return ( sizeof($this->queryElements)!=0 );
  }
  public function get(){
    if( $this->hasQueryElements() ){
      $this->query = "WHERE " . implode(" AND ", $this->queryElements ) . " ";
    }
    return $this->query;
  }

}


class SQL{

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

		if($this->SELECT_query==""){$this->SELECT();}

		$query .= $this->SELECT_query;
		$query .= $this->WHERE_query;
		$query .= $this->GROUPBY_query;
		$query .= $this->ORDERBY_query;
		$query .= ($this->PAGE_query=="") ? $this->PAGE() : $this->PAGE_query;
		//$query .= $this->PAGE_query;
		$query .= ";";

		return $query;
	}
	public function buildView(){
		$query = "";

		if($this->SELECT_query==""){$this->SELECT();}

		$query .= $this->SELECT_query;
		$query .= $this->WHERE_query;
		$query .= $this->GROUPBY_query;
		$query .= $this->ORDERBY_query;
		$query .= ";";

		return $query;
	}
	public function buildInto($tabla,$IN = ""){
		$query = "";
		if( self::validKey($tabla) and self::validKey($IN) ){
			$IN = ($IN == "") ? "" : "IN '$IN'";

			if($this->SELECT_query==""){$this->SELECT();}
			$this->SELECT_query = str_replace("FROM","INTO $tabla$IN FROM");

			$query .= $this->SELECT_query;
			$query .= $this->WHERE_query;
			$query .= $this->GROUPBY_query;
			$query .= $this->ORDERBY_query;
			$query .= ";";
		}
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
		$tables = &$this->tables;
		$query = &$this->SELECT_query;

		$tables = [$table];
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

	public function PAGE($page = NULL, $limit = NULL){
		$query = &$this->PAGE_query;

		if(is_null($page)){
			$page = &$this->PAGENUMBER;
		}else{
			$this->PAGENUMBER = (int) $page;
			$page = &$this->PAGENUMBER;
		}

		if(is_null($limit)){
			$limit = &$this->LIMIT;
		}else{
			$this->LIMIT = (int) $limit;
			$limit = &$this->LIMIT;
		}

		$offset = &$this->OFFSET;
		$offset = ($limit * $page);

		$query = "LIMIT $limit OFFSET $offset ";
		return $query;
	}
	public function ORDERBY($data,$mask = NULL){
		$query = &$this->ORDERBY_query;
		$mask = (is_null($mask)) ? $this->DESCRIBE() : $mask;
		$fields = self::mask($data,$mask);

		$validOrders = ["ASC","DESC"];

		$query = [];
		foreach($fields as $field=>$order){
			$order = (in_array($order,$validOrders)) ? $order : "ASC";
			$query[] = "$field $order";
		}
		$query = implode(", ",$query);
		$query = "ORDER BY $query ";

		return $query;
	}

	public function setData($data){
		if(is_array($data)){
			$this->datos = $data;
			}else{
			$this->datos = json_decode($data);
		}
	}

	public function get($all=False){
		if($all){
			$datos['status'] = &$this->status;
			$datos['message'] = &$this->message;
			$datos["data"] = &$this->datos;
		}else{
			$datos = &$this->datos;
		}
		return $datos;
	}
	public function getOne(){
		return $this->datos[0];
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
		if(sizeof($datos)!=0){
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
		}else{
			$table = '</br><div style="font-size:20px; color: red;">Tabla vacía.</div></br>';
		}

		if($show){echo $table;}
		return $table;
	}
}
class pending{
	public static function validKey($key){
		$valid = True;
		$black = ["'",'"',"%","&","=","!","|","¡","/",":",";"];
		$chars = str_split($key);
		foreach($char as $c){
			if(in_array($c,$black)){$valid = False; break;}
		}
		return $valid;
	}

	public static function FREE($con,$query,$binds = array(), $fetch = True, $indexFirst = True){
		$datos["status"] = True;
		$datos["message"] = "";
		$datos["data"] = array();

		try{
			$q['handle'] = $con['handler']->prepare($query);

			if($fetch){
					$q['handle']->setFetchMode(PDO::FETCH_ASSOC);
					$q['handle']->execute( $binds );
					$c = 0;
					while( $row=$q['handle']->fetch() ) {
						foreach($row as $f => $v){
							if($indexFirst){
								$datos["data"][$c][$f] = $v;
								}else{
								$datos["data"][$f][$c] = $v;
							}
						}
						$c++;
					}
			}else{
					$q['handle']->execute( $binds );
			}
		}catch (Exception $e){
			$datos["status"] = False;
			$datos["message"] = "ErrorManager en comando FREE. ".$e->getMessage();
		}

		return $datos;
	}
	public static function NEWVIEW($con,$viewname,$query,$binds = array() ){
		if( self::validKey($viewname) ){
			$viewquery = "CREATE OR REPLACE VIEW $viewname AS $query ";
			$r = self::FREE($con,$viewquery,$binds, $fetch = False);
			return $r['status'];
		}else{
			return False;
		}
	}
	public static function DROPVIEW($con,$viewname){
		if( self::validKey($viewname) ){
			$viewquery = "DROP VIEW IF EXISTS $viewname ";
			$r = self::FREE($con,$viewquery, array() , $fetch = False);
			return $r['status'];
		}else{
			return False;
		}
	}
	public static function INTO($con, $tabla, $query, $binds = array(), $IN = ""){
		if( self::validKey($tabla) and self::validKey($IN) ){
			$IN = ($IN == "") ? "" : "IN '$IN'";
			$query = str_replace("FROM","INTO $tabla$IN FROM");
			$r = self::FREE($con, $query, $binds, $fetch = False);
			return $r['status'];
		}else{
			return False;
		}
	}

	public static function json($datos,$show=True,$isJSON=True){
		if($isJSON){header('Content-Type: application/json');}
		$json = json_encode($datos, JSON_PRETTY_PRINT );
		if($show){echo $json;}
		return $json;
	}
	public static function show($datos,$show=True,$indexFirst = True){
		$datos = ( isset($datos['data']) ) ? $datos['data'] : $datos;

		$IF = $indexFirst;
		if(sizeof($datos)!=0){
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
		}else{
			$table = '</br><div style="font-size:20px; color: red;">Tabla vacía.</div></br>';
		}

		if($show){echo $table;}
		return $table;
	}
}
