<?php
$form_data = NULL;
$form_status = False;
$form_error = "";

$category_id = (int) $_POST["category_id"];
$category_name = $_POST["category_name"];

if( notEmptyString($category_name) ){
  if( $ECOM->isStoreManager() ){

    $success = $ECOM->UpdateCategoryName( $category_id, $category_name );
    if( $success ){
      $form_status = True;
      $form_data = $category_id;
    }else{
      $form_error = $ECOM->message();
    }

  }else{
    $form_error = TRANSLATE("no_tienes_permisos_para_realizar_esta_accion");
  }
}else{
  $form_error = TRANSLATE("falta_el_nombre_de_la_categoria");
}
