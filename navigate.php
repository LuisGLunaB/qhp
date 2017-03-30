<?php
include_once("./Loaders/LOADMODULE_ALL.php");

$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();

if( $SQLConnection->status() ){
	$User = new UserObject();
	$NewUserData = array("username"=>"Luis2", "password"=>"lol2");

	//$User->NewUser( $NewUserData );
	$User->LoginWithCookie();

	print_r($User->UserData);
}else{
	echo $SQLConnection->message();
}

?>

<a href="index.php">Index</a>
