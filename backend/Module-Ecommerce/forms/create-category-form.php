<style media="screen">

  #parent_categories_list{
    color: gray;
    font-weight: 500;
  }
  #parent_categories_list a:hover{
    color: red !important;
    text-decoration: underline;
  }
</style>

<form id="create-category-form" action="" method="post">
    <input type="hidden" name="form" value="create-category-form">

    <label for="">
      <?php pTRANSLATE("categoria_padre"); ?>
    </label>
    <div id="parent_categories_list">
      <?php pTRANSLATE("ninguna"); ?>
    </div>

    <label for="category_name">
      <?php echo TRANSLATE("nueva_categoria")." (".TRANSLATE("nivel")." <span id='category_level_count'>1</span>)"; ?>
    </label>
    <input type="text" name="category_name" id="category_name" value="" placeholder="<?php pTRANSLATE("nombre_de_la_categoria"); ?>">
    <input type="hidden" id="category_level" name="category_level" value="1">
    <input type="hidden" id="parent_category_id" name="parent_category_id" value="0">


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
  var $Form_cc1 = $("#create-category-form");

  $(".save-button", $Form_cc1).click( function(){
    clear_form_error($Form_cc1);
    LockForm($Form_cc1);

    if( notEmptyFormValue("category_name",$Form_cc1) ){
      $Form_cc1.submit();//Or API call and processing.
    }else{
      UnlockForm($Form_cc1);
      set_form_error_TRANSLATE("falta_el_nombre_de_la_categoria",$Form_cc1);
    }

  });

</script>
