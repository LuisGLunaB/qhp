<?php

$store_id = $ECOM->getStoreId();
$product_name = $_POST["product_name"];
$product_description = $_POST["product_description"];
$product_price1 = $_POST["product_price1"];
$product_code = $_POST["product_code"];

$product_is_virtual = $_POST["product_is_virtual"];
$product_has_stock = $_POST["product_has_stock"];
$product_is_visible = $_POST["product_is_visible"];
$products_is_active = $_POST["products_is_active"];

$product_image1_file = $_FILES["product_image1"];

$form_data = NULL;
$form_status = False;
$form_error = "";

if( $ECOM->isStoreManager() or $ECOM->hasAdminLevel() ){

  $ECOM->CreateProduct($product_name,$product_description,$product_price1,$store_id,$product_code,$product_is_virtual,$product_has_stock,$product_is_visible,$products_is_active);
  if( $ECOM->status() ){
    $form_status = True;
    $form_data = $ECOM->lastInsertId();

    $ECOM->UpdateProductImage( $product_image1_file, $ECOM->lastInsertId(), $imageNumber=1 );
  }else{
    $form_error = $ECOM->message();
  }

}else{
  $form_error = TRANSLATE("no_tienes_permisos_para_realizar_esta_accion");
}
