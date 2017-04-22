<?php
if( ! isset($AutomaticSQLConnection) ){
  $AutomaticSQLConnection = True;
}

if( $AutomaticSQLConnection ){
  if( ! isset($SQLConnection) ){
    // $SQLConnection = new SQLConnector("mkti.mx","db_ledcity","user_ledcity","password_ledcity");
    $SQLConnection = new SQLConnector("localhost","test","root","");
    $con = $SQLConnection->getConnector();
  }
}
