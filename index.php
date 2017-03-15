<?php
include_once("./SQLModule.php");

$SQLConnection = new SQLConnector("mkti.mx","ricardovertiz","ricardovertiz","ricardovertiz22");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLBasicSelector($con,"prueba");
	//$PRUEBA->WHEREID( [20,22,25] );
	//$PRUEBA->ORDERBY( array("id"=>"DESC") );
	//$PRUEBA->PAGE( 0 );
	$PRUEBA->execute();

	//$PRUEBA->saveAsTable("prueba");

	echo $PRUEBA->getRawQuery();
	print_r($PRUEBA->data);
	/*
	$PRUEBA = new SQL($con,"blog");
	$PRUEBA->SELECT( ["id"]);
	$PRUEBA->EXECUTE();
	$PRUEBA->json();*/
}else{
	echo $SQLConnection->message();
}







?>
