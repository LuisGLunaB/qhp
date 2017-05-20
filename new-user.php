<?php
  $ROOT = "."; define("ROOT", $ROOT);

  require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );
  $error_message = "";
  if( $SQLConnection->status() ){

      if( was_form_submitted("new-user") ){
        /* Eviroment: $form_data, $form_status, $form_error */
        include_once( ROOT . "/backend/Module-Accounts/new-user-process.php");
        if($form_status){
          header("Location: index.php");
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
    <title>Nuevo Usuario</title>
  </head>
  <body>
    <?php
      include_once( ROOT . "/backend/Module-Accounts/new-user-form.php" );
    ?>
  </body>
</html>
