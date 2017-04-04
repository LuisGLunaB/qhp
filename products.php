<?php
  $ROOT = "."; define("ROOT", $ROOT);
  include_once( ROOT . "/backend/Loaders/LOADMODULE_SQL_BASIC.php");
  /* Enviroment: $SQLConnection, $con */
  if( $SQLConnection->status() ){
    $PRODUCTS = new SQLBasicSelector("products_view_ledcity");
    /*$PRODUCTS->free(
      "SELECT *,
        CONCAT(watts,'W') AS wattsW,
        IF( voltage_min = voltage_max, CONCAT(voltage_min,'V'), CONCAT(voltage_min,'V - ',voltage_max,'V') ) AS voltage,
        CONCAT('$', FORMAT(price_1, 2)) AS price1,
        CONCAT('$', FORMAT(price_2, 2)) AS price2,
        CONCAT('$', FORMAT(price_3, 2)) AS price3,
        CONCAT('$', FORMAT(price_4, 2)) AS price4,
        CONCAT('$', FORMAT(price_5, 2)) AS price5
       FROM products
       NATURAL JOIN product_brands
       NATURAL JOIN product_categories_1
       NATURAL JOIN product_categories_2
      ");
      $PRODUCTS->saveAsView("products_view_ledcity");
    */
    $PRODUCTS->execute();
    DISPLAY::asTable( $PRODUCTS->data );
  }

?>
