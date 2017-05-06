<?php
$ROOT = "."; define("ROOT", $ROOT);
// include_once( ROOT . "/backend/Loaders/LOADMODULE_ALL.php");
/* Eviroment: $SQLConnection, $con */
//
// if( $SQLConnection->status() ){
//   $User = UserObject::FullLoginWithCookieLevel($required_level=1,$redirect_url=NULL);
// 	print_r($User->UserData);
//
//   $SQL = new SQLObject();
//   DISPLAY::asTable(  $SQL->QUERY("SELECT user_id, email AS 'correo_electronico' FROM users;")  );
//
// }else{
// 	echo $SQLConnection->message();
// }

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
          <a href="#">Categorías</a> <span>></span>
          <a href="#">Agregar Categoría</a>
        </div>

        <div class="ui-header-title left-align">
          <h1>Agregar Categoría</h1>
        </div>
      </div>

      <div class="ui-main-buttons right-align" style="display: inline-block; float: right;">
        <a class="btn-floating btn-medium waves-effect waves-light blue darken-1"><i class="material-icons">search</i></a>
        <a class="btn-floating btn-medium waves-effect waves-light blue darken-1"><i class="material-icons">add</i></a>
      </div>

    </div>
    <style media="screen">
      .ui-content{
        color: black;
        background-color: white;
        border-radius: 8px;
        box-shadow: 3px 3px 6px 3px rgba(0,0,0,0.17);
        /*border: 1px solid rgb(200,200,200);*/
      }
      .ui-content.medium{
        width: 60%;
        margin-top:7%;
      }
      .ui-tabs{
        width: 100%;
        /*top: -25px;*/
        position: relative;
        border-bottom: 3px double #a9a9f7;
      }
      .ui-tab{
        font-weight: 400;
        width: 19%;
        border-radius: 8px 8px 0 0;
        background-color: white;
        color: #232323;
        display: inline-block;
        font-size: 16px;
        padding: 2px;
        margin: 0px;
        text-align: center;
        border: 1px solid #bfbfbf;
        border-bottom: 0px;
        background: rgb(255,255,255);
        background: -moz-linear-gradient(top, rgba(255,255,255,1) 52%, rgba(238,238,238,1) 89%);
        background: -webkit-linear-gradient(top, rgba(255,255,255,1) 52%,rgba(238,238,238,1) 89%);
        background: linear-gradient(to bottom, rgba(255,255,255,1) 52%,rgba(238,238,238,1) 89%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#eeeeee',GradientType=0 );
      }
      .ui-tab:hover{
        margin-top: -6px;
        padding-top: 8px;
      }
      .ui-tab.selected{
        background: rgb(0,0,0);
        color: white;
      }
      .ui-tab.good{
        background: rgb(64, 204, 94);
        color: white;
      }
      .ui-tab.bad{
        background: rgb(204, 64, 64);
        color: white;
      }
      .ui-tab.blocked{
        background: rgb(220, 220, 220);
        color: #a5a5a5;
      }
    </style>

      <div class="ui-content medium row left-align" >

        <div class="ui-tabs">
          <a href="#" class="ui-tab selected">Información</a>
          <a href="#" class="ui-tab">Categoria</a>
          <a href="#" class="ui-tab ">Precios</a>
          <a href="#" class="ui-tab ">Tamaños</a>
          <a href="#" class="ui-tab ">Precios</a>
        </div>

        <div class="" style="padding: 10px;">
          Texto
        </div>
      </div>


  </div>


	<script src="<?php echo $ROOT;?>/javascript/main.js"></script>
</body>

</html>
