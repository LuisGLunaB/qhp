<?php

// $FB = new FacebookPageManager( $MKTi );
$FB = new FacebookPageInsights($MKTi);
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

        echo "Me:<br>";
        print_r( $FB->me() );
        echo "<br>";
        DISPLAY::asTable( $FB->OnlineFans("2017-03-01") );
        DISPLAY::asTable(  FacebookPageInsights::table_percentiles($FB->data) );


        // list($avg,$std) = FacebookPageInsights::columns_avgstd($FB->data);
        // print_r( $avg );
        // print_r( $std );
        // $data = $FB->CONVERSATIONS();
        // echo "Inbox:<br>";
        // echo "size: $FB->size since: $FB->since until: $FB->until function: $FB->LastFunction <br>";
        // print_r( $data );



        // while( ! $FB->isLastPage() ){
        //   echo "<br><br>";
        //   $data = $FB->next();
        //   echo "Inbox:<br>";
        //   echo "size: $FB->size since: $FB->since until: $FB->until function: $FB->LastFunction <br>";
        //   print_r( $data );
        // }

      }else {
        echo "No Token";
      }
      echo '<br><a href="'.$FB->getLoginURL().'">Entra con Facebook</a>';
    ?>
  </body>
</html>
