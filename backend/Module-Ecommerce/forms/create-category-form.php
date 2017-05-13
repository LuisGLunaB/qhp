<form id="create-category-form" action="" method="post">
<input type="hidden" name="form" value="create-category-form">

    <label for="category_name"><?php pTRANSLATE("nombre_de_la_categoria"); ?></label>
    <select name="category_name" id="category_name">
      <?php echo NAVIGATE::buildKeyValueOptions( $ECOM->ReadCategoriesByLevel() );?>
    </select>

    <div class="form-error"><?php showFormError(); ?></div>

    <div class="ui-form-buttons">
        <a href="javascript:{}"
          data-position="bottom" data-delay="1" data-tooltip="<?php pTRANSLATE("agregar"); ?>"
          class="save-button btn-floating btn-small tooltipped waves-effect waves-light blue darken-1">
          <i class="material-icons">add</i>
        </a>
    </div>

</form>

<script type="text/javascript">
  var $Form_cc1 = $("#create-category-form");
  $(".save-button", $Form_cc1).click( function(){
    clear_form_error($Form_cc1);
    if( notEmptyFormValue("category_name",$Form_cc1) ){
      $Form_cc1.submit();//Or API call and processing.
    }else{
      set_form_error_TRANSLATE("falta_el_nombre_de_la_categoria",$Form_cc1);
    }
  });

</script>
