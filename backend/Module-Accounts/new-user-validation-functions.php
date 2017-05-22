<?php
function checkForUsernameErrors($username){
  $form_error = ( isStringLong($username,5) ) ? "" : "* El nombre de usuario debe tener al menos 6 caracteres.</br>";
  return $form_error;
}
function checkForPasswordErrors($password,$password_verification){
  $form_error = ( ifPassWordsMatch($password,$password_verification) ) ? "" : "* Las contraseñas no coinciden.</br>";
  $form_error .= ( isStringLong($password) ) ? "" : "* La contraseña debe tener al menos 6 caracteres.</br>";
  $form_error .= ( isPasswordStrong($password) ) ? "" : "* La contraseña no es lo suficientemente segura.</br>";
  return $form_error;
}
function ifPassWordsMatch($password,$password_verification){
  return ( $password == $password_verification );
}
function isStringLong($string,$lenght=6){
  return ( strlen( $string ) >= $lenght );
}
function isPasswordStrong($password) {
  return True;
}

function SendVerificationEmail($user_id,$From,$To,$ToName="",$BusinessName,$VerificationURL){
  // $verification_key = CreateVerificationKey($user_id);
  // require_once( ROOT . "/backend/Loaders/LOADMODULE_Email.php" );
  // Email(
  //   $From, //From
  //   $To, //To
  //   "Verifica tu cuenta de ". $BusinessName, //Title
  //   $ToName, //Name
  //   "$VerificationURL?verification_key=$verification_key" //URL
  // );
}

function SendVerificationEmail2($From,$To,$Title,$NAME,$VERIFICATION_URL){

	$success = SendEmailWithTemplate(
		$From, $To ,$Title,
		array("NAME" => $NAME,"VERIFICATION_URL" => $VERIFICATION_URL),
		MODULE_ROUTE_Email . "verification_template.php"
	);

	if( ! $success ) {
		echo "Error al enviar email de verificación. ";
	}

	return $success;
}
