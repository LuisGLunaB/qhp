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

$TRANSLATIONS["agregar"]["ES"] = "Agregar";
$TRANSLATIONS["agregar"]["EN"] = "Add";

$TRANSLATIONS["buscar"]["ES"] = "Buscar";
$TRANSLATIONS["buscar"]["EN"] = "Search";

$TRANSLATIONS["tienda"]["ES"] = "Tienda";
$TRANSLATIONS["tienda"]["EN"] = "Store";

$TRANSLATIONS["tiendas"]["ES"] = "Tiendas";
$TRANSLATIONS["tiendas"]["EN"] = "Stores";

$TRANSLATIONS["ver_todos"]["ES"] = "Ver todos";
$TRANSLATIONS["ver_todos"]["EN"] = "See All";

$TRANSLATIONS["ver_todas"]["ES"] = "Ver todas";
$TRANSLATIONS["ver_todas"]["EN"] = "See All";

$TRANSLATIONS["mi_tienda"]["ES"] = "Mi Tienda";
$TRANSLATIONS["mi_tienda"]["EN"] = "My Store";

$TRANSLATIONS["nombre_de_la_tienda"]["ES"] = "Nombre de la tienda";
$TRANSLATIONS["nombre_de_la_tienda"]["EN"] = "Store name";

$TRANSLATIONS["agregar_tienda"]["ES"] = "Agregar Tienda";
$TRANSLATIONS["agregar_tienda"]["EN"] = "Add Store";

# Categorias
$TRANSLATIONS["categoria"]["ES"] = "Categoría";
$TRANSLATIONS["categoria"]["EN"] = "Category";

$TRANSLATIONS["categorias"]["ES"] = "Categorías";
$TRANSLATIONS["categorias"]["EN"] = "Categories";

$TRANSLATIONS["nombre_de_la_categoria"]["ES"] = "Nombre de la Categoría";
$TRANSLATIONS["nombre_de_la_categoria"]["EN"] = "Category name";

$TRANSLATIONS["nueva_categoria"]["ES"] = "Nueva categoría";
$TRANSLATIONS["nueva_categoria"]["EN"] = "New category";

$TRANSLATIONS["elegir_categoria_padre"]["ES"] = "Elegir categoría padre (opcional)";
$TRANSLATIONS["elegir_categoria_padre"]["EN"] = "Select parent category (optional)";

$TRANSLATIONS["categoria_padre"]["ES"] = "Categoría Padre";
$TRANSLATIONS["categoria_padre"]["EN"] = "Parent Category";

$TRANSLATIONS["nivel"]["ES"] = "Nivel";
$TRANSLATIONS["nivel"]["EN"] = "Level";

$TRANSLATIONS["falta_el_nombre_de_la_tienda"]["ES"] = "* Falta el nombre de la tienda.";
$TRANSLATIONS["falta_el_nombre_de_la_tienda"]["EN"] = "* Store name is missing.";

$TRANSLATIONS["falta_el_nombre_de_la_categoria"]["ES"] = "* Falta el nombre de la categoría.";
$TRANSLATIONS["falta_el_nombre_de_la_categoria"]["EN"] = "* Category name is missing.";

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
