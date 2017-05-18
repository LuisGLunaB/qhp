<?php

// $FB = new FacebookPageManager( $MKTi );
$FB = new FacebookPageInsights($Yo);
// $FB->loadToken();
/*
// if( $FB->isNewTokenAvailable() ){
//   $FB->catchToken();
//   $FB->extendToken();
//   $FB->saveToken();
//   $FB->loadToken();
// }
*/

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

        echo "Alquimia:<br>";
        DISPLAY::asTable( $FB->FansByCountry("AlquimiaTransforma","2015-05-10") );
        echo "GrupoKP:<br>";
        DISPLAY::asTable( $FB->FansByCountry("GrupoKP","2015-05-10") );
        echo "Colateral:<br>";
        DISPLAY::asTable( $FB->FansByCountry("colateralmkt","2015-05-10") );
        echo "Merkategia:<br>";
        DISPLAY::asTable( $FB->FansByCountry("merkategia","2015-05-10") );
        echo "MKTi:<br>";
        DISPLAY::asTable( $FB->FansByCountry("mkti.mx","2015-05-10") );

        // echo "Cuentas:<br>";
        // DISPLAY::asTable( $FB->AdAccounts() );

      }else {
        echo "No Token";
      }
      echo '<br><a href="'.$FB->getLoginURL().'">Entra con Facebook</a>';
    ?>
  </body>
</html>
