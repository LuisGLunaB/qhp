function Treemenu_HideAllBranches($Tree){
  $(".tree-branch", $Tree).hide();
  $(".branch-level-1", $Tree).show();
}
function Treemenu_HideBranchesAtLevel(level,$Tree){
  $(".branch-level-"+level, $Tree).hide();
}
function Treemenu_hasChildren(branch_id,$Tree){
  count = $(".parent-branch-"+branch_id, $Tree).length;
  return (count != 0);
}
function Treemenu_ShowBranchesAtLevel(level,$Tree){
  $(".branch-level-"+level, $Tree).show();
}
function Treemenu_ShowChildBranchesOf(branch_id,$Tree){
  if( Treemenu_hasChildren(branch_id,$Tree) ){
    $(".parent-branch-"+branch_id, $Tree).show();
  }
}
function Treemenu_HideChildBranchesOf(branch_id,$Tree){

  if( Treemenu_hasChildren(branch_id,$Tree) ){
    $DirectChildren = $(".parent-branch-"+branch_id, $Tree);
    $DirectChildren.hide();
    $DirectChildren.each( function() {
      Treemenu_HideChildBranchesOf( $(this).data("branch_id") ,$Tree);
    });
  }

}
function Treemenu_ShowParentsOf(branch_id,$Tree){
  $parent = $(".branch-id-"+branch_id, $Tree);
  if( $parent.length >0 ){
    parent_id = $parent.data("parent_branch");
    if( parent_id != 0){
      $(".branch-id-"+parent_id, $Tree).show();
      Treemenu_ShowChildBranchesOf(parent_id);
      Treemenu_ShowParentsOf(parent_id,$Tree);
    }
  }

}
function Treemenu_isBranchOpen(branch_id,$Tree){
  return $(".parent-branch-"+branch_id, $Tree).first().is(":visible");
}
function Treemenu_isBranchClosed(branch_id,$Tree){
  return (! Treemenu_isBranchOpen(branch_id,$Tree) );
}
function Treemenu_ManageClick(branch_id,$Tree){
  if( Treemenu_isBranchClosed( branch_id, $Tree) ){
    Treemenu_HideAllBranches( $Tree );
    Treemenu_ShowParentsOf( branch_id, $Tree); //Keeps ALL Parents and Brothers visible
    Treemenu_ShowChildBranchesOf( branch_id, $Tree ); //Show Direct Children
  }else{
    Treemenu_HideChildBranchesOf( branch_id, $Tree); //Hide all children and grandchildren
  }
}
