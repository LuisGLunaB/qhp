<?php
/* Eviroment: $ECOM, $SQLConnection, $con & defined(ECOMMERCE_ROUTES) */
include_once("./backend/Module-Ecommerce/LOAD_ECOMMERCE_ENVIROMENT.php");

$error_message = "";
if( $SQLConnection->status() ){

  // $tiendas_AS = TRANSLATE("tiendas");
  // $editar_AS = TRANSLATE("editar");
  // $eliminar_AS = TRANSLATE("eliminar");
  //
  // $store_list_query =
  // "SELECT
  //   store_name AS '$tiendas_AS',
  //   LINK( CONCAT('update_store.php?store_id=',store_id),'$editar_AS','action') AS '$editar_AS',
  //   LINK( CONCAT('delete_store.php?store_id=',store_id),'$eliminar_AS','action') AS '$eliminar_AS'
  //  FROM
  //   stores
  //  ORDER BY
  //   store_name ASC
  //  ;
  // ";
  //
  // $SQL = new SQLObject();
  // $store_list = $SQL->QUERY( $store_list_query );

  $categories_table = $ECOM->ReadCategoriesAll();
}else{
	$error_message = $SQLConnection->message();
}

# UI Navegation:
$SectionTitle = "categorias";
$BreadCrumbs = [
  ["index.php","mi_tienda"],
  ["categories.php","categorias"]
];
$MenuButtons = [
  ["create_category.php","add"]
];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html lang="<?php pTRANSLATE("lang"); ?>" xml:lang="<?php pTRANSLATE("lang"); ?>"
xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>E-Commerce</title>
	<?php include_once( ECOMMERCE_ROUTE . "head-configurations.php" ); ?>
	<style type="text/css">

	</style>
</head>

<body onresize="" onload="">
	<?php include_once("$ROOT/UI/ui-sidebar.php"); ?>
  <div id="ui-main">
    <?php include_once("$ROOT/UI/ui-header.php"); ?>
      <div class="ui-content medium row left-align" >

        <?php

          // DISPLAY::asTable( $categories_table , "ui-table");
        ?>
        <style media="screen">
          .category-description a{
            display: inline-block !important;
            width: auto;
            text-decoration: underline;
          }
        </style>

        <table class="ui-table select-category">

          <thead>
            <tr>
              <th><?php pTRANSLATE("categorias"); ?></th>
              <th class=""><?php pTRANSLATE("editar"); ?></th>
              <th class="editar protected-section"><?php pTRANSLATE("eliminar"); ?></th>
            </tr>
          </thead>

          <tbody>
            <?php
            foreach($categories_table as $row){
              $a = ECOMMERCE::CategoryRow2CategoryTable( $row ) ;
              $category_links = ECOMMERCE::CategoryTableRow2Links( $a );
              // print_r($row);
              echo "
              <tr>
                <td class='category-description'>$category_links</td>
                <td class='edit'> <a href='javascript:{}'>".TRANSLATE("editar")."</a> </td>
                <td class='delete protected-section'> <a href='javascript:{}'>".TRANSLATE("eliminar")."</a> </td>
              </tr>";
            }
            ?>
          </tbody>


        </table>

        <script type="text/javascript">
          $(".category-description a").click( function(){
            var click_category_id = $(this).data("category_id")
            window.location.href = "update_category.php?category_id="+click_category_id;
          });
          $(".edit a").click( function(){
            var click_category_id = $(this).parent().parent().find(".category-description a").last().data("category_id");
            window.location.href = "update_category.php?category_id="+click_category_id;
          });
          $(".delete a").click( function(){
            var click_category_id = $(this).parent().parent().find(".category-description a").last().data("category_id");
            window.location.href = "delete_category.php?category_id="+click_category_id;
          });
        </script>

      </div>
  </div>

  <?php include_once("$ROOT/UI/javascripts.php"); ?>
</body>

</html>
