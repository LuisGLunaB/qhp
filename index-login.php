<?php
$ROOT = "."; define("ROOT", $ROOT);
include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
/* Eviroment: $SQLConnection, $con */

if( $SQLConnection->status() ){
  $User = UserObject::FullLoginWithCookieLevel($required_level=1,$redirect_url=NULL);
	print_r($User->UserData);

  $SQL = new SQLObject();
  DISPLAY::asTable(  $SQL->QUERY("SELECT user_id, email AS 'correo_electronico' FROM users;")  );

}else{
	echo $SQLConnection->message();
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Ya...veamos</title>
  </head>
  <body>
    <a href="new-user.php">New User</a>
    <a href="login.php">Login</a>
  </body>
</html>
