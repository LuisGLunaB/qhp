<?php
// API Request Parsing Code

$form_data = NULL;
$form_status = False;
$form_error = "";

$store_id = (int) $_POST["store_id"];
$store_name = $_POST["store_name"];

if( notEmptyString($store_name) ){
  if( $ECOM->isStoreOwner($store_id) or $ECOM->hasAdminLevel() ){

    $success = $ECOM->UpdateStoreName( $store_name, $store_id );
    if( $success ){
      $form_status = True;
      $form_data = $store_id;
    }else{
      $form_error = $ECOM->message();
    }

  }else{
    $form_error = TRANSLATE("no_tienes_permisos_para_realizar_esta_accion");
  }
}else{
  $form_error = TRANSLATE("falta_el_nombre_de_la_tienda");
}

/* Eviroment: $form_data, $form_status, $form_error */

// API Request Return Code
