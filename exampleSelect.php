<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){

	/*
	$PRUEBA = new SQLSummarySelector($con,"productos", ["id","rin"]);
	$PRUEBA->UPPERCASE( ["marca"], "");
	$PRUEBA->COUNT( ["pmenudeo"] );
	$PRUEBA->AVG( ["pmenudeo"] );
	$PRUEBA->STD( ["pmenudeo"] );
	$PRUEBA->WHERE( array("marca"=>["Continental","Pirelli","Goodyear"]) );
	$PRUEBA->LOWER_EQUAL( array("rin"=>17) );
	$PRUEBA->GROUPBY( ["marca","rin"]);
	$PRUEBA->PAGE( 0, 25 );
	$PRUEBA->ORDERBY( array("marca"=>"DESC","rin"=>"DESC") );
	//$PRUEBA->ORDERBY( array("pmenudeo_COUNT"=>"DESC") );
	$PRUEBA->execute();
	echo "Raw: ".$PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);
	*/

	/*
	$PRUEBA = new SQLBasicSelector($con,"productos", ["id","marca","modelo","descripcion"]);
	$PRUEBA->SEARCH( "Bridgestone Año 2005", ["marca","modelo","descripcion"]);
	$PRUEBA->ORDERBY( array("search_relevance"=>"DESC") );
	$PRUEBA->execute();
	echo "Raw: ".$PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);
	*/

	$PRUEBA = new SQLSummarySelector($con,"productos");
	echo $PRUEBA->EXISTS( array("id"=>[-10,11,20]) );


}else{
	echo $SQLConnection->message();
}
