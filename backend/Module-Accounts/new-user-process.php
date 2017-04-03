<?php
$new_user_status = False;
$new_user_message = "";

// Validate: Password lenght, Password strenght, Password match,
// Username lenght, Non Empty fields

if( key_exists("form",$_POST) ){ if( $_POST["form"] == "new-user"){
    ## Form was submited ##
    require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );
    /* Enviroment: $SQLConnection, $con */
    if( $SQLConnection->status() ){
        require_once( ROOT . "/backend/Module-Accounts/UserObject.php" );
      	$NewUserData = $_POST;
        $NewUser = new UserObject();
      	if( $NewUser->NewUser($NewUserData, $LoginAfterInsert, $is_verified) ){
            // New User has been created
            $new_user_status = True;
            $new_user_id = $NewUser->lastId;
            if( (!is_null($new_user_redirect)) and $new_user_redirect != "" ){
              header("Location: $new_user_redirect");
            }
        }else{
            $new_user_message = $NewUser->message();
        }
    }else{
      $new_user_message = $SQLConnection->message();
    }
}}

$NewUserForm = utf8_decode(
  file_get_contents( ROOT . "/backend/Forms/new-user-form.php")
);

if($new_user_message != ""){
  $NewUserForm .= '<div class="error">'.$new_user_message.'</div>';
}
