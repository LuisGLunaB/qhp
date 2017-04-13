<?php

require_once( ROOT . "/backend/Loaders/SET_MODULE_ROUTE.php");

if( ! defined("MODULE_ROUTE_Accounts") ) {
  define("MODULE_ROUTE_Accounts", MODULE_ROUTE . "Module-Accounts/" );
}

require_once( MODULE_ROUTE_Accounts . "UserObject.php");
