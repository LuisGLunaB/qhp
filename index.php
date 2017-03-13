<?php
include_once("./SQLModule.php");

$SQLConnection = new SQLConnector("mkti.mx","ricardovertiz","ricardovertiz","ricardovertiz22");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLBasicSelector($con,"blog",["id","titulo"]);
	$PRUEBA->WHEREID( [20,22,25] );
	$PRUEBA->execute();

	print_r($PRUEBA->SELECT_query);
	print_r($PRUEBA->WHERE_query);
	print_r( $PRUEBA->data );
	/*
	$PRUEBA = new SQL($con,"blog");
	$PRUEBA->SELECT( ["id"]);
	$PRUEBA->EXECUTE();
	$PRUEBA->json();*/
}else{
	echo $SQLConnection->message();
}







?>
