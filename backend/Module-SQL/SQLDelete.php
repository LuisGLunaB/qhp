<?php
class SQLDelete extends SQLBasicTableManager{
  public $DELETE_query = "";
  public $WHERE_query = "";
  public $WHERE_binds = [];

  public function __construct($con, $TableName, array $fieldsMask = NULL ){
    parent::__construct($con,$TableName,$fieldsMask);
    $this->DELETE();
  }

  # Regular UPDATE building methods
  public function DELETE(){
    $this->DELETE_query = "DELETE FROM $this->TableName";
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

  # Execution Methods
  public function execute(){
    return $this->executeFetchId( $this->getQuery(), $this->getBinds() );
  }
  public function getQuery(){
    if($this->WHERE_query != ""){
      return "$this->DELETE_query $this->WHERE_query;";
    }else{
      $this->ErrorManager->handleError("No WHERE clause specified on DELETE $this->TableName." );
    }
  }
  public function getBinds(){
    return $this->WHERE_binds;
  }

  public function clear(){
    $this->DELETE_query = "";
    $this->WHERE_query = "";
    $this->WHERE_binds = [];
  }

}
