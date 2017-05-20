<?php
require_once( ROOT . "/backend/Module-Accounts/new-user-validation-functions.php" );

$form_status = False;
$form_error = "";
$form_data = NULL;

sleep(3.0);
extract($_POST);
$NewUserData = $_POST;

$form_error = checkForPasswordErrors($password,$password_verification);

if( $form_error == "" ){
    require_once( ROOT . "/backend/Module-Accounts/UserObject.php" );
    $NewUser = new UserObject();
    // $NewUser->new_user_level = 1;
  	if( $NewUser->NewUser($NewUserData, $login_after_insert, $is_verified ) ){
        $form_status = True;
        $form_data = $NewUser->lastId;

        if( $is_verified ){
            // Welcome Email (Delete Account Option)
        }else{
            SendVerificationEmail(
              $NewUser->lastId,
              NOREPLY_EMAIL,
              $NewUserData["email"],
              "",
              BUSINESS_NAME,DOMAIN . "/verify_user.php"
            );
        }

    }else{
        $form_error .= "* ".$NewUser->message(). "</br>";
    }
}
// $form_error = utf8_decode($form_error);
