<?php
if( ! defined("MODULE_ROUTE") ){
  define("MODULE_ROUTE", ROOT."/backend/" );
  //define("MODULE_ROUTE", "$_SERVER[DOCUMENT_ROOT]/backend/" );
  if( ! defined("CONFIGURATIONS_ROUTE") ){
    define("CONFIGURATIONS_ROUTE", MODULE_ROUTE . "Configurations/" );
  }
  require_once( CONFIGURATIONS_ROUTE . "macrovariables.php" );
  require_once( CONFIGURATIONS_ROUTE . "debugging.php" );
  require_once( MODULE_ROUTE . "Loaders/LOADMODULE_Functionals.php" );
}
