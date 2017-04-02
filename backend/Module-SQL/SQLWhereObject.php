<?php
class SQLWhereObject{
  public $query = "";
  public $queryElements = [];
  public $binds = array();
  protected $WhereCounter = 0;
  protected $validSymbols = ["=",">",">=","<","<=","IN","LIKE"];

  public function __construct(array $assocWhere = NULL, $symbol = "="){
    if( !is_null($assocWhere) ){
      $this->buildWhere($assocWhere, $symbol);
    }
  }
  public function buildWhere( array $assocWhere, $symbol = "=" ){
    foreach($assocWhere as $key => $value){
      $symbol = $this->checkForINSymbol($value,$symbol);
      $symbol = $this->forceValidSymbol($symbol);
      $value = SQLBasicTableManager::inputAsArray($value);

      $bindedNamesArray = $this->bindAndCountWheres($value);

      $bindedNamesString = implode(", ", $bindedNamesArray);
      $this->queryElements[] = "$key $symbol ( $bindedNamesString )";
    }
  }
  protected function bindAndCountWheres($value){
    $bindedNamesArray = [];
    foreach($value as $v){
      $bindName = ":where$this->WhereCounter";
      $this->binds[$bindName] = $v;
      $bindedNamesArray[] = $bindName;
      $this->WhereCounter++;
    }
    return $bindedNamesArray;
  }

  # Symbol Whitelisting Methods
  public function checkForINSymbol($value,$symbol){
    if( is_array($value) ){
      $symbol = ( sizeof($value)<=1 ) ? $symbol : "IN";
    }
    return $symbol;
  }
  public function forceValidSymbol($foreignSymbol){
    $symbol = ( $this->isValidSymbol($foreignSymbol) ) ? $foreignSymbol : "=";
    return $symbol;
  }
  public function isValidSymbol($foreignSymbol){
    return in_array($foreignSymbol,$this->validSymbols);
  }

  # Query retriever methods
  public function get(){
    if( $this->hasQueryElements() ){
      $this->query = "WHERE " . implode(" AND ", $this->queryElements ) . " ";
    }
    return $this->query;
  }
  protected function hasQueryElements(){
    return ( sizeof($this->queryElements)!=0 );
  }

  public function clear(){
    $this->query = "";
    $this->queryElements = [];
    $this->binds = array();
    $this->WhereCounter = 0;
  }
}
