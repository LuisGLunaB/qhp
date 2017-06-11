<?php
include_once("./FileObject.php");
if( array_key_exists("Archivo",$_FILES) ){
  $Archivo = new ImageObject( $_FILES["Archivo"] );
  $Archivo->SaveTo("./uploads/", "ejemplo_compressed." . $Archivo->getExtension() );
  // Cropping
  $Archivo->Convert2JPEG();
  $Archivo->CreateThumbnail(100);
  $Archivo->FitTo(550);
  $Archivo->Compress();

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
