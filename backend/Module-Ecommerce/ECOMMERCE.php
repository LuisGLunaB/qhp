<?php
class ECOMMERCE extends SQLObject{
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

  
}
