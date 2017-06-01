<?php
  $SQLStoreForm = new SQLObject();
  $FormValues_us1 = $SQLStoreForm->QUERY(
    "SELECT * FROM stores WHERE store_id = :store_id",
     array(":store_id" => $_GET["store_id"]) )[0];
?>


<form id="update-store-form" action="?store_id=<?php echo $_GET["store_id"]; ?>" method="post">
<input type="hidden" name="form" value="update-store-form">

    <label for="store_name"><?php pTRANSLATE("nombre_de_la_tienda"); ?></label>
    <input type="text" name="store_name" id="store_name" placeholder="" value="" >
    <input type="hidden" name="store_id" value="">

    <div class="form-error"><?php UI_ShowFormError(); ?></div>
    <?php echo UI_FormEditButton(); ?>

</form>


<script type="text/javascript">
  var $Form_us1 = $("#update-store-form");

  <?php DumpFormValues( $FormValues_us1,'$Form_us1'); ?>
  $("*[name=store_name]", $Form_us1).focus();

  $(".save-button", $Form_us1).click( function(){
    LockForm($Form_us1);
    clear_form_error($Form_us1);

      if( notEmptyFormValue("store_name",$Form_us1) ){
        $Form_us1.submit();//Or API call and processing.
      }else{
        set_form_error_TRANSLATE("falta_el_nombre_de_la_tienda",$Form_us1);
      }

    UnlockForm($Form_us1);
  });
</script>
