<?php
  $ROOT = "."; define("ROOT", $ROOT);
  include_once( ROOT . "/backend/Loaders/LOADMODULE_SQL_BASIC.php");
  /* Enviroment: $SQLConnection, $con */
  if( $SQLConnection->status() ){
    $PRODUCTS = new SQLBasicSelector("products_view_ledcity");
    $PRODUCTS->PAGE(0,20);
    $TOTAL = $PRODUCTS->TOTAL();
    $PRODUCTS->execute();

    /*
    TODO:
      - Make getCategoriesCount call getFieldsCount
      - Refactor NAVIGATE
      - Parse POST:
        1. Fill with NULLs given expected keys ( is_null($_POST[x]) )
        2. Ignore emptyValues mask ( isset($_POST[x]) )
    */

    $distinct_count = NAVIGATE::getFieldCount("products_view_ledcity","voltage","voltage");
    echo NAVIGATE::buildFormSelect($distinct_count);

    $distinct_count = NAVIGATE::getFieldCount("products_view_ledcity","category_1_id","category_1_name");
    echo NAVIGATE::buildFormSelect($distinct_count);

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
