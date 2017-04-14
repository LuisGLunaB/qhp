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


echo file_get_contents( ROOT . "/backend/Module-Email/verification_template.php");

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Backend Index</title>
  </head>
  <body>
    <a href="new-user.php">New User</a>
    <a href="login.php">Login</a>
  </body>
</html>
