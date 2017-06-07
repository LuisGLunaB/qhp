<?php
include_once("./backend/Module-Ecommerce/LOAD_ECOMMERCE_ENVIROMENT.php");

$error_message = "";
if( $SQLConnection->status() ){
    $ECOM->SecureProtectedSection();
    # Update Store
    if( was_form_submitted("update-store-form") ){
      include_once( ECOMMERCE_ROUTE_processes . "update-store.php" );
      if($form_status){
        header("Location: stores.php?process=update-store&status=1&form_data=$form_data");
      }
    }

  }else{
	$error_message = $SQLConnection->message();
}

# UI Navegation:
$SectionTitle = "editar_tienda";
$BreadCrumbs = [
  ["index.php","mi_tienda"]
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

          <?php include_once( ECOMMERCE_ROUTE_forms. "update-store-form.php"); ?>

      </div>
  </div>
  <?php include_once("$ROOT/UI/javascripts.php"); ?>
</body>

</html>
