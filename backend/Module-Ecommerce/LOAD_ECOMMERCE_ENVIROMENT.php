<?php
if( ! isset($ROOT) ){ $ROOT=".";}
if( ! defined("ROOT") ){ define("ROOT", $ROOT); }

define( "ECOMMERCE_ROUTE", ("$ROOT/backend/Module-Ecommerce/") );
define( "ECOMMERCE_ROUTE_forms", (ECOMMERCE_ROUTE."forms/") );
define( "ECOMMERCE_ROUTE_processes", (ECOMMERCE_ROUTE."processes/") );

include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
include_once( ECOMMERCE_ROUTE . "ECOMMERCE.php"); //Object

if( $SQLConnection->status() ){
  $ECOM = new ECOMMERCE();
}else{
  $ECOM = NULL;
}

/* Eviroment: $ECOM, $SQLConnection, $con */
