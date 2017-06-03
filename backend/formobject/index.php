<?php
  $ROOT = "."; define("ROOT",$ROOT);
  // include_once("$ROOT/FormObject.php");
  //
  // $FormData = &$_POST;
  // $Dummy =  array(
  //   "name"=>"Luis",
  //   "email"=>"germi_uga@hotmail.com"
  // );
  // $FormData = $Dummy; //Quitar
  //
  // $FormObject = new FormObject( $FormData );
  // $FormObject->SaveData( );
  include_once("$ROOT/EmailerObject.php");
  $Emailer = new EmailerObject("contacto@mkti.mx");
  $sent = $Emailer->SendWithTemplate("luis@mkti.mx","Email Prueba","Hola Luis");

  if($sent){
    echo "Enviado!";
  }else{
    echo $Emailer->errorMessage();
  }

?>
