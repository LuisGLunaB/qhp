<?php
class ECOMMERCE extends SQLObject{
  public $store_id = 1;

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
  public function ReadCategoriesByLevel($category_level=1, $ORDERBY="ASC", $store_id=NULL){
    $store_id = (is_null($store_id)) ? $this->store_id : $store_id;

    $binds[":store_id"] = (int) $store_id;
    $binds[":category_level"] = (int) $category_level;
    $ORDERBY = ($ORDERBY=="ASC") ? "ASC" : "DESC";

    $data = $this->QUERY(
    "SELECT
      category_id,category_name
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
}
