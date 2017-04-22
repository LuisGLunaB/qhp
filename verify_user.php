<?php
  $ROOT = "."; define("ROOT", $ROOT);
  $verification_key = "";
  if( isset($_GET) ){ if(array_key_exists("verification_key",$_GET)){
    $verification_key  = $_GET["verification_key"];
  }}
?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Verificar Cuenta</title>
   </head>
   <body>

   </body>
 </html>
