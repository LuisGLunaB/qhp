<?php
$debugging = True;
if($debugging){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	}else{
	error_reporting(0);
	ini_set('display_errors', 0);
}

include_once("./SQLModule.php");

$SQLConnection = new SQLConnector("mkti.mx","ricardovertiz","ricardovertiz","");
$con = $SQLConnection->getConnector();

$PRUEBA = new SQL($con,"blog");
$PRUEBA->SELECT( ["id"]);

$PRUEBA->EXECUTE();

$PRUEBA->json();


?>
