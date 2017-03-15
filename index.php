<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLBasicSelector($con,"productos", ["id","modelo","costo"]);
	$PRUEBA->LOWER_EQUAL( array("id"=>10) );
	$PRUEBA->ORDERBY( array("id"=>"DESC") );
	$PRUEBA->execute();

	//$PRUEBA->saveAsTable("back_up");

	//echo $PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);
	/*
	$PRUEBA = new SQL($con,"blog");
	$PRUEBA->SELECT( ["id"]);
	$PRUEBA->EXECUTE();
	$PRUEBA->json();*/
}else{
	echo $SQLConnection->message();
}







?>
