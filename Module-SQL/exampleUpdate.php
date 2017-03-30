<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();
if( $SQLConnection->status() ){
	$PRUEBA = new SQLUpdate($con, "productos", ["id","rin","marca"] );
	$set = array("rin" => 10013, "marca" => "Tr1es");
	$where = array("id" => 3215);
	$PRUEBA->UPDATE();
	$PRUEBA->SETNOTACTIVE();
	$PRUEBA->PLUSONE( ["ancho","alto"] );
	$PRUEBA->MINUSONE( ["rin"] );

	$PRUEBA->WHEREID( 3220 );
	$PRUEBA->execute();
	echo $PRUEBA->getQuery();

}else{
	echo $SQLConnection->message();
}







?>
