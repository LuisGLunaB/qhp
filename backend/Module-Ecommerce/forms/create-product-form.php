<form id="create-product-form" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="form" value="create-product-form">

    <!-- IMAGEN -->
    <div style="width: 100%; text-align: center;">
      <label><?php pTRANSLATE("imagen_principal"); ?></label>
      <label for="product_image1" class="product_image1 fileimage1"></label>
      <input class="file" type="file" name="product_image1" id="product_image1">
    </div>

    <!-- NOMBRE -->
    <label for="product_name"><?php pTRANSLATE("nombre_del_producto"); ?></label>
    <input type="text" name="product_name" id="product_name" value=""
      placeholder="<?php pTRANSLATE("mi_nuevo_producto"); ?>">

    <!-- DESCRIPCION  -->
    <label for="product_description"><?php pTRANSLATE("descripcion"); ?></label>
    <textarea name="product_description" id="product_description" rows="3"></textarea>

    <!-- PRECIO  -->
    <label for="product_price1"><?php pTRANSLATE("precio"); ?></label>
    <input class="special money" type="text" name="product_price1" id="product_price1" value="">

    <!-- CODIGO  -->
    <label for="product_code"><?php pTRANSLATE("codigo_interno"); ?></label>
    <input class="special code" type="text" name="product_code" id="product_code" value=""
      placeholder="(<?php pTRANSLATE("optional"); ?>)">

    <!-- HIDDEN  -->
    <input type="hidden" name="product_is_virtual" value="0">
    <input type="hidden" name="product_has_stock" value="1">
    <input type="hidden" name="product_is_visible" value="1">
    <input type="hidden" name="products_is_active" value="1">

    <!-- BUTTON & ERRORS  -->
    <div class="form-error"><?php UI_ShowFormError(); ?></div>
    <?php echo UI_FormAddButton(); ?>

</form>
<script type="text/javascript">

  $image1 = $("#product_image1");
  $image1.change(function(){
    File_ShowThumbnail(this);
  });

  $price = $("#product_price1");
  $price.change( function(){
    var price = onlyNumericCharacters( $price.val() );
    $price.val( price );
  });

</script>

<script type="text/javascript">

  var $Form_cp1 = $("#create-product-form");

  $(".save-button", $Form_cp1).click( function(){
    clear_form_error($Form_cp1);

    var formError = "";
    if ( ! File_isImage($image1.get(0)) ) formError += TRANSLATE("el_archivo_tiene_que_ser_una_imagen") + "<br>";
    if ( ! notEmptyFormValue("product_name",$Form_cp1) ) formError += TRANSLATE("falta_el_nombre_del_producto") + "<br>";
    if ( ! notEmptyFormValue("product_description",$Form_cp1) ) formError += TRANSLATE("falta_la_descripcion_del_producto") + "<br>";
    if ( ! notEmptyFormValue("product_price1",$Form_cp1) ) formError += TRANSLATE("falta_el_precio") + "<br>";
    if ( Number($price.val()) <= 0.0 ) formError += TRANSLATE("el_precio_debe_ser_mayor_a_cero") + "<br>";

    if( formError == "" ){
      $Form_cp1.submit();
    }else{
      set_form_error( formError, $Form_cp1 );
    }

  });

</script>
