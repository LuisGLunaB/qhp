<?php
$required_user_level = ( isset($required_user_level) ) ?
    $required_user_level : 1;
$user_denied_access_url = ( isset($user_denied_access_url) ) ?
    $user_denied_access_url : "logout.php?message=restricted-access";

$User = new UserObject();
if( $User->isCookieLoginPossible() ){
    if( $User->LoginWithCookie() ){
      if( $User->hasRequiredLevel($required_user_level) ){
          print_r($User->UserData);
      }else{
          // Restricted section
          header("Location: $user_denied_access_url ");
      }
    }else{
      // echo "Error with the cookie: ". $User->message();
    }
}else{
  // echo "No cookies set, all good.";
}
