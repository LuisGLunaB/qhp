<div id="CategoryTree1" >
  <?php ECOMMERCE::ShowTree( $ECOM->ReadCategoryBranches() ); ?>
</div>

<style media="screen">
  #CategoryTree1{
    width: 100%;
    height: auto;
    overflow: auto;
    display: inline-block;
  }
</style>

<script src="<?php echo $ROOT;?>/UI/ui-treemenu.js"></script>
<script type="text/javascript">
  function AlertCategoryId(category_id, category_name, category_level, parent_category_id, category_url, store_id){
    // alert(category_id+"-"+category_name+"("+category_level+")");
  }
  var Treemenu_Onclick = AlertCategoryId;

  $CategoryTree1 = $("#CategoryTree1");
  Treemenu_HideAllBranches($CategoryTree1);
  $(".tree-branch", $CategoryTree1).click( function(){
    $branch = $(this);
    Treemenu_ManageClick( $branch.data("branch_id") );

    Treemenu_Onclick(
      $branch.data("category_id"),
      $branch.data("category_name"),
      $branch.data("category_level"),
      $branch.data("parent_category_id"),
      $branch.data("category_url"),
      $branch.data("store_id")
    );

  });

</script>
