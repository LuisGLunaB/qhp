<?php
require_once( ROOT . "/backend/Loaders/SET_MODULE_ROUTE.php");

if ( ! defined("MODULE_ROUTE_Facebook") ){
  define("MODULE_ROUTE_Facebook", MODULE_ROUTE . "Module-Facebook/" );
}

// require_once( MODULE_ROUTE_SQL . "FBO.php");
require_once( MODULE_ROUTE_Facebook . "FacebookObject.php");
