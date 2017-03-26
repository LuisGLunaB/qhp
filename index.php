<?php
include_once("./SQLModule.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();

if( $SQLConnection->status() ){
	include_once("./UserObject.php");
	$User = new UserObject();
	$NewUserData = array("email"=>"Luis".rand(100,10000), "password"=>"lol2");

	//$User->NewUser( $NewUserData );
	$User->Login("Luis2","lol2");
	print_r($User->UserData);
}else{
	echo $SQLConnection->message();
}

?>

<a href="navigate.php">Navigate</a>
