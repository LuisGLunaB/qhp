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
	$PRUEBA->GROUPBY( ["marca","rin"]);
	$PRUEBA->PAGE( 0, 10 );
	$PRUEBA->execute();
	echo "Raw: ".$PRUEBA->getRawQuery();
	DISPLAY::asTable($PRUEBA->data);


	include_once("./UserObject.php");
	$User = new UserObject();
	//$User->NewUser( array("username"=>"Luis", "password"=>"lol2") );
	$User->LoginWithCookie();
	print_r($User->UserData);


}else{
	echo $SQLConnection->message();
}




?>
