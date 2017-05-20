<style media="screen">
  #CategoryTree1{
    width: 100%;
    height: auto;
    overflow: auto;
    display: inline-block;
    padding: 6px;
  }
  #CategoryTree1 .category-tree-title{
    font-size: 18px !important;
    margin-top: 4px;
    margin-bottom: 4px;
  }
  #CategoryTree1 .tree-branch{
    display: inline-block;
    width: 100%;
    border: 1px solid #545454;
    border-top: 0px;
  }
  #CategoryTree1 .tree-branch a{
    display: inline-block;
    width: 100%;
    opacity: 0.94;
  }
  #CategoryTree1 .tree-branch a:hover{
    opacity: 1.00;
  }
  #CategoryTree1 .branch-level-1 a{
    font-size: 18px;
    font-weight: 700;
    padding-top: 4px;
    padding-bottom: 4px;
    padding-left: 10px;
    color: white;
    background-color: black;
  }
  #CategoryTree1 .branch-level-1 a.waves-effect .waves-ripple {
    background-color: rgba(100, 100, 100, 0.75);
  }
  #CategoryTree1 .branch-level-2 a{
    font-size: 16px;
    font-weight: 500;
    padding-top: 3px;
    padding-bottom: 3px;
    padding-left: 25px;
    color: white;
    background-color: rgb(100, 100, 100);
  }
  #CategoryTree1 .branch-level-2 a.waves-effect .waves-ripple {
    background-color: rgba(140, 140, 140, 0.75);
  }
  #CategoryTree1 .branch-level-3 a{
    font-size: 14px;
    font-weight: 400;
    padding-top: 2px;
    padding-bottom: 2px;
    padding-left: 40px;
    color: white;
    background-color: rgb(140, 140, 140);
  }
  #CategoryTree1 .branch-level-3 a.waves-effect .waves-ripple {
    background-color: rgba(220, 220, 220, 0.75);
  }
  #CategoryTree1 .branch-level-4 a{
    font-size: 13px;
    font-weight: 400;
    padding-top: 2px;
    padding-bottom: 2px;
    padding-left: 55px;
    color: black;
    background-color: rgb(220, 220, 220);
  }
  #CategoryTree1 .branch-level-4 a.waves-effect .waves-ripple {
    background-color: rgba(255, 255, 255, 0.75);
  }
  #CategoryTree1 .branch-level-5 a{
    font-size: 12px;
    font-weight: 400;
    padding-top: 2px;
    padding-bottom: 2px;
    padding-left: 70px;
    color: black;
    background-color: rgb(255, 255, 255);
  }
  #CategoryTree1 .branch-level-5 a.waves-effect .waves-ripple {
    background-color: rgba(220, 220, 220, 0.75);
  }
</style>

<div id="CategoryTree1" >
  <div class="ui-header-title left-align">
        <h1 class="category-tree-title">
          <?php pTRANSLATE("menu_de_categorias"); ?>
        </h1>
  </div>
  <?php ECOMMERCE::ShowTree( $ECOM->ReadCategoryBranches() ); ?>
</div>

<!-- tree-branch branch-id-5 parent-branch-0 branch-level-1 -->


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
