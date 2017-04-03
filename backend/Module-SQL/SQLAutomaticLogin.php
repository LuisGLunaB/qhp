<?php
if( ! isset($AutomaticSQLConnection) ){
  $AutomaticSQLConnection = True;
}

if( $AutomaticSQLConnection ){
  if( ! isset($SQLConnection) ){
    $SQLConnection = new SQLConnector("localhost","test","root","");
    $con = $SQLConnection->getConnector();
  }
}
