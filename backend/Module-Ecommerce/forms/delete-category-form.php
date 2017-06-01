<?php
  $SQLCategoryForm = new SQLObject();
  $category_id = $_GET["category_id"];
  $FormValues_dc1 = $SQLCategoryForm->QUERY(
    "SELECT * FROM categories WHERE category_id = :category_id",
     array(":category_id" => $category_id ) )[0];
  $category_children_count = $ECOM->ReadCategoryAllChildrenCount($category_id) ;
?>


<form id="delete-category-form" action="?category_id=<?php echo $category_id; ?>" method="post">
<input type="hidden" name="form" value="delete-category-form">

  <label for="category_name"><?php pTRANSLATE("categorias_padre"); ?></label>
  <?php echo $ECOM->ShowCategoryParents($category_id); ?>
  <label for="category_name"><?php pTRANSLATE("nombre_de_la_categoria"); ?></label>
  <span id="category_name"></span>
  <input type="hidden" name="category_id" value="">

  <div class="form-error"><?php UI_ShowFormError(); ?></div>
  <?php echo UI_FormDeleteButton(); ?>

</form>


<script type="text/javascript">
  var $Form_dc1 = $("#delete-category-form");
  var category_children_count = <?php echo $category_children_count; ?>;

  $("*[name=category_id]", $Form_dc1).val( "<?php echo $category_id; ?>");
  $("#category_name", $Form_dc1).html( "<?php echo $FormValues_dc1['category_name']; ?>" );

  $(".save-button", $Form_dc1).click( function(){
    LockForm($Form_dc1);
    clear_form_error($Form_dc1);

    if(category_children_count==0){
      confirm_message = TRANSLATE("seguro_que_quieres_borrar_esta_categoria");
    }else{
      confirm_message = TRANSLATE("estas_por_borrar_1_categoria_y")+" "+category_children_count+" "+TRANSLATE("de_sus_subcategorias")+". "+TRANSLATE("estas_seguro");
    }

    if( ui_confirm( confirm_message ) ){
      $Form_dc1.submit();
    }

    UnlockForm($Form_dc1);
  });
</script>
