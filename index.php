<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLSummarySelector($con,"productos", ["id","rin"]);
	$PRUEBA->UPPERCASE( ["marca"], "");
	$PRUEBA->COUNT( ["pmenudeo"] );
	$PRUEBA->AVG( ["pmenudeo"] );
	$PRUEBA->STD( ["pmenudeo"] );
	$PRUEBA->GROUPBY( ["marca","rin"]);
	//$PRUEBA->PAGE( 0 );
	$PRUEBA->LOWER_EQUAL( array("id"=>100) );
	$PRUEBA->ORDERBY( array("marca"=>"ASC","rin"=>"DESC") );
	$PRUEBA->execute();

	echo $PRUEBA->getRawQuery();
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
