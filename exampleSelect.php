<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){

	$PRUEBA = new SQLSummarySelector("productos", ["id","modelo","rin"]);
	$PRUEBA->UPPERCASE( ["marca"], "");
	$PRUEBA->COUNT( ["pmenudeo"] );
	$PRUEBA->AVG( ["pmenudeo"] );
	$PRUEBA->STD( ["pmenudeo"] );
	$PRUEBA->SEARCH( "Cinturato", ["marca","modelo","descripcion"]);
	//$PRUEBA->WHERE( array("marca"=>["Continental","Pirelli","Goodyear","Bridgestone"]) );
	//$PRUEBA->LOWER_EQUAL( array("rin"=>18) );
	$PRUEBA->GROUPBY( ["id"]);
	$PRUEBA->PAGE( 0, 20 );
	//$PRUEBA->ORDERBY( array("marca"=>"DESC","rin"=>"DESC") );
	//$PRUEBA->ORDERBY( array("pmenudeo_COUNT"=>"DESC") );
	$PRUEBA->execute();
	echo "Raw: ".$PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);


	/*
	$PRUEBA = new SQLBasicSelector($con,"productos", ["id","marca","modelo"]);
	$PRUEBA->SEARCH( "Bridgestone Año 2005", ["marca","modelo","descripcion"]);
	$PRUEBA->PAGE( 0, 10 );
	$PRUEBA->execute();
	echo "Raw: ".$PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);
	*/
	/*
	$PRUEBA = new SQLSummarySelector($con,"productos");
	echo $PRUEBA->EXISTS( array("id"=>[-10,11,20]) );
	*/


}else{
	echo $SQLConnection->message();
}
