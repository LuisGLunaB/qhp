<?php
include_once("./Loaders/LOADMODULE_ALL.php");

$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();

if( $SQLConnection->status() ){
	$User = new UserObject();
	$NewUserData = array("username"=>"Luis23", "password"=>"lol2");

	//$User->NewUser( $NewUserData );
	//$User->Login("Luis2","lol2");

	print_r($User->UserData);
}else{
	echo $SQLConnection->message();
}

?>

<a href="navigate.php">Navigate</a>
