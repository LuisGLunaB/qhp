<?php
class ECOMMERCE extends SQLObject{
  protected $store_id = 1;
  public $user_level = 4;//0=notLogged,1=StoreVisitor/User,2=Manager,3=Owner,4=Admin,5=SuperAdmin

  protected $category_fields = ["category_id","category_name","store_id","category_level",
    "parent_category_id","category_url"];
  public function getStoreId($method_store_id=NULL){
      if( ! is_null( $method_store_id ) ){
        return $method_store_id;
      }else{
        $instance_store_id = $this->store_id;
        return $instance_store_id;
      }
    }

  public function isStoreManager($store_id=NULL){
    return ( $this->hasStorePermissions($store_id) and $this->hasManagerLevel() );
  }
  public function isStoreOwner($store_id=NULL){
    return ( $this->hasStorePermissions($store_id) and $this->hasOwnerLevel() );
  }
  public function hasStorePermissions($store_id=NULL){
    $store_id = $this->getStoreId($store_id);
    return ( $this->store_id==$store_id );
  }
  public function hasUserLevel($required_user_level=1){
    return ($this->user_level >= $required_user_level);
  }
  public function hasManagerLevel(){
    return $this->hasUserLevel(2);
  }
  public function hasOwnerLevel(){
    return $this->hasUserLevel(3);
  }
  public function hasAdminLevel(){
    return $this->hasUserLevel(4);
  }
  public function hasSuperAdminLevel(){
    return $this->hasUserLevel(5);
  }

  public function SecureProtectedSection(){
    if( $this->hasAccessToProtectedSections() ){
      // Access Granted
    }else{
      header("Location: index.php?process=super-protected-section&status=1");
    }
  }
  public function SecureSuperProtectedSection(){
    if( $this->hasSuperAdminLevel() ){
      // Access Granted
    }else{
      header("Location: index.php?process=super-protected-section&status=1");
    }
  }
  public function hasAccessToProtectedSections(){
    return ($this->isStoreOwner() or $this->hasAdminLevel());
  }

  # STORE functions
  public function CreateStore($store_name){
    $binds[":store_name"] = $store_name;
    $binds[":store_url"] = self::string_to_url($store_name);

    $this->QUERY(
     "INSERT INTO stores (store_name,store_url)
      VALUES (:store_name,:store_url);
     ", $binds );

    return $this->VariableOrErrorvalue( $this->lastInsertId() );
  }
  public function ReadAllStores(){

    $data = $this->QUERY(
     "SELECT * FROM stores
      ORDER BY store_id ASC;
     ");

     return $this->VariableOrErrorvalue( $data );
  }
  public function ReadStore($store_id){
    $binds[":store_id"] = (int) $store_id;

    $data = $this->QUERY(
    "SELECT * FROM stores
     WHERE store_id = :store_id;
    ", $binds);

    return $this->VariableOrErrorvalue( $data[0] );
  }
  public function UpdateStoreName( $new_store_name, $store_id=NULL ){
    $store_id = $this->getStoreId( $store_id );

    $binds[":new_store_name"] = $new_store_name;
    $binds[":new_store_url"] = self::string_to_url($new_store_name);
    $binds[":store_id"] = (int) $store_id;

    $this->QUERY(
     "UPDATE
        stores
      SET
        store_name = :new_store_name,
        store_url = :new_store_url
      WHERE
        store_id = :store_id;
     ", $binds );

    return $this->status();
  }
  public function DeleteStore( $store_id ){
    $binds[":store_id"] = (int) $store_id;

    $this->QUERY(
     "DELETE FROM stores
      WHERE store_id = :store_id;
     ", $binds );

    return $this->status();
  }

  # CATEGORY functions
  public function CreateCategory($category_name,$category_level=1,$parent_category_id=0,$store_id=NULL){
    $store_id = $this->getStoreId($store_id);

    $parent_category_id = ($category_level==1) ? 0 : $parent_category_id;
    $category_level = ($category_level<1) ? 1 : $category_level;

    $binds[":category_name"] = $category_name;
    $binds[":category_url"] = self::string_to_url($category_name);
    $binds[":category_level"] = (int) $category_level;
    $binds[":parent_category_id"] = (int) $parent_category_id;
    $binds[":store_id"] = (int) $store_id;

    $this->QUERY(
     "INSERT INTO categories
        (category_name,category_url,category_level,parent_category_id,store_id)
      VALUES
        (:category_name,:category_url,:category_level,:parent_category_id,:store_id)
     ", $binds );

    return $this->VariableOrErrorvalue( $this->lastInsertId() );
  }
  public function ReadCategoriesAll($parent_category_id=0,$max_level=NULL,$level=1,$ORDERBY="ASC",$store_id=NULL){
    $store_id = $this->getStoreId($store_id);

    $db_max_level = $this->ReadCategoriesMaxLevel($store_id);
    $max_level = ( is_null($max_level) ) ? $db_max_level : $max_level;
    $max_level = min($db_max_level,$max_level);

    $level = (int) min(max(1,$level),$max_level);
    $ORDERBY = ($ORDERBY=="ASC") ? "ASC" : "DESC";
    $children_category_level = (int) ($this->ReadCategoryLevel($parent_category_id) + 1);
    $children_category_level = max($children_category_level,$level);

    $binds[":parent_category_id"] = (int) $parent_category_id;
    $binds[":store_id"] = (int) $store_id;
    $category_fields = $this->category_fields;

    $SELECTS = [];
    for ($i=$level;$i<=$max_level;$i++) {
        $FIELDS = [];
        foreach($category_fields as $field){
          $FIELDS[] = "c$i.$field AS c$i"."_$field";
        }
        $SELECTS[] = implode(",",$FIELDS);
    }
    $SELECTS = implode(",",$SELECTS);

    $JOINS = "";
    for ($i=($level+1);$i<=$max_level;$i++) {
        $h = ($i-1);
        $JOINS .= "LEFT JOIN categories AS c$i ON c$h.category_id = c$i.parent_category_id ";
    }

    $ORDERS = [];
    for ($i=$level;$i<=$max_level;$i++) {
        $ORDERS[] = "c$i.category_name $ORDERBY";
    }
    $ORDERS = implode(",",$ORDERS);

    $is_last = ($children_category_level>$max_level);
    $parent_filter = ($is_last) ?
      "c".($children_category_level-1).".category_id = :parent_category_id " :
      "c$children_category_level.parent_category_id = :parent_category_id ";
    $data = $this->QUERY(
     "SELECT
        $SELECTS
      FROM categories AS c$level
        $JOINS
      WHERE
        $parent_filter
        AND c$level.store_id = :store_id
      ORDER BY
        $ORDERS
      ", $binds);
    return $this->VariableOrErrorvalue( $data );
  }
  public function ReadCategoriesByLevel($category_level=1, $ORDERBY="ASC", $store_id=NULL){
    $store_id = (is_null($store_id)) ? $this->store_id : $store_id;

    $binds[":store_id"] = (int) $store_id;
    $binds[":category_level"] = (int) $category_level;
    $ORDERBY = ($ORDERBY=="ASC") ? "ASC" : "DESC";

    $category_fields = implode(",",$this->category_fields);
    $data = $this->QUERY(
    "SELECT
      $category_fields
     FROM
      categories
     WHERE
      store_id = :store_id
      AND category_level = :category_level
    ORDER BY
      category_name $ORDERBY;
    ", $binds);

    return $this->VariableOrErrorvalue( $data );
  }
  public function ReadCategoryChildren($category_id=0, $ORDERBY="ASC",$store_id=NULL){
    $store_id = (is_null($store_id)) ? $this->store_id : $store_id;
    $ORDERBY = ($ORDERBY=="ASC") ? "ASC" : "DESC";

    if( $category_id != 0 ){
      $binds[":store_id"] = (int) $store_id;
      $binds[":parent_category_id"] = (int) $category_id;
      $category_fields = implode(",",$this->category_fields);

      $ChildrenTable = $this->QUERY(
      "SELECT
        $category_fields
       FROM
        categories
       WHERE
        parent_category_id = :parent_category_id
        AND store_id = :store_id
      ORDER BY
        category_name $ORDERBY;
      ", $binds);
      if( sizeof($ChildrenTable)!=0 ){
        return $this->VariableOrErrorvalue( $ChildrenTable , []);
      }else{
        return [];
      }

    }else{
      return $this->ReadCategoriesByLevel( 1 , $ORDERBY, $store_id);
    }

  }
  public function ReadCategoryChildrenIDs($category_id){
    $ChildrenTable = $this->ReadCategoryChildren($category_id);
    return self::getCategoryIDs($ChildrenTable);
  }
  public function ReadCategoryAllChildrenCount($category_id){
    $count = 0;
    $direct_children = $this->ReadCategoryChildren($category_id);
    foreach( $direct_children as $direct_child ){
      $count++;
      $count +=  $this->ReadCategoryAllChildrenCount( $direct_child["category_id"] );
    }
    return $count;
  }
  public function ReadCategoryChildrenCount($category_id=0){
    $children_count = sizeof( $this->ReadCategoryChildren($category_id) ) ;
    return $children_count;
  }
  public function ReadCategoriesMaxLevel($store_id=NULL){
    $store_id = $this->getStoreId($store_id);
    $store_id = (int) $store_id;

    $data = $this->QUERY(
    "SELECT
      MAX(category_level) AS category_level
     FROM categories
     WHERE store_id = $store_id;
    ", []);

    return $this->VariableOrErrorvalue( $data[0]["category_level"] );
  }
  public function ReadCategoryParents($category_id){
    $Parents = [];
    $keepLooping = True;
    while($keepLooping){
      $CurrentCategoryRow = $this->ReadCategory($category_id);
      array_unshift($Parents, $CurrentCategoryRow);
      $category_id = $CurrentCategoryRow["parent_category_id"];

      $keepLooping = (! ($category_id==0 or is_null($category_id) or sizeof($category_id)==0 ) );
    }
    return $Parents;
  }
  public function ReadCategory($category_id){
    if($category_id!=0){
      $binds[":category_id"] = (int) $category_id;

      $data = $this->QUERY(
      "SELECT * FROM categories
       WHERE category_id = :category_id;
      ", $binds);

      return $this->VariableOrErrorvalue( $data[0] );
    }else{
      $this->ErrorManager->handleError("No Category with id: $category_id" );
      return NULL;
    }

  }
  public function ReadCategoryField($category_id,$field){
    $data = $this->ReadCategory($category_id);
    if( $this->status() ){
      $var = $data["$field"];
    }else{
      $var = NULL;
    }
    return $this->VariableOrErrorvalue( $var );
  }
  public function ReadCategoryLevel($category_id){
    if($category_id==0){
      return 0;
    }else{
      return $this->ReadCategoryField($category_id,"category_level");
    }
  }
  public function ReadCategoryBranches($id=0,$ORDERBY="ASC",$store_id=NULL){
    $data = $this->ReadCategoryChildren($id ,$ORDERBY,$store_id);

    $children = [];
    if(sizeof($data)>0){
      foreach($data as $index => $row){
        $children = self::ReadCategoryBranches( $row["category_id"] ,$ORDERBY,$store_id);
        $data[$index]["children"] = $children;
      }
    }

    return $data; //$data = NULL if no children found.
  }
  public function UpdateCategoryName( $category_id, $category_name ){
    $binds[":category_id"] = $category_id;
    $binds[":category_name"] = $category_name;
    $binds[":category_url"] = self::string_to_url($category_name);

    $this->QUERY(
     "UPDATE
        categories
      SET
        category_name = :category_name,
        category_url = :category_url
      WHERE
        category_id = :category_id;
     ", $binds );

    return $this->status();
  }
  public function DeleteCategory($category_id){
    $binds[":category_id"] = (int) $category_id;
    $this->QUERY(
     "DELETE FROM categories
      WHERE category_id = :category_id;
     ", $binds );
    $success = $this->status();

    if($success){
      $ChildrenIDs = $this->ReadCategoryChildrenIDs( $category_id );
      foreach($ChildrenIDs as $eachChildrenID){
        $this->DeleteCategory( $eachChildrenID );
      }
    }

    return $success;
  }
  public function ShowCategoryParents( $category_id ){
    $parents_span = "";
    $category_parents =  $this->ReadCategoryParents( $category_id ) ;
    foreach($category_parents as $parent){
      $parents_span .= "<span>".TRANSLATE("nivel"). " $parent[category_level] - $parent[category_name] </span> <br>";
    }
    return $parents_span;
  }
  public static function ShowTree($data,$level=1,$children_field="children"){
    if( sizeof($data)>0 and !is_null($data) ){
      foreach($data as $index => $row){
        $row["branch_id"] = $row["category_id"];
        $row["parent_branch"] = $row["parent_category_id"];
        $row["branch_level"] = $level;
        self::ShowTreeBranch($row);
        self::ShowTree( $row["$children_field"] , ($level+1) );
      }
    }
  }
  public static function ShowTreeBranch($branch){
    // $branch is an associative array of 1 row of data.
    $b = $branch;
    $attributes = self::Associative2DataAttributes($b);
    echo "
    <div $attributes class='tree-branch branch-id-$b[branch_id] parent-branch-$b[parent_branch] branch-level-$b[branch_level]' >
      <a class='waves-effect' href='javascript:{}'>$b[category_name]</a>
    </div>
    ";
  }
  public static function getCategoryIDs($CategoryTable){
    $ids = [];
    foreach($CategoryTable as $row){
      $ids[] = $row["category_id"];
    }
    return $ids;
  }
  public static function CategoryTableRow2Links($table,$glue = " > "){
    $links_array = [];

    foreach($table as $row){
      $id = $row["category_id"];
      $name = $row["category_name"];
      if( !is_null($name) ){
        $links_array[] =
        "<a href='javascript:{}' data-category_id='$id' data-category_name='$name'>$name</a>";
      }
    }

    return implode( $glue, $links_array );
  }
  public static function CategoryRow2CategoryTable($row){
    $max_level = self::CategoryRowGetLevel($row);
    $category_table = [];
    for ($level=1;$level<=$max_level; $level++){
      $category_table[] = self::CategoryRow2CategoryAssocAtLevel($row,$level);
    }
    return $category_table;
  }
  public static function CategoryRow2CategoryAssocAtLevel($row,$level=1){
    $i = $level;
    if( array_key_exists("c$i"."_category_id",$row) ){
      $id = $row["c$i"."_category_id"];
      $name = $row["c$i"."_category_name"];
      return array( "category_id"=>$id, "category_name"=>$name );
    }else {
      return "";
    }
  }
  public static function CategoryRowGetLevel($row){
    $i = 1;
    while(True){
      if( array_key_exists("c$i"."_category_id",$row) ){
        $i++;
      }else {
        $i--;
        break;
      }
    }
    return $i;
  }

  # PRODUCT function
  public function CreateProduct($product_name,$product_description,$product_price1,$store_id,$product_code="",$product_is_virtual=False,$product_has_stock=True,$product_is_visible=True,$products_is_active=True){
    # Obligatory parameters
    $binds[":product_name"] = $product_name;
    $binds[":product_description"] = $product_description;
    $binds[":product_price1"] = $product_price1;
    $binds[":store_id"] = (int) $store_id;
    # Optional parameters
    $binds[":product_code"] = $product_code;
    $binds[":product_is_virtual"] = (bool) $product_is_virtual;
    $binds[":product_has_stock"] = (bool) $product_has_stock;
    $binds[":product_is_visible"] = (bool) $product_is_visible;
    $binds[":products_is_active"] = (bool) $products_is_active;

    $InsertProductQuery = self::getINSERTSINGLE_query("products",$binds);
    $this->QUERY( $InsertProductQuery , $binds );

    return $this->VariableOrErrorvalue( $this->lastInsertId() );
  }
  public function UpdateProductImage($ImageFile,$product_id,$imageNumber){
    $imageNumber = (int) $imageNumber;
    $product_id = (int) $product_id;

    $directory = ROOT . "/images_ecommerce/products/";
    $name = "product_image$imageNumber"."_$product_id";

    $FILE = SaveEcommerceImage($ImageFile,$directory,$name,$fitSize=800);

    if( $FILE->status() ){
      return $this->UpdateProductImageName($product_id, $FILE->getNameAndExtension() ,$imageNumber);
    }else{
      return False;
    }
  }
  protected function UpdateProductImageName($product_id,$image_name,$imageNumber){
    $imageNumber = (int) $imageNumber;
    $product_image = "product_image$imageNumber";
    $binds[":$product_image"] = $image_name;
    $binds[":product_id"] = (int) $product_id;

    $this->QUERY(
     "UPDATE products
      SET $product_image = :$product_image
      WHERE product_id = :product_id;
     ", $binds );

    return $this->status();
  }


  protected static function getINSERTSINGLE_query($tableName,$binds){
    list($FieldsPlaceholder,$ValuesPlaceholder) = self::getFieldsAndValuesPlaceholdersFromBinds($binds);
    return "INSERT INTO $tableName ($FieldsPlaceholder) VALUES ($ValuesPlaceholder);";
  }
  protected static function getFieldsAndValuesPlaceholdersFromBinds($binds){
    $FieldsPlaceholder = self::getFieldsPlaceholderFromBinds($binds);
    $ValuesPlaceholder = self::getValuesPlaceholderFromBinds($binds);
    return [$FieldsPlaceholder,$ValuesPlaceholder];
  }
  protected static function getFieldsPlaceholderFromBinds($binds){
    return implode(",", self::getFieldsFromBinds($binds) );
  }
  protected static function getValuesPlaceholderFromBinds($binds){
    return implode(",",array_keys($binds));
  }
  protected static function getFieldsFromBinds($binds){
    $fields = [];
    foreach($binds as $bind => $ignore_me){
      $fields[] = ltrim($bind, ':');
    }
    return $fields;
  }
  protected static function Associative2DataAttributes($Assoc){
    $HTMLAttributes = "";
    foreach($Assoc as $key => $value ){
      if(!is_array($value)){
        $HTMLAttributes .= " data-$key='$value' ";
      }
    }
    return $HTMLAttributes;
  }
}

function SaveEcommerceImage($ImageFile,$directory,$name,$fitSize=800,$compressionRate=75){
  $FILE = new ImageObject( $ImageFile );
  if( $FILE->status() ){
    $FILE->SaveTo($directory, ("$name.".$FILE->getExtension()) );
    $FILE->Convert2JPEG();

    $FILE->CreateThumbnail( 500 , $compressionRate, "large" );
    $FILE->CreateThumbnail( 300 , $compressionRate, "medium" );
    $FILE->CreateThumbnail( 100, $compressionRate, "small" );

    $FILE->FitTo( $fitSize );
    $FILE->Compress( $compressionRate );
  }
  return $FILE;
}
