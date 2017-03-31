<?php
class SQLUpdate extends SQLBasicTableManager{
  public $UPDATE_query = "";
  public $SET_query = [];
  public $SET_binds = array();
  public $WHERE_query = "";
  public $WHERE_binds = [];

  public function __construct($TableName, array $fieldsMask = NULL, $con = NULL ){
    parent::__construct($TableName,$fieldsMask,$con);
    $this->UPDATE();
  }

  # Regular UPDATE building methods
  public function UPDATE(){
    $this->UPDATE_query = "UPDATE $this->TableName";
  }
  public function SET($AssocArray){
    $this->maskWithMyFields($AssocArray);
    foreach($AssocArray as $key => $value){
      $this->SET_query[] = "$key = :$key";
      $this->SET_binds[":$key"] = $value;
    }
  }
  public function WHERE($assocWhere,$symbols="="){
    $this->BASICWHERE($assocWhere,$symbols);
  }
  public function WHEREID($ids){
    $ids = self::inputAsArray($ids);

    $this->WHERE_query = "";
    $this->WHERE_binds = [];
    $idfield = $this->getTableId();

    return $this->WHERE( array($idfield=>$ids),"=");
  }

  # Row Activators and Deactivators
  public function SETACTIVEORDEACTIVE($active=True){
    $field = "is_active";
    if( $this->isValidField($field) ){
      $activeOneOrZero = ($active) ? 1 : 0;
      $this->SET( array($field=>$activeOneOrZero) );
    }
  }
  public function SETACTIVE(){
    $this->SETACTIVEORDEACTIVE( True );
  }
  public function SETNOTACTIVE(){
    $this->SETACTIVEORDEACTIVE( False );
  }

  # Plus and Minus Methods
  public function SETPLUSMINUS($Array,$value=1){
    $value = (int) $value;
    $value = ($value>=0) ? "+$value" : "$value";

    $Array = self::inputAsArray($Array);
    $this->maskWithMyFields($Array);
    foreach($Array as $key){
      $this->SET_query[] = "$key = $key$value";
    }
  }
  public function PLUSONE($Array){
    $this->SETPLUSMINUS($Array,1);
  }
  public function MINUSONE($Array){
    $this->SETPLUSMINUS($Array,-1);
  }

  # Execution Methods
  public function execute(){
    return $this->executeFetchId( $this->getQuery(), $this->getBinds() );
  }
  public function getQuery(){
    $SET = implode(", ",$this->SET_query);
    if($this->WHERE_query!=""){
      return "$this->UPDATE_query SET $SET $this->WHERE_query;";
    }else{
      $this->ErrorManager->handleError("No WHERE clause specified on UPDATE $this->TableName." );
    }
  }
  public function getBinds(){
    return array_merge($this->WHERE_binds,$this->SET_binds);
  }

  public function clear(){
    $this->UPDATE_query = "";
    $this->SET_query = "";
    $this->SET_binds = array();
    $this->WHERE_query = "";
    $this->WHERE_binds = [];
  }

}
