<?php
$ROOT = "."; define("ROOT", $ROOT);
include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
/* Eviroment: $SQLConnection, $con */

if( $SQLConnection->status() ){
  $User = UserObject::FullLoginWithCookieLevel(1,NULL);
	print_r($User->UserData);
}else{
	echo $SQLConnection->message();
}


// echo "Email: " . SendVerificationEmail("contacto@mkti.mx", "luis.g.luna18@gmail.com", "Verifica tu cuenta", "Luis", "http://mkti.mx/" );
// echo "Verificar: " . VerifyUser("hola-3");
// $key = CreateVerificationKey(1);
// echo "Key: " . $key;
// echo "Verificar: " . VerifyUser($key);

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Backend Index</title>
  </head>
  <body>
    <a href="new-user.php">New User</a>
  </body>
</html>
