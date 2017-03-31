<?php
include_once("./Loaders/LOADMODULE_ALL.php");
$SQLConnection = new SQLConnector("localhost","test","root","");
$con = $SQLConnection->getConnector();

//echo "Email: " . SendVerificationEmail("contacto@mkti.mx", "luis.g.luna18@gmail.com", "Verifica tu cuenta", "Luis", "http://mkti.mx/" );
//echo "Verificar: " . VerifyUser("hola-3");
$key = CreateVerificationKey(1);
echo "Key: " . $key;
echo "Verificar: " . VerifyUser($key);
/*


if( $SQLConnection->status() ){
	//$User = new UserObject();
	//$NewUserData = array("username"=>"Luis23", "password"=>"lol2");

	//$User->NewUser( $NewUserData );
	//$User->Login("Luis2","lol2");

	print_r($User->UserData);
}else{
	echo $SQLConnection->message();
}
*/

?>

<a href="navigate.php">Navigate</a>
