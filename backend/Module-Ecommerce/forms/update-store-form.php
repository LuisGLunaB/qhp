
<form id="update-store-form" action="" method="post">
<input type="hidden" name="form" value="update-store-form">

    <label for="store_name"><?php pTRANSLATE("nombre_de_la_tienda"); ?></label>
    <input type="text" name="store_name" id="store_name" placeholder="" value="" >
    <input type="hidden" name="store_id" value="">

    <div class="form-error"><?php UI_ShowFormError(); ?></div>
    <?php echo UI_FormEditButton(); ?>

</form>


<script type="text/javascript">
  var $Form_us1 = $("#update-store-form");
  $(".save-button", $Form_us1).click( function(){
    clear_form_error($Form_us1);

    if( notEmptyFormValue("store_name",$Form_us1) ){
      $Form_us1.submit();//Or API call and processing.
    }else{
      set_form_error_TRANSLATE("falta_el_nombre_de_la_tienda",$Form_us1);
    }
  });
</script>
