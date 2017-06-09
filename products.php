<?php
/* Eviroment: $ECOM, $SQLConnection, $con & defined(ECOMMERCE_ROUTES) */
include_once("./backend/Module-Ecommerce/LOAD_ECOMMERCE_ENVIROMENT.php");

$error_message = "";
if( $SQLConnection->status() ){

  $productos_AS = TRANSLATE("producto");
  $editar_AS = TRANSLATE("editar");
  $eliminar_AS = TRANSLATE("eliminar");

  $store_list_query =
  "SELECT
    store_name AS '$productos_AS',
    LINK( CONCAT('update_store.php?store_id=',store_id),'$editar_AS','action') AS '$editar_AS',
    LINK( CONCAT('delete_store.php?store_id=',store_id),'$eliminar_AS','action super-protected-section') AS '$eliminar_AS'
   FROM
    stores
   ORDER BY
    store_name ASC
   ;
  ";

  $SQL = new SQLObject();
  $store_list = $SQL->QUERY( $store_list_query );
}else{
	$error_message = $SQLConnection->message();
}

# UI Navegation:
$SectionTitle = "productos";
$BreadCrumbs = [
  ["index.php","mi_tienda"]
];
$MenuButtons = [
  ["create_product.php","add"]
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
      <div class="ui-content medium row left-align protected-section" >

        <?php
          DISPLAY::asTable( $store_list, "ui-table");
        ?>

      </div>
  </div>
  <?php include_once("$ROOT/UI/javascripts.php"); ?>
</body>

</html>
