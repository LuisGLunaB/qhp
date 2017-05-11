<?php
define("LANGUAGE","ES");

$TRANSLATIONS["lang"]["ES"] = "es";
$TRANSLATIONS["lang"]["EN"] = "en";

$TRANSLATIONS["add"]["ES"] = "Agregar";
$TRANSLATIONS["add"]["EN"] = "Add";

$TRANSLATIONS["search"]["ES"] = "Buscar";
$TRANSLATIONS["search"]["EN"] = "Search";

$TRANSLATIONS["guardar"]["ES"] = "Guardar";
$TRANSLATIONS["guardar"]["EN"] = "Save";

$TRANSLATIONS["buscar"]["ES"] = "Buscar";
$TRANSLATIONS["buscar"]["EN"] = "Search";

$TRANSLATIONS["tienda"]["ES"] = "Tienda";
$TRANSLATIONS["tienda"]["EN"] = "Store";

$TRANSLATIONS["tiendas"]["ES"] = "Tiendas";
$TRANSLATIONS["tiendas"]["EN"] = "Stores";

$TRANSLATIONS["mi_tienda"]["ES"] = "Mi Tienda";
$TRANSLATIONS["mi_tienda"]["EN"] = "My Store";

$TRANSLATIONS["nombre_de_la_tienda"]["ES"] = "Nombre de la tienda";
$TRANSLATIONS["nombre_de_la_tienda"]["EN"] = "Store name";

$TRANSLATIONS["agregar_tienda"]["ES"] = "Agregar Tienda";
$TRANSLATIONS["agregar_tienda"]["EN"] = "Add Store";

# Categorias
$TRANSLATIONS["cateogoria"]["ES"] = "Categoría";
$TRANSLATIONS["cateogoria"]["EN"] = "Category";

$TRANSLATIONS["categorias"]["ES"] = "Categorías";
$TRANSLATIONS["categorias"]["EN"] = "Categories";

function TRANSLATE($key){
  if( defined("LANGUAGE") ){
    if( array_key_exists("TRANSLATIONS",$GLOBALS) ){
      $TRANSLATIONS = $GLOBALS["TRANSLATIONS"];
      if( array_key_exists($key,$TRANSLATIONS) ){
        $TRANS = $TRANSLATIONS[$key];
        if( array_key_exists(LANGUAGE,$TRANS) ){
          return $TRANS[LANGUAGE];
        }else{
          return "No hay traducción al idioma '".LANGUAGE."'";
        }
      }else{
        return "Esta frase no está en el diccionario de traducciones.";
      }
    }else{
      return "No hay un diccionario de traducciones definido.";
    }
  }else {
    return "No hay un idioma definido.";
  }
}
function pTRANSLATE($key){
  echo TRANSLATE($key);
}
