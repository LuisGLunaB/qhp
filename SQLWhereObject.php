<?php

class SQLWhereObject{
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
