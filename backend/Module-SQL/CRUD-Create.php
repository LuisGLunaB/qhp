<?php
$insert1_status = False;
$insert1_updated = False;
$insert1_message = "";
$insert1_id = NULL;

if( !isset($insert1_request_type) ){ $insert1_request_type = "post"; }
if( !isset($insert1_action) ){ $insert1_action = basename($_SERVER['PHP_SELF']); }
if( !isset($insert1_onupdate) ){ $insert1_onupdate = NULL; }
if( !isset($insert1_redirect) ){ $insert1_redirect = NULL; }

$_REQ = ($insert1_request_type=="post") ? $_POST : $_GET;

if( key_exists("insert",$_REQ) or key_exists("onupdate",$_REQ) ){
    /* Enviroment: $SQLConnection, $con */
    if( $SQLConnection->status() ){
    	$CRUD = new SQLInsert( $insert1_TableName, $insert1_fields );
      $CRUD->INSERT( $_REQ );
      if( !is_null($insert1_onupdate) ){ $CRUD->ONDUPLICATE( $insert1_onupdate ); }

      $CRUD->execute();
      if( $CRUD->status() ){
          $insert1_status = True;
          $insert1_id = $CRUD->getLastId();
          $insert1_updated = ($insert1_id==0) ? False : True;
          if( !is_null($insert1_redirect) ){
             header("Location: $insert1_redirect");
          }
      }else{
          $insert1_message = $CRUD->message();
      }
    }else{
      $insert1_message = $SQLConnection->message();
    }

    $insert1_message = utf8_decode($insert1_message);

} // If form is submitted procedure END

if($insert1_message != ""){
  $insert1_message .= '<div class="form-error">'.$insert1_message.'</div>';
}
