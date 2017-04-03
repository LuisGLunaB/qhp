<?php
require_once( ROOT . "/backend/Loaders/SET_MODULE_ROUTE.php");

if ( ! defined("MODULE_ROUTE_SQL") ){
  define("MODULE_ROUTE_SQL", MODULE_ROUTE . "Module-SQL/" );
}

require_once( MODULE_ROUTE_SQL . "SQLConnector.php");
require_once( MODULE_ROUTE_SQL . "SQLBasicTableManager.php");
require_once( MODULE_ROUTE_SQL . "SQLBasicSelector.php");
require_once( MODULE_ROUTE_SQL . "SQLWhereObject.php");

//require_once( MODULE_ROUTE_SQL . "SQLUpdate.php");

require_once( MODULE_ROUTE_SQL . "SQLAutomaticLogin.php");
