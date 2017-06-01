<?php
  $SQLStoreForm = new SQLObject();
  $FormValues_ds1 = $SQLStoreForm->QUERY(
    "SELECT * FROM stores WHERE store_id = :store_id",
     array(":store_id" => $_GET["store_id"]) )[0];
?>


<form id="delete-store-form" action="?store_id=<?php echo $_GET["store_id"]; ?>" method="post">
<input type="hidden" name="form" value="delete-store-form">

    <label for="store_name"><?php pTRANSLATE("nombre_de_la_tienda"); ?></label>
    <span id="store_name"><?php echo $FormValues_ds1["store_name"]; ?></span>
    <input type="hidden" name="store_id" value="<?php echo $FormValues_ds1["store_id"]; ?>">

    <div class="form-error"><?php UI_ShowFormError(); ?></div>
    <?php echo UI_FormDeleteButton(); ?>

</form>


<script type="text/javascript">
  var $Form_ds1 = $("#delete-store-form");

  $(".save-button", $Form_ds1).click( function(){
    LockForm($Form_ds1);
    clear_form_error($Form_ds1);

      if( ui_confirm( TRANSLATE("seguro_que_quieres_borrar_esta_tienda") ) ){
        $Form_ds1.submit();
      }

    UnlockForm($Form_ds1);
  });
</script>
