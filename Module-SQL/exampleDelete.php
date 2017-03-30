<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLDelete($con, "productos");

	$PRUEBA->WHERE( array("marca"=>["Luis","Luis2"]) );

	$PRUEBA->execute();
	echo $PRUEBA->getQuery();

}else{
	echo $SQLConnection->message();
}







?>
