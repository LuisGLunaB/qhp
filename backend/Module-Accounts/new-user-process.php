<?php

sleep(3.0); // Ease automatic user creation

require_once( ROOT . "/backend/Module-Accounts/new-user-validation-functions.php" );
$form_status = False;
$form_error = "";
$form_data = NULL;

// Get data from $_POST
$NewUserData = $_POST;
$login_after_insert = $_POST["login_after_insert"];
$is_verified = $_POST["is_verified"];
$password = $_POST["password"];
$password_verification = $_POST["password_verification"];

//Data validation and error catching
$is_username_register = array_key_exists("username",$_POST);
if( $is_username_register ){
  $username = $_POST["username"];
  $form_error = checkForUsernameErrors($username);
}else{
  $email = $_POST["email"];
  $form_error = checkForPasswordErrors($password,$password_verification);
}

// Attemp to register user
if( $form_error == "" ){
    require_once( ROOT . "/backend/Module-Accounts/UserObject.php" );
    $NewUser = new UserObject();
    // $NewUser->new_user_level = 1;
  	if( $NewUser->NewUser($NewUserData, $login_after_insert, $is_verified ) ){
        $form_status = True;
        $form_data = $NewUser->lastId;

        if( $is_verified ){
            // Welcome Email (Delete Account Option)
        }else{ }
          if( ! $is_username_register ){
            SendVerificationEmail(
              $NewUser->lastId,
              NOREPLY_EMAIL,
              $email,
              "",
              BUSINESS_NAME,DOMAIN . "/verify_user.php"
            );
          }
    }else{
        $form_error .= "* ".$NewUser->message(). "</br>";
    }
}
// $form_error = utf8_decode($form_error);
