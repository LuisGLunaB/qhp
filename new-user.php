<?php
  $ROOT = "."; define("ROOT", $ROOT);

  $new_user_form_name = "new-user-form.php";
  $new_user_redirect = "index.php";
  $new_user_LoginAfterInsert = True;
  $new_user_checkRegisters = False;
  $new_user_level = 1;
  $new_user_is_verified = 0;

  /* If form is submited, attempt to insert user, if not, just retrieve Form
     if there is an error, the message is shown inside the Form */
  include_once( ROOT . "/backend/Module-Accounts/new-user-process.php");
  /* Eviroment: $con, $new_user_status, $new_user_message, $new_user_id, $NewUserForm*/

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Nuevo Usuario</title>
  </head>
  <body>
    <?php
      echo "Form: <br>$NewUserForm";
    ?>
  </body>
</html>
