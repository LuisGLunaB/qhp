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
	$PRUEBA->PAGE( 0,10 );
	$PRUEBA->LOWER_EQUAL( array("id"=>100) );
	$PRUEBA->ORDERBY( array("pmenudeo_COUNT"=>"DESC") );
	$PRUEBA->saveAsView("yeyv");
	$PRUEBA->execute();

	//$PRUEBA->executeFree("SELECT * FROM productos", []);

	echo "Raw: ".$PRUEBA->getRawQuery();
	//echo "Free: ".$PRUEBA->FREE_query;
	DISPLAY::asTable($PRUEBA->data);
}else{
	echo $SQLConnection->message();
}
