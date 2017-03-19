<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	/*
	$PRUEBA = new SQLInsert($con, "productos", ["id","rin","marca"]);
	$PRUEBA->INSERT( [array("id" => 3213 ,"rin" => 10, "marca" => "Uno0"),array("id" => 3214 ,"rin" => 20, "marca" => "Dos0")] );
	$PRUEBA->ONDUPLICATE();
	$PRUEBA->execute();
	echo $PRUEBA->getQuery();
	*/
	$data = [array("id" => 3213 ,"rin" => 11, "marca" => "Uno"),array("id" => 3214 ,"rin" => 12, "marca" => "Dos")];
	$PRUEBA = new SQLInsert($con, "productos", ["id","rin","marca"]);
	echo $PRUEBA->saveAsTable( "Muajaja", $data );
	//$PRUEBA->execute();
	//echo $PRUEBA->getQuery();

	// Save as Table
	// Refactoring
	// Update, Delete, activate/deactivate
}else{
	echo $SQLConnection->message();
}







?>
