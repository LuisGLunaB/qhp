<?php
$login_user_status = False;
$login_user_message = "";


if( key_exists("form",$_POST) ){ if( $_POST["form"] == "login"){
    ## Login Form was submited ##
    sleep(0.1); // Sleeping eases Form submission atacks
    require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );
    /* Enviroment: $SQLConnection, $con */
    if( $SQLConnection->status() ){
        require_once( ROOT . "/backend/Module-Accounts/UserObject.php" );

        $UsernameOrEmail = ( array_key_exists("email",$_POST) ) ? $_POST["email"] : $_POST["username"] ;
        $Password = $_POST["password"];

        $LoginUser = new UserObject();
        if( $LoginUser->Login( $UsernameOrEmail, $Password) ){
          $login_user_message = True;
          if( (!is_null($login_user_redirect)) and $login_user_redirect != "" ){
            header("Location: $login_user_redirect");}
        }else{
          $login_user_message = $LoginUser->message();
        }
    }else{
      $login_user_message = $SQLConnection->message();
    }

    $login_user_message = utf8_decode($login_user_message);

}} // If form is submitted procedure END

$LoginForm = file_get_contents( ROOT . "/backend/Forms/$login_form_name");
if($login_user_message != ""){$LoginForm .= '<div class="form-error">'.$login_user_message.'</div>';}
