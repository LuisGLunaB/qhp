<?php
include_once("./FileObject.php");
if( array_key_exists("Archivo",$_FILES) ){
  $Archivo = new FileObject( $_FILES["Archivo"] );
  echo $Archivo->SaveTo();
  print_r($_FILES);
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <style media="screen">
      form input{
        display: block;
      }
    </style>

    <form class="" action="" method="post" enctype="multipart/form-data">
      <input type="input" name="dato" value="Hola!">
      <input type="file" name="Archivo" >
      <input type="submit" name="enviar" value="Enviar">
    </form>

  </body>
</html>
