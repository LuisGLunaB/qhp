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
	$PRUEBA->PAGE( 0, 25 );
	//$PRUEBA->LOWER_EQUAL( array("id"=>100) );
	$PRUEBA->ORDERBY( array("pmenudeo_COUNT"=>"DESC") );
	$PRUEBA->execute();
	echo "Raw: ".$PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);

	/*
	$PRUEBA = new SQLSummarySelector($con,"productos");
	echo $PRUEBA->EXISTS( array("id"=>[-10,11,20]) );
	*/

}else{
	echo $SQLConnection->message();
}
