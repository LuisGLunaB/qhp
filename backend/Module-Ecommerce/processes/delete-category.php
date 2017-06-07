<?php
$form_data = NULL;
$form_status = False;
$form_error = "";

$category_id = (int) $_POST["category_id"];

if( $ECOM->hasOwnerLevel() or $ECOM->hasAdminLevel() ){

  $success = $ECOM->DeleteCategory( $category_id );
  if( $success ){
    $form_status = True;
    $form_data = $category_id;
  }else{
    $form_error = $ECOM->message();
  }

}else{
  $form_error = TRANSLATE("no_tienes_permisos_para_realizar_esta_accion");
}
