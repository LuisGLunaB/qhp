<?php
function checkForPasswordErrors($password,$password_verification){
  $form_error = ( ifPassWordsMatch($password,$password_verification) ) ? "" : "* Las contraseñas no coinciden.</br>";
  $form_error .= ( isPasswordLong($password) ) ? "" : "* La contraseña debe tener al menos 6 caracteres.</br>";
  $form_error .= ( isPasswordStrong($password) ) ? "" : "* La contraseña no es lo suficientemente segura.</br>";
  return $form_error;
}
function ifPassWordsMatch($password,$password_verification){
  return ( $password == $password_verification );
}
function isPasswordLong($password,$lenght=6){
  return ( strlen( $password ) >= $lenght );
}
function isPasswordStrong($password) {
  return True;
}

function SendVerificationEmail($user_id,$From,$To,$ToName="",$BusinessName,$VerificationURL){
  $verification_key = CreateVerificationKey($user_id);
  require_once( ROOT . "/backend/Loaders/LOADMODULE_Email.php" );
  SendVerificationEmail(
    $From, //From
    $To, //To
    "Verifica tu cuenta de ". $BusinessName, //Title
    $ToName, //Name
    "$VerificationURL?verification_key=$verification_key" //URL
  );
}
