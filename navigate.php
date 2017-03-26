<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();

if( $SQLConnection->status() ){
	include_once("./UserObject.php");
	$User = new UserObject();
	$NewUserData = array("username"=>"Luis2", "password"=>"lol2");
	//$User->NewUser( $NewUserData );

	echo $User->LoginWithCookie();

	print_r($User->UserData);
}else{
	echo $SQLConnection->message();
}




?>

<a href="index.php">Index</a>
