<?php
$ROOT = "."; define("ROOT", $ROOT);

include_once( ROOT . "/backend/Module-Facebook/FacebookObject.php");
$FB = new FacebookObject();

if( $FB->isNewTokenAvailable() ){
  $FB->catchToken();
  $FB->extendToken();
  // $FB->saveToken();
  // $FB->loadToken();
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Yotta</title>
  </head>
  <body>
    <?php
      if( $FB->hasToken() ){
        print_r( $FB->me() );
        DISPLAY::asTable( $FB->accounts() );
      }
      echo '<br><a href="'.$FB->getLoginURL().'">Entra con Facebook</a>';
    ?>
  </body>
</html>
