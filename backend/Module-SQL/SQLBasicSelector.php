<?php

class SQLBasicSelector extends SQLBasicTableManager{
  public $COMPLETE_query = ""; # Complete query to be executed
  public $TableName = "";

  public $SELECT_query = ""; # Just the first row of the query
  public $commaSeparatedFields = "";

  public $MATCH_query = "";

  public $WHERE = NULL; # SQLWhereObject
  public $WHERE_query = "";

  public $ORDERBY_query = "";
  public $maskORDERBY = True;

  public $PAGINATION_query = "";
  protected $LIMIT = 30;
  protected $PAGE = 0;

  public $FREE_query = "";
  public $FREE_binds = array();
  public $isFreeMode = False;

  public $OPERATIONS_query = "";
  public $GROUPBY_query = "";

  public $data = array();

  # Performance Statistics
  public $DataSize = 0; # in MB
  public $ExecutionTime = 0; # in Seconds
  public $BuildTime = 0; # in Seconds
  public $TotalTime = 0; # in Seconds

  public function __construct($TableName, array $fieldsMask = NULL, $con=NULL){
    parent::__construct($TableName,$fieldsMask,$con);
    $this->buildSELECT();
  }

  # Methods about $SELECT_query
  public function buildSELECT(){
    $this->getCommaSeparatedFields();
    $this->SELECT();
  }
  public function getCommaSeparatedFields(){
    $this->maskFields();
    $this->commaSeparatedFields = implode(", ", $this->maskedFields );
    return $this->commaSeparatedFields;
  }
  public function SELECT(){
    if( $this->isFieldsMaskOn() ){
      $this->SELECT_query = "SELECT $this->commaSeparatedFields $this->MATCH_query FROM $this->TableName ";
    }else{
      $this->SELECT_query = "SELECT * $this->MATCH_query FROM $this->TableName ";
    }
    return $this->SELECT_query;
  }

  public function SEARCH($words,$fields,$precision=2){
    if( is_array($words) ){$words = implode(" ",$words);}
    $words = self::simpleStringWhiteListing($words);

    $this->maskWithMyFields($fields);
    $fieldsString = implode(", ",$fields);
    switch ($precision) {
        case 1: $MODE = "IN BOOLEAN MODE";break;
        case 2: $MODE = "IN NATURAL LANGUAGE MODE";break;
        case 3: $MODE = "WITH QUERY EXPANSION";break;
        case 4: $MODE = "IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION";break;
        default: $MODE = "IN NATURAL LANGUAGE MODE";
    }

    $this->MATCH_query = ", MATCH ( $fieldsString ) AGAINST ('$words' $MODE ) as search_relevance ";
    $this->ORDERBYRELEVANCE();
  }

  # Methods about $WHERE_query
  public function WHERE( array $assocWhere, $fieldsMask = NULL, $symbol = "=" ){
    /* This method is not refactored in order to keep all the coupling to
    the WHEREObject in a single place. */

    # Create WHERE Object if necessary
    if( is_null($this->WHERE) ){ $this->WHERE = new SQLWhereObject(); }

    # Mask Where with valid Table Fields

    if( is_null($fieldsMask) ){
      $fieldsMask = $this->getTableFields();
    }else{
      $fieldsMask = $this->maskWithMyFields($fieldsMask);
    }
    $fieldsMask[] = "search_relevance";
    $maskedAssocWhere = self::maskAssocArray($assocWhere, $fieldsMask );

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
  public function LIKE( array $assocWhere, $fieldsMask = NULL ){
    return $this->WHERE( $assocWhere, $fieldsMask, $symbol="LIKE");
  }
  public function WHEREID( $id ){
    return $this->WHERE(
      $assocWhere = array( $this->getTableId() => $id),
      $fieldsMask = [ $this->getTableId() ]
    );
  }
  public function whereExists(){
    return ( !is_null($this->WHERE) );
  }

  public function TOTAL(){
    $query = "SELECT COUNT(*) AS count FROM $this->TableName ";
    $binds = [];

    if( $this->whereExists() ){
      $query .= $this->WHERE->get().";";
      $binds = $this->WHERE->binds;
    }

    $this->executeFetchTable( $query , $binds );
    $count = $this->fetchTable();
    return $count[0]["count"];
  }

  # Methods about $ORDERBY_query
  public function ORDERBY( $assocArray ){
    if($this->maskORDERBY){
      $Allfields = $this->getTableFields();
      $Allfields[] = "search_relevance";
      $assocArray = self::maskAssocArray( $assocArray, $Allfields );
    }
    $orderElementsArray = $this->buildOrderByArray($assocArray);
    $orderElementsString = implode(", ", $orderElementsArray);
    $this->ORDERBY_query = "ORDER BY $orderElementsString ";
    return $this->ORDERBY_query;
  }
  public function ORDERBYRELEVANCE(){
    $this->ORDERBY( array("search_relevance"=>"DESC") );
  }

  public function buildOrderByArray($assocArray){
    $validOrders = ["ASC","DESC"];
    $orderElementsArray = array();
    foreach($assocArray as $field => $order){
      $order = in_array($order,$validOrders) ? $order : "DESC";
      $orderElementsArray[] = "$field $order";
    }
    return $orderElementsArray;
  }

  # Methods about $PAGINATION_query
  public function PAGE( $page=0, $limit=NULL ){
    $this->setLimit($limit);
    $this->setPage($page);
    $OFFSET = ( $this->LIMIT * $this->PAGE );
    $this->PAGINATION_query = "LIMIT $this->LIMIT OFFSET $OFFSET ";
    return $this->PAGINATION_query;
  }
  public function setLimit($limit){
    $limit = ( is_null($limit) ) ? $this->LIMIT : (int) $limit;
    $this->LIMIT = $limit;
  }
  public function setPage($page){
    $page = (int) $page;
    $this->PAGE = $page;
  }

  # Main Execution and Fetching Methods
  public function execute($query=NULL,$binds=NULL){
    $query = (is_null($query)) ? $this->getQuery() : $query;
    $binds = (is_null($binds)) ? $this->getBinds(): $binds;

    $this->recordStatistics("START");
    $this->executeFetchTable( $query , $binds );

    $this->recordStatistics("BUILD");
		$this->data = $this->fetchTable();

    $this->recordStatistics("STOP");
		return $this->data;
  }

  public function getQuery(){
    if( !$this->isFreeMode ){
      $this->COMPLETE_query =
          $this->SELECT() . " " .
          $this->WHERE_query . " " .
          $this->GROUPBY_query . " " .
          $this->ORDERBY_query;

      $this->COMPLETE_query = ($this->MATCH_query=="") ? $this->COMPLETE_query :
        "SELECT * FROM ( $this->COMPLETE_query ) AS $this->TableName WHERE search_relevance > 0 ";
      $this->COMPLETE_query .= " $this->PAGINATION_query;";

      $query = $this->COMPLETE_query;

    }else{
      $query = $this->FREE_query;
    }
    return $query;
  }
  public function getBinds(){
    $binds = [];
    if( !$this->isFreeMode ){
      if( $this->whereExists() ){
        $binds = $this->WHERE->binds;
      }
    }else{
      $binds = $this->FREE_binds;
    }
    return $binds;
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

  # Free Execution and Fetching Methods (SQLInjection UNSAFE)
  public function free( $query, $binds=[] ){
    return $this->executeFree($query, $binds);
  }
  public function CALL( $functionName, $argumentsArray ){
    return NULL;
  }
  public function CALLDATA( $procedureName, $argumentsArray ){
    return NULL;
  }

  public function executeFree( $query, $binds=[] ){
    $this->enterFreeMode($query, $binds);
    return $this->execute( $query, $binds );
  }
  public function enterFreeMode( $query, $binds){
    $this->FREE_query = $query;
    $this->FREE_binds = $binds;
    $this->isFreeMode = True;
  }
  public function exitFreeMode(){
    $this->FREE_query = "";
    $this->FREE_binds = array();
    $this->isFreeMode = False;
  }

  # Table Operations Methods
  public function saveAsTable($TableName){
    $TableName = strtolower($TableName);
    if( self::isSafeSQLString($TableName) ){
      $DATA_query = $this->getQuery();
			$query =
        "DROP TABLE IF EXISTS $TableName;
        CREATE TABLE $TableName ( $DATA_query ); ";
      $this->executeQueryWithBinds( $query , $this->getBinds() );
		}else{
			$this->ErrorManager->handleError("$TableName is not a valid table name." );
		}
  }
  public function dropTable($TableName){
    $TableName = strtolower($TableName);
    if( self::isSafeSQLString($TableName) ){
      $query = "DROP TABLE IF EXISTS $TableName; ";
      $this->executeQuery( $query );
    }else{
			$this->ErrorManager->handleError("$TableName is not a valid table name." );
		}
  }
  public function truncateTable($TableName){
    $TableName = strtolower($TableName);
    if( self::isSafeSQLString($TableName) ){
      if( $this->isValidForeignTable($TableName) ){
        $query = "TRUNCATE TABLE $TableName; ";
        $this->executeQuery( $query );
      }
    }else{
			$this->ErrorManager->handleError("$TableName is not a valid table name." );
		}
  }
  # View Operations Methods
  public function saveAsView($ViewName){
    $ViewName = strtolower($ViewName);
    if( self::isSafeSQLString($ViewName) ){
      $DATA_query = $this->getQuery();
			$query = "CREATE OR REPLACE VIEW $ViewName AS ( $DATA_query ) ";
      $this->executeQueryWithBinds( $query , $this->getBinds() );
		}else{
			$this->ErrorManager->handleError("$ViewName is not a valid view name." );
		}
	}
  public function dropView($ViewName){
    $ViewName = strtolower($ViewName);
    if( self::isSafeSQLString($ViewName) ){
      $query = "DROP VIEW IF EXISTS $ViewName; ";
      $this->executeQuery( $query );
    }else{
			$this->ErrorManager->handleError("$ViewName is not a valid view name." );
		}
  }

  # Property Retrieving Methods
  public function getRawQuery(){
    return self::multi_str_replace( $this->getBinds(), $this->getQuery() );
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
