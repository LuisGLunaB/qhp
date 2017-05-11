<?php
/* Eviroment: $ECOM, $SQLConnection, $con & defined(ECOMMERCE_ROUTES) */
include_once("./backend/Module-Ecommerce/LOAD_ECOMMERCE_ENVIROMENT.php");

$error_message = "";
if( $SQLConnection->status() ){

    # Create Store
    if( was_form_submitted("create-store-form") ){
      /* Eviroment: $form_data, $form_status, $form_error */
      include_once( ECOMMERCE_ROUTE_processes . "create-store.php" );
      if($form_status){
        // header("Location: index.php?process=create-store&status=1");
      }
    }

  }else{
	$error_message = $SQLConnection->message();
}

// $SectionTitle; $BreadCrumbs; $MenuButtons;
// $Languages;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html lang="es-MX" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>E-Commerce</title>
	<?php include_once( ECOMMERCE_ROUTE . "head-configurations.php" ); ?>
	<style type="text/css">

	</style>
</head>

<body onresize="" onload="">
	<?php include_once("$ROOT/UI/ui-sidebar.php"); ?>
  <div id="ui-main">
    <div id="ui-header">

      <div class="left-align" style="display: inline-block; float: left;">

        <div class="ui-header-breadcrumbs left-align">
          <a href="#">Mi tienda</a> <span>></span>
          <a href="#">Categorías</a>
        </div>

        <div class="ui-header-title left-align"><h1>Agregar Categoría</h1></div>

      </div>

      <div class="ui-main-buttons right-align" style="display: inline-block; float: right;">
        <a data-position="bottom" data-delay="0" data-tooltip="Buscar"
          class="btn-floating btn-medium tooltipped waves-effect waves-light blue darken-1">
          <i class="material-icons">search</i>
        </a>
        <a data-position="bottom" data-delay="0" data-tooltip="Guardar"
          class="btn-floating btn-medium tooltipped waves-effect waves-light blue darken-1">
          <i class="material-icons">add</i>
        </a>
      </div>

    </div>

      <div class="ui-content small row left-align" >
        <!-- <div class="ui-tabs">
          <a href="#" class="ui-tab selected">Información</a>
          <a href="#" class="ui-tab">Categoria</a>
          <a href="#" class="ui-tab ">Precios</a>
          <a href="#" class="ui-tab ">Tamaños</a>
          <a href="#" class="ui-tab ">Precios</a>
        </div> -->
        <?php include_once( ECOMMERCE_ROUTE_forms. "create-store-form.php"); ?>
      </div>

  </div>
  <?php include_once("$ROOT/UI/javascripts.php"); ?>
</body>

</html>
