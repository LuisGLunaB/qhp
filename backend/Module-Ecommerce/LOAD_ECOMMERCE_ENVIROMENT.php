<?php
if( ! isset($ROOT) ){ $ROOT=".";}
if( ! defined("ROOT") ){ define("ROOT", $ROOT); }

define( "ECOMMERCE_ROUTE", ("$ROOT/backend/Module-Ecommerce/") );
define( "ECOMMERCE_ROUTE_forms", (ECOMMERCE_ROUTE."forms/") );
define( "ECOMMERCE_ROUTE_processes", (ECOMMERCE_ROUTE."processes/") );
define( "ECOMMERCE_ROUTE_blocks", (ECOMMERCE_ROUTE."blocks/") );

include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
include_once( ECOMMERCE_ROUTE . "ECOMMERCE.php"); //Object

include_once( ROOT . "/UI/TRANSLATIONS.php");

$hasAccessToProtectedSections = False;
$isSuperUser = False;
if( $SQLConnection->status() ){
  $ECOM = new ECOMMERCE();
  $hasAccessToProtectedSections = $ECOM->hasAccessToProtectedSections();
  $isSuperUser = $ECOM->hasSuperAdminLevel();
}else{
  $ECOM = NULL;
}

/* Eviroment: $ECOM, $SQLConnection, $con */
