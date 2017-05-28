<?php
define("LANGUAGE","ES");

$TRANSLATIONS["lang"]["ES"] = "es";
$TRANSLATIONS["lang"]["EN"] = "en";

$TRANSLATIONS["editar"]["ES"] = "Editar";
$TRANSLATIONS["editar"]["EN"] = "Edit";

$TRANSLATIONS["eliminar"]["ES"] = "Eliminar";
$TRANSLATIONS["eliminar"]["EN"] = "Delete";

$TRANSLATIONS["add"]["ES"] = "Agregar";
$TRANSLATIONS["add"]["EN"] = "Add";

$TRANSLATIONS["search"]["ES"] = "Buscar";
$TRANSLATIONS["search"]["EN"] = "Search";

$TRANSLATIONS["guardar"]["ES"] = "Guardar";
$TRANSLATIONS["guardar"]["EN"] = "Save";

$TRANSLATIONS["guardar_cambios"]["ES"] = "Guardar cambios";
$TRANSLATIONS["guardar_cambios"]["EN"] = "Save changes";

$TRANSLATIONS["agregar"]["ES"] = "Agregar";
$TRANSLATIONS["agregar"]["EN"] = "Add";

$TRANSLATIONS["buscar"]["ES"] = "Buscar";
$TRANSLATIONS["buscar"]["EN"] = "Search";

$TRANSLATIONS["tienda"]["ES"] = "Tienda";
$TRANSLATIONS["tienda"]["EN"] = "Store";

$TRANSLATIONS["tiendas"]["ES"] = "Tiendas";
$TRANSLATIONS["tiendas"]["EN"] = "Stores";

$TRANSLATIONS["ver_todos"]["ES"] = "Ver todos";
$TRANSLATIONS["ver_todos"]["EN"] = "See all";

$TRANSLATIONS["ver_todas"]["ES"] = "Ver todas";
$TRANSLATIONS["ver_todas"]["EN"] = "See all";

$TRANSLATIONS["mi_tienda"]["ES"] = "Mi Tienda";
$TRANSLATIONS["mi_tienda"]["EN"] = "My Store";

$TRANSLATIONS["nombre_de_la_tienda"]["ES"] = "Nombre de la Tienda";
$TRANSLATIONS["nombre_de_la_tienda"]["EN"] = "Store name";

$TRANSLATIONS["agregar_tienda"]["ES"] = "Agregar Tienda";
$TRANSLATIONS["agregar_tienda"]["EN"] = "Add Store";

$TRANSLATIONS["nueva_tienda"]["ES"] = "Nueva Tienda";
$TRANSLATIONS["nueva_tienda"]["EN"] = "New Store";

$TRANSLATIONS["editar_tienda"]["ES"] = "Editar Tienda";
$TRANSLATIONS["editar_tienda"]["EN"] = "Edit Store";


## TOAST
$TRANSLATIONS["create-store-toast"]["ES"] = "Tienda Creada con éxito.";
$TRANSLATIONS["create-store-toast"]["EN"] = "Store Created successfully.";

$TRANSLATIONS["update-store-toast"]["ES"] = "Tienda Actualizada con éxito.";
$TRANSLATIONS["update-store-toast"]["EN"] = "Store Updated successfully.";

# Categorias
$TRANSLATIONS["categoria"]["ES"] = "Categoría";
$TRANSLATIONS["categoria"]["EN"] = "Category";

$TRANSLATIONS["categorias"]["ES"] = "Categorías";
$TRANSLATIONS["categorias"]["EN"] = "Categories";

$TRANSLATIONS["nombre_de_la_categoria"]["ES"] = "Nombre de la Categoría";
$TRANSLATIONS["nombre_de_la_categoria"]["EN"] = "Category name";

$TRANSLATIONS["nueva_categoria"]["ES"] = "Nueva Categoría";
$TRANSLATIONS["nueva_categoria"]["EN"] = "New Category";

$TRANSLATIONS["elegir_categoria_padre"]["ES"] = "Elegir Categoría padre (opcional)";
$TRANSLATIONS["elegir_categoria_padre"]["EN"] = "Select parent Category (optional)";

$TRANSLATIONS["categoria_padre"]["ES"] = "Categoría Padre";
$TRANSLATIONS["categoria_padre"]["EN"] = "Parent Category";

$TRANSLATIONS["ninguna"]["ES"] = "Ninguna";
$TRANSLATIONS["ninguna"]["EN"] = "None";

$TRANSLATIONS["categorias_padre"]["ES"] = "Categorías Padre";
$TRANSLATIONS["categorias_padre"]["EN"] = "Parent Categories";

$TRANSLATIONS["menu_de_categorias"]["ES"] = "Menú de Categorías";
$TRANSLATIONS["menu_de_categorias"]["EN"] = "Categories menu";

$TRANSLATIONS["nivel"]["ES"] = "Nivel";
$TRANSLATIONS["nivel"]["EN"] = "Level";

$TRANSLATIONS["falta_el_nombre_de_la_tienda"]["ES"] = "* Falta el nombre de la Tienda.";
$TRANSLATIONS["falta_el_nombre_de_la_tienda"]["EN"] = "* Store name is missing.";

$TRANSLATIONS["mi_nueva_tienda"]["ES"] = "Mi nueva Tienda";
$TRANSLATIONS["mi_nueva_tienda"]["EN"] = "My new Store";


$TRANSLATIONS["falta_el_nombre_de_la_categoria"]["ES"] = "* Falta el nombre de la Categoría.";
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
