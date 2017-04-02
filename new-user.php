<?php
  $ROOT = "."; define("ROOT", $ROOT);
  $new_user_redirect = "";
  include_once( ROOT . "/backend/Module-Accounts/new-user-process.php");
  /* Eviroment:
    $con
    $new_user_status
    $new_user_message
    $NewUserForm
  */
  echo "Form: <br>$NewUserForm";
?>
