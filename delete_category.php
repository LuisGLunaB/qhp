<?php
include_once("./backend/Module-Ecommerce/LOAD_ECOMMERCE_ENVIROMENT.php");

$error_message = "";
if( $SQLConnection->status() ){
    $ECOM->SecureProtectedSection();
    # Delete Category
    if( was_form_submitted("delete-category-form") ){
      include_once( ECOMMERCE_ROUTE_processes . "delete-category.php" );
      if($form_status){
        header("Location: categories.php?process=delete-category&status=1&form_data=$form_data");
      }
    }

  }else{
	$error_message = $SQLConnection->message();
}

# UI Navegation:
$SectionTitle = "eliminar_categoria";
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
      <div class="ui-content small row left-align protected-section" >

          <?php include_once( ECOMMERCE_ROUTE_forms. "delete-category-form.php"); ?>

      </div>
  </div>
  <?php include_once("$ROOT/UI/javascripts.php"); ?>
</body>

</html>
