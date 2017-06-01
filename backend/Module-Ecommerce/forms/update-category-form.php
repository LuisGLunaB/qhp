<?php
  $SQLCategoryForm = new SQLObject();
  $category_id = $_GET["category_id"];
  $FormValues_uc1 = $SQLCategoryForm->QUERY(
    "SELECT * FROM categories WHERE category_id = :category_id",
     array(":category_id" => $category_id ) )[0];
?>


<form id="update-category-form" action="?category_id=<?php echo $category_id; ?>" method="post">
<input type="hidden" name="form" value="update-category-form">

  <label for="category_name"><?php pTRANSLATE("categorias_padre"); ?></label>
  <?php echo $ECOM->ShowCategoryParents($category_id); ?>
  <label for="category_name"><?php pTRANSLATE("nombre_de_la_categoria"); ?></label>
  <input type="text" name="category_name" id="category_name" placeholder="" value="" >
  <input type="hidden" name="category_id" value="">

  <div class="form-error"><?php UI_ShowFormError(); ?></div>
  <?php echo UI_FormEditButton(); ?>

</form>


<script type="text/javascript">
  var $Form_uc1 = $("#update-category-form");

  $("*[name=category_id]", $Form_uc1).val( "<?php echo $category_id; ?>");

  $category_name = $("*[name=category_name]", $Form_uc1);
  $category_name.val( "<?php echo $FormValues_uc1['category_name']; ?>");
  $category_name.focus();

  $(".save-button", $Form_uc1).click( function(){
    LockForm($Form_uc1);
    clear_form_error($Form_uc1);

      if( notEmptyFormValue("category_name",$Form_uc1) ){
        $Form_uc1.submit();
      }else{
        set_form_error_TRANSLATE("falta_el_nombre_de_la_categoria",$Form_uc1);
      }

    UnlockForm($Form_uc1);
  });
</script>
