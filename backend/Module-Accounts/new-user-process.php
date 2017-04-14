<?php
$new_user_status = False;
$new_user_message = "";
$new_user_id = NULL;

// Validate: Password lenght, Password strenght, Password match,
// Username lenght, Non Empty fields

if( key_exists("form",$_POST) ){ if( $_POST["form"] == "new-user"){
    ## New User Form was submited ##
    sleep(4); // Sleeping eases Form submission atacks
    require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );
    /* Enviroment: $SQLConnection, $con */
    if( $SQLConnection->status() ){

      	$NewUserData = $_POST;

        if( ifPassWordsMatch($NewUserData) ){
            if( isPasswordLong($NewUserData) ){
                if( isPasswordStrong($NewUserData) ){
                    if( isUserNameValid($NewUserData) ){

                        require_once( ROOT . "/backend/Module-Accounts/UserObject.php" );
                        $NewUser = new UserObject();
                        $NewUser->new_user_level = $new_user_level;
                      	if( $NewUser->NewUser($NewUserData, $new_user_LoginAfterInsert, $new_user_is_verified, $new_user_checkRegisters) ){
                            // New User has been created
                            $new_user_status = True;
                            $new_user_id = $NewUser->lastId;

                            if( $new_user_is_verified){
                                //Welcome Email (Delete Option)
                            }else{
                                //Verification Email
                                $verification_key = CreateVerificationKey($new_user_id);
                                require_once( ROOT . "/backend/Loaders/LOADMODULE_Email.php" );
                                SendVerificationEmail(
                                  NOREPLY_EMAIL, //From
                                  $NewUserData["email"], //To
                                  "Verifica tu cuenta de ". BUSINESS_NAME, //Title
                                  "", //Name
                                  DOMAIN . "/verify_user.php?verification_key=$verification_key" //URL
                                );
                            }

                            if( (!is_null($new_user_redirect)) and $new_user_redirect != "" ){
                              header("Location: $new_user_redirect");}
                        }else{
                            $new_user_message = $NewUser->message();
                        }

                    }else{
                      $new_user_message = utf8_encode("El nombre de usuario no es válido.");}
                }else{
                  $new_user_message = utf8_encode("La contraseña no es lo suficientemente segura.");}
            }else{
              $new_user_message = utf8_encode("La contraseña debe tener al menos 6 caracteres.");}
        }else{
          $new_user_message = utf8_encode("Las contraseñas no coinciden.");}
    }else{
      $new_user_message = $SQLConnection->message();
    }

    $new_user_message = utf8_decode($new_user_message);

}} // If form is submitted procedure END

$NewUserForm = file_get_contents( ROOT . "/backend/Forms/$new_user_form_name");
if($new_user_message != ""){$NewUserForm .= '<div class="form-error">'.$new_user_message.'</div>';}

function ifPassWordsMatch($REQUEST){
  return ( $REQUEST["password"] == $REQUEST["password-verification"] );
}
function isPasswordLong($REQUEST,$lenght=6){
  return ( strlen($REQUEST["password"]) >= $lenght );
}
function isPasswordStrong($REQUEST) {
  return True;
}
function isUserNameValid($REQUEST,$lenght=5){
  if( array_key_exists("username",$REQUEST) ){
    return ( strlen($REQUEST["username"]) >= $lenght );
  }else{
    return True;
  }
}
