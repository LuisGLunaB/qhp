<?php
$new_user_status = False;
$new_user_message = "";

if( key_exists("form",$_POST) ){ if( $_POST["form"] == "new-user"){
    ## Form was submited ##
    require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );
    /* Enviroment: $SQLConnection, $con */
    if( $SQLConnection->status() ){
      echo "Enviado y conectado";
    }else{
      $new_user_message = $SQLConnection->message();
    }
}}

$NewUserForm = utf8_decode(file_get_contents( ROOT . "/backend/Forms/new-user-form.php"));
//Add div:form-error si hubo un error
