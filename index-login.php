<?php
$ROOT = "."; define("ROOT", $ROOT);
include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
/* Eviroment: $SQLConnection, $con */

if( $SQLConnection->status() ){
  include_once( ROOT . "/backend/Module-Accounts/cookie-login-process.php");
  /* Eviroment: $User -Instance */


  $SQL = new SQLObject();
  DISPLAY::asTable(  $SQL->QUERY("SELECT user_id, email AS 'correo_electronico' FROM users;")  );

}else{
	alert( $SQLConnection->message() );
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Loged User</title>
  </head>
  <body>
    <a href="new-user.php">New User</a>
    <a href="login.php">Login</a>
  </body>
</html>
