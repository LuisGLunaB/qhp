<?php
// API Request Parsing Code

$form_data = NULL;
$form_status = False;
$form_error = "";

extract($_POST);

if( notEmptyString($category_name) ){
  $ECOM->CreateCategory( $category_name );
  if( $ECOM->status() ){
    $form_status = True;
    $form_data = $ECOM->lastInsertId();
  }else{
    $form_error = $ECOM->message();
  }
}else{
  $form_error = TRANSLATE("falta_el_nombre_de_la_categoria");
}

/* Eviroment: $form_data, $form_status, $form_error */

// API Request Return Code
