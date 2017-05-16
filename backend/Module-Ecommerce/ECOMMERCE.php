<?php
class ECOMMERCE extends SQLObject{
  public $store_id = 1;
  protected $category_fields = ["category_id","category_name","store_id","category_level",
    "parent_category_id","category_url"];
  # Store functions
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
  public function UpdateStoreName( $store_id, $new_store_name ){
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

  public function CreateCategory($category_name,$category_level=1,$parent_category_id=0,$store_id=NULL){
    $store_id = (is_null($store_id)) ? $this->store_id : $store_id;
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
  public function ReadAllCategories($ORDERBY="ASC",$level=1,$max_level=NULL,$store_id=NULL){
    $store_id = ( is_null($store_id) ) ? $this->store_id : $store_id;
    $max_level = ( is_null($max_level) ) ? 3 : 3;
    $level = (int) $level;
    $ORDERBY = ($ORDERBY=="ASC") ? "ASC" : "DESC";

    $binds[":level"] = (int) $level;
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

    $data = $this->QUERY(
     "SELECT
        $SELECTS
      FROM categories AS c$level
        $JOINS
      WHERE
        c$level.category_level = :level
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
  public function ReadCategoryChildren($category_id, $ORDERBY="ASC",$store_id=NULL){
    $store_id = (is_null($store_id)) ? $this->store_id : $store_id;
    $ORDERBY = ($ORDERBY=="ASC") ? "ASC" : "DESC";

    if( $category_id != 0 ){
      $binds[":store_id"] = (int) $store_id;
      $binds[":parent_category_id"] = (int) $category_id;
      $category_fields = implode(",",$this->category_fields);

      $data = $this->QUERY(
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

      return $this->VariableOrErrorvalue( $data );
    }else{
      return $this->ReadCategoriesByLevel( 1 , $ORDERBY, $store_id);
    }

  }

  public function ReadCategory($category_id){
    $binds[":category_id"] = (int) $category_id;

    $data = $this->QUERY(
    "SELECT * FROM categories
     WHERE category_id = :category_id;
    ", $binds);

    return $this->VariableOrErrorvalue( $data[0] );
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
    return $this->ReadCategoryField($category_id,"category_level");
  }

}
