<?php
  $parent_category_id = (int) (array_key_exists("parent_category_id",$_GET)) ? $_GET["parent_category_id"] : 0;
  $category_level = ($parent_category_id==0) ? 0 : $ECOM->ReadCategoryLevel($parent_category_id);

  $category_options = $ECOM->ReadCategoryChildren( $parent_category_id );
  $category_options = NAVIGATE::buildKeyValueOptions( $category_options );

?>
<?php


// DISPLAY::asTable( $ECOM->ReadCategoryChildren(0) );
// DISPLAY::asTable( $ECOM->ReadCategoriesAll() );
?>
<form id="create-category-form" action="" method="post">

    <input type="hidden" name="form" value="create-category-form">
    <input type="hidden" id="category_level" name="category_level" value="<?php echo ($category_level+1); ?>">
    <input type="hidden" id="parent_category_id" name="parent_category_id" value="<?php echo $parent_category_id; ?>">

    <label for="parent_category"><?php pTRANSLATE("elegir_categoria_padre"); ?></label>
    <select name="parent_category" id="parent_category">
      <?php echo $category_options; ?>
    </select>

    <label for="category_name">
      <?php echo TRANSLATE("nueva_categoria")." (".TRANSLATE("nivel")." ".($category_level+1).")"; ?>
    </label>
    <input type="text" name="category_name" id="category_name" value="" placeholder="<?php pTRANSLATE("nombre_de_la_categoria"); ?>">

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

  var $parent_category_id = $("#parent_category", $Form_cc1);
  $parent_category_id.change( function(){
    // Unable form function.
    LockForm($Form_cc1);
    window.location.href = ( "./create_category.php?parent_category_id=" + this.value );
  });

  $(".save-button", $Form_cc1).click( function(){
    clear_form_error($Form_cc1);
    UnlockForm($Form_cc1);

    if( notEmptyFormValue("category_name",$Form_cc1) ){
      $Form_cc1.submit();//Or API call and processing.
    }else{
      UnlockForm($Form_cc1);
      set_form_error_TRANSLATE("falta_el_nombre_de_la_categoria",$Form_cc1);
    }

  });

</script>
