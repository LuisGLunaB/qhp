<?php
  $ROOT = "."; define("ROOT", $ROOT);

  require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );
  $error_message = "";
  if( $SQLConnection->status() ){

      if( was_form_submitted("login-user") ){
        /* Eviroment: $form_data, $form_status, $form_error */
        include_once( ROOT . "/backend/Module-Accounts/login-user-process.php");
        if($form_status){
          header("Location: index-login.php");
        }
      }

  }else{
  	$error_message = $SQLConnection->message();
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Entrar</title>
  </head>
  <body>
    <?php
      include_once( ROOT . "/backend/Module-Accounts/login-form.php" );
    ?>
  </body>
</html>
