<?php
  $ROOT = "."; define("ROOT", $ROOT);

  $new_user_redirect = "";
  $LoginAfterInsert = False;
  $is_verified = 0;
  $new_user_id = 0; // quitar

  include_once( ROOT . "/backend/Module-Accounts/new-user-process.php");

  /* Eviroment:
    $con
    $new_user_status
    $new_user_message
    $new_user_id;
    $NewUserForm
  */
  echo "$new_user_id Form: <br>$NewUserForm";
?>
