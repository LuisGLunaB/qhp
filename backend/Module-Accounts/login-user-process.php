<?php

sleep(2.0); // Ease dictionary attacks

// require_once( ROOT . "/backend/Module-Accounts/new-user-validation-functions.php" );
$form_status = False;
$form_error = "";
$form_data = NULL;

$UserData = $_POST;

require_once( ROOT . "/backend/Module-Accounts/UserObject.php" );
$UsernameOrEmail = UserObject::extractEmailOrUsername( $UserData );
$Password = $_POST["password"];

$LoginUser = new UserObject();
if( $LoginUser->Login( $UsernameOrEmail, $Password) ){
  $form_status = True;
  $form_data = $LoginUser->getUserId();
}else{
  $form_error = $LoginUser->message();
}
