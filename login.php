<?php
  $ROOT = "."; define("ROOT", $ROOT);

  $login_form_name = "login-form.php";
  $login_user_redirect = "index.php";


  include_once( ROOT . "/backend/Module-Accounts/login-user-process.php");
  /* Eviroment: $con, $new_user_status, $new_user_message, $new_user_id, $NewUserForm*/

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Entrar</title>
  </head>
  <body>
    <?php
      echo "Form: <br>$LoginForm";
    ?>
  </body>
</html>
