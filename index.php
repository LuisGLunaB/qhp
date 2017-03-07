<?php
include_once("./SQLModule.php");

$SQLConnection = new SQLConnector("mkti.mx","ricardovertiz","ricardovertiz","ricardovertiz22");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLBasicTableManager($con,"blog", ["id","a","titulo"] );
	print_r($PRUEBA->TableFields);
	print_r($PRUEBA->maskedFields);
	/*
	$PRUEBA = new SQL($con,"blog");
	$PRUEBA->SELECT( ["id"]);
	$PRUEBA->EXECUTE();
	$PRUEBA->json();*/
}else{
	echo $SQLConnection->message();
}






?>
