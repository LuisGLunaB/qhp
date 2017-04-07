<?php
  $ROOT = "."; define("ROOT", $ROOT);
  include_once( ROOT . "/backend/Loaders/LOADMODULE_SQL_BASIC.php");
  /* Enviroment: $SQLConnection, $con */

  if( $SQLConnection->status() ){

    $ViewName = "products_view_ledcity";
    $FilterFields = [ "category_1_id", "category_2_id", "wattsW" ];
    $FILTER = NAVIGATE::ParseRequest($_GET, $FilterFields);

    $page = 0; $limit = 20;
    $PRODUCTS = new SQLBasicSelector( "products_view_ledcity" );
    $PRODUCTS->WHERE( $FILTER , $FilterFields );
    $PRODUCTS->PAGE( $page , $limit );
    $PRODUCTS->execute();
    $total = $PRODUCTS->TOTAL();

    list($WHERE_query, $WHERE_binds) = $PRODUCTS->WHERE->getWHERE();

    $OPTIONS_category_1 = NAVIGATE::getCategoriesCount($ViewName,1,$WHERE_query,$WHERE_binds); //If cat 1 selected: Ignore WHERES
    $OPTIONS_category_2 = NAVIGATE::getCategoriesCount($ViewName,2,$WHERE_query,$WHERE_binds);
    $OPTIONS_wattsW = NAVIGATE::getFieldCount($ViewName,"wattsW","wattsW",$WHERE_query,$WHERE_binds);

    $SELECT_category_1 = NAVIGATE::buildFormSelect($OPTIONS_category_1, $FILTER["category_1_id"] );
    $SELECT_category_2 = NAVIGATE::buildFormSelect($OPTIONS_category_2, $FILTER["category_2_id"] );
    $SELECT_wattsW = NAVIGATE::buildFormSelect($OPTIONS_wattsW, $FILTER["wattsW"] );

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <form id="form" class="form" action="" method="get" enctype="multipart/form-data">
      <?php
      print_r($FILTER);

      echo $SELECT_category_1;
      echo $SELECT_category_2;
      echo $SELECT_wattsW;

      ?>
      <input type="submit" name="submit" value="Enviar">
    </form>

    <?php DISPLAY::asTable( $PRODUCTS->data ); ?>
  </body>
</html>
