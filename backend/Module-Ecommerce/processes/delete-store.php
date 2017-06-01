<?php
$form_data = NULL;
$form_status = False;
$form_error = "";

$store_id = (int) $_POST["store_id"];

if( $ECOM->hasSuperAdminLevel() ){

  $success = $ECOM->DeleteStore( $store_id );
  if( $success ){
    $form_status = True;
    $form_data = $store_id;
  }else{
    $form_error = $ECOM->message();
  }

}else{
  $form_error = TRANSLATE("no_tienes_permisos_para_realizar_esta_accion");
}
