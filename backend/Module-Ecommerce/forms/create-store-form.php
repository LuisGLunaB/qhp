<form id="create-store-form" action="" method="post">
<input type="hidden" name="form" value="create-store-form">

    <label for="store_name"><?php pTRANSLATE("nombre_de_la_tienda"); ?></label>
    <input type="text" name="store_name" id="store_name" value="" placeholder="<?php pTRANSLATE("mi_nueva_tienda"); ?>">

    <div class="form-error"><?php UI_ShowFormError(); ?></div>

    <div class="ui-form-buttons">
        <a href="javascript:{}"
          data-position="bottom" data-delay="1" data-tooltip="<?php pTRANSLATE("agregar"); ?>"
          class="save-button btn-floating btn-small tooltipped waves-effect waves-light blue darken-1">
          <i class="material-icons">add</i>
        </a>
    </div>

</form>

<script type="text/javascript">
  var $Form_cs1 = $("#create-store-form");
  $(".save-button", $Form_cs1).click( function(){
    clear_form_error($Form_cs1);

    if( notEmptyFormValue("store_name",$Form_cs1) ){
      $Form_cs1.submit();//Or API call and processing.
    }else{
      set_form_error_TRANSLATE("falta_el_nombre_de_la_tienda",$Form_cs1);
    }
  });

</script>
