<?php
  $ROOT = "."; define("ROOT",$ROOT);
  include_once( ROOT . "/backend/Module-Facebook/FacebookObject.php");
  include_once( ROOT . "/backend/Module-Functionals/DISPLAY.php");

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  $fb_app_secret = 'c478fbfb19e9f22f688b5db5144086c7';
  $fb_app_id = '632416283438924';

  $fb_con = FacebookConnection::Set( $fb_app_secret, $fb_app_id);
  if( is_null($fb_con) ){
    echo "Error al conectar a Facebook";
    exit;
  }

  echo "Conectado.<br>";
  $FB = new FacebookObject($fb_con);
  if( !$FB->status() ){
    echo "Error Declarar Objeto";
    exit;
  }

  echo "Declarado.<br>";
  if( $FB->isNewTokenAvailable() ){
    $FB->catchToken();
    if( $FB->status() ){
      echo "Token atrapado.<br>";
    }else{
      echo "Error al atrapar Token.<br>";
      exit;
    }
  }else{
    $FB->setToken($Yo);
    $FBLoginURL = $FB->getLoginURL("http://pruebas.mkti.mx/facebook.php");
    echo "<a href='$FBLoginURL'>Facebook Login</a>";
  }

  if( $FB->status() ){
    $Answer = $FB->FanpagePosts("mkti.mx");
    DISPLAY::asTable($Answer);
  }
