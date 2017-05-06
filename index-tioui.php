<?php
	$ROOT = "."
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html lang="es-MX" xml:lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>

	<title>TIOUI Jewerly</title>
	<meta name="description" content="Descripción" />

	<?php
	include_once("$ROOT/html/metatags-configurations.php");
	include_once("$ROOT/html/metatags-stylesheets.php");
	include_once("$ROOT/html/javascript-files.php");
	include_once("$ROOT/html/tracking-head.php");
	?>

	<!-- CSS SOLO PARA LA PÁGINA ACTUAL  -->
	<style type="text/css">
	</style>

</head>

<body onresize="" onload="">
	<a href="javascript:">
		<img id="display-menu-button" data-activates="slide-out" class="button-collapse" src="<?php echo $ROOT; ?>/images/icons/menu-elegant.png" alt="Display Menu Button">
	</a>
	<?php
	include_once("$ROOT/html/mobile-menu.php");
	//include_once("$ROOT/html/main-menu.php");
	include_once("$ROOT/blocks/index-menu.php");
	include_once("$ROOT/blocks/regular-carousel.php");
	?>


  <div class="container" style="">

  </div>

	<?php
	//include_once("$ROOT/html/footer.php");
	include_once("$ROOT/html/tracking-body.php");
	?>
	<script src="<?php echo $ROOT;?>/javascript/main.js"></script>
</body>

</html>
