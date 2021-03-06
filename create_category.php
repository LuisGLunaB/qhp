<?php
/* Eviroment: $ECOM, $SQLConnection, $con & defined(ECOMMERCE_ROUTES) */
include_once("./backend/Module-Ecommerce/LOAD_ECOMMERCE_ENVIROMENT.php");

$error_message = "";
if( $SQLConnection->status() ){

    # Create Store
    if( was_form_submitted("create-category-form") ){
      /* Eviroment: $form_data, $form_status, $form_error */
      include_once( ECOMMERCE_ROUTE_processes . "create-category.php" );
      if($form_status){
        header("Location: categories.php?process=create-category&lastid=$form_data&status=1");
      }
    }

  }else{
	$error_message = $SQLConnection->message();
}

# UI Navegation:
$SectionTitle = "nueva_categoria";
$BreadCrumbs = [
  ["index.php","mi_tienda"],
  ["categories.php","categorias"]
];
$MenuButtons = [];
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
        <div class="row">
            <div class="col s6 m6 l6">
              <?php include_once( ECOMMERCE_ROUTE_blocks. "category-tree.php"); ?>
            </div>
            <div class="col s6 m6 l6">
              <?php include_once( ECOMMERCE_ROUTE_forms. "create-category-form.php"); ?>
            </div>
        </div>

      </div>

  </div>
  <?php include_once("$ROOT/UI/javascripts.php"); ?>
</body>

</html>
