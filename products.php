<?php
  $ROOT = "."; define("ROOT", $ROOT);
  include_once( ROOT . "/backend/Loaders/LOADMODULE_SQL_BASIC.php");
  /* Enviroment: $SQLConnection, $con */
  if( $SQLConnection->status() ){
    //$categories_1 = NAVIGATE::getProductCategories(1);
    //$categories_2 = NAVIGATE::getProductCategories(2);


    $PRODUCTS = new SQLBasicSelector("products_view_ledcity");
    $PRODUCTS->PAGE(0,20);
    $TOTAL = $PRODUCTS->TOTAL();
    $PRODUCTS->execute();

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php
    echo NAVIGATE::buildCategorySelect(1);
    echo NAVIGATE::buildCategorySelect(2);
    echo NAVIGATE::PaginationBar(0,20,$TOTAL);
    DISPLAY::asTable( $PRODUCTS->data );
    ?>
  </body>
</html>
