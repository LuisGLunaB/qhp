<?php

class SQLBasicSelector extends SQLBasicTableManager{
  public $COMPLETE_query = ""; # Complete query to be executed
  public $TableName = "";

  public $SELECT_query = ""; # Just the first row of the query
  public $commaSeparatedFields = "";

  public $WHERE = NULL; # SQLWhereObject
  public $WHERE_query = "";

  public $ORDERBY_query = "";
  public $maskORDERBY = True;

  public $PAGINATION_query = "";
  protected $LIMIT = 30;
  protected $PAGE = 0;

  public $FREE_query = "";

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
      $this->WHERE = new SQLWhereObject();
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
  public function whereExists(){
    return ( !is_null($this->WHERE) );
  }

  public function ORDERBY( $assocArray ){
    if($this->maskORDERBY){
      $assocArray = self::maskAssocArray( $assocArray, $this->getTableFields() );
    }
    $validOrders = ["ASC","DESC"];
    $orderElements = array();
    foreach($assocArray as $field => $order){
      $order = in_array($order,$validOrders) ? $order : "DESC";
      $orderElements[] = "$field $order";
    }
    $orderElements = implode(", ", $orderElements);
    $this->ORDERBY_query = "ORDER BY $orderElements ";
    return $this->ORDERBY_query;
  }
  public function PAGE( $page=0, $limit=NULL ){
    $this->setLimit($limit);
    $this->setPage($page);
    $OFFSET = ( $this->LIMIT * $this->PAGE );
    $this->PAGINATION_query = "LIMIT $this->LIMIT OFFSET $OFFSET ";
  }
  public function setLimit($limit){
    $limit = ( is_null($limit) ) ? $this->LIMIT : (int) $limit;
    $this->LIMIT = $limit;
  }
  public function setPage($page){
    $page = (int) $page;
    $this->PAGE = $page;
  }
  public function FREE( $query, $binds=[] ){
    $this->COMPLETE_query = $query;
    $this->WHERE->binds = $binds;
    return $this->execute( $this->COMPLETE_query , $this->WHERE->binds );
  }

  public function execute( $query=NULL, $binds=NULL, $buildArray=True ){
    $this->data = array();

    $this->recordStatistics("START");
    $this->tryToExecuteQuery($query, $binds);

    $this->recordStatistics("BUILD");
    if($buildArray){
		    $this->buildArrayFromExecution();
    }

    $this->recordStatistics("STOP");

		return $this->data;
  }
  public function saveAsTable($TableName){
    if( self::isSafeSQLString($TableName) ){
			$query = "
        DROP TABLE IF EXISTS $TableName;
        CREATE TABLE $TableName ( $this->COMPLETE_query ); ";
      $this->execute( $query , $this->getBinds(), $buildArray=False );
		}else{
			$this->ErrorManager->handleError("$TableName is not a valid table name." );
		}
  }
  public function dropTable($TableName){
    if( self::isSafeSQLString($TableName) ){
      $query = "DROP TABLE IF EXISTS $TableName; ";
      $this->execute( $query , [], $buildArray=False );
    }else{
			$this->ErrorManager->handleError("$TableName is not a valid table name." );
		}
  }
  public function truncateTable($TableName){
    if( self::isSafeSQLString($TableName) ){
      $query = "TRUNCATE TABLE $TableName; ";
      $this->execute( $query , [], $buildArray=False );
    }else{
			$this->ErrorManager->handleError("$TableName is not a valid table name." );
		}
  }
  public function saveAsView($ViewName){
    if( self::isSafeSQLString($ViewName) ){
			$query = "CREATE OR REPLACE VIEW $ViewName AS ( $this->COMPLETE_query ) ";
      $this->execute( $query , $this->getBinds(), $buildArray=False );
		}else{
			$this->ErrorManager->handleError("$ViewName is not a valid view name." );
		}
	}
  public function dropView($ViewName){
    if( self::isSafeSQLString($ViewName) ){
      $query = "DROP VIEW IF EXISTS $ViewName; ";
      $this->execute( $query , [], $buildArray=False );
    }else{
			$this->ErrorManager->handleError("$ViewName is not a valid view name." );
		}
  }

  protected function tryToExecuteQuery( $query=NULL, $binds=NULL ){
    $query = is_null($query) ? $this->getQuery() : $query;
    $binds = is_null($binds) ? $this->getBinds() : $binds;
    try{
			$this->Query = $this->con['handler']->prepare( $query );
			$this->Query->setFetchMode(PDO::FETCH_ASSOC);
			$this->Query->execute( $binds );
    }catch (Exception $e){
      $this->ErrorManager->handleError("Error in SELECT $this->TableName.", $e );
		}
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
  public function getQuery(){
    $this->COMPLETE_query =
      $this->SELECT_query . " " .
      $this->WHERE_query . " " .
      $this->ORDERBY_query . " ".
      $this->PAGINATION_query;
    return $this->COMPLETE_query;
  }
  public function getRawQuery(){
    return self::multi_str_replace( $this->getBinds(), $this->COMPLETE_query );
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
