<?php
$ROOT = "."; define("ROOT", $ROOT);
include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
include_once( ROOT . "/backend/Module-Ecommerce/ECOMMERCE.php");
/* Eviroment: $SQLConnection, $con */

if( $SQLConnection->status() ){

  $ECOM = new ECOMMERCE();

}else{
	echo $SQLConnection->message();
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html lang="es-MX" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>

	<title>E-Commerce</title>
	<meta name="description" content="Descripción" />
  <link href="<?php echo $ROOT;?>/UI/ui-stylesheet.css" rel="stylesheet" type="text/css" />
	<?php
  	include_once("$ROOT/html/metatags-configurations.php");
  	include_once("$ROOT/html/stylesheets.php");
  	include_once("$ROOT/html/javascript-files.php");
  	// include_once("$ROOT/html/tracking-head.php");
	?>

	<style type="text/css">
	</style>

</head>

<body onresize="" onload="">
	<?php
  $ECOM->ReadAllStores();
	// include_once("$ROOT/html/mobile-menu.php");
	// include_once("$ROOT/html/main-menu.php");
	// include_once("$ROOT/blocks/index-menu.php");
	// include_once("$ROOT/blocks/regular-carousel.php");
	//include_once("$ROOT/html/footer.php");
	// include_once("$ROOT/html/tracking-body.php");
  include_once("$ROOT/UI/ui-sidebar.php");
	?>
  <div id="ui-main">

    <link href="<?php echo $ROOT;?>/UI/ui-stylesheet-header.css" rel="stylesheet" type="text/css" />
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

    <link href="<?php echo $ROOT;?>/UI/ui-stylesheet-content.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $ROOT;?>/backend/Forms/forms.css" rel="stylesheet" type="text/css" />
      <div class="ui-content small row left-align" >

        <!-- <div class="ui-tabs">
          <a href="#" class="ui-tab selected">Información</a>
          <a href="#" class="ui-tab">Categoria</a>
          <a href="#" class="ui-tab ">Precios</a>
          <a href="#" class="ui-tab ">Tamaños</a>
          <a href="#" class="ui-tab ">Precios</a>
        </div> -->

        <form id="create-store-form" class="" action="" method="get">
          <label for="store_name">Nombre de la tienda</label>
          <input type="text" name="store_name" id="store_name" value="">

          <div class="ui-form-buttons">

            <input type="hidden" name="form" value="create-store-form">

            <a href="javascript:{}" onclick="$('#create-store-form').submit();"
              data-position="bottom" data-delay="1" data-tooltip="Guardar"
              class="btn-floating btn-small tooltipped waves-effect waves-light blue darken-1">
              <i class="material-icons">add</i>
            </a>

          </div>

        </form>

      </div>


  </div>


	<script src="<?php echo $ROOT;?>/javascript/main.js"></script>
</body>

</html>
