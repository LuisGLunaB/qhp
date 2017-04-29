<?php
$ROOT = "."; define("ROOT", $ROOT);
require_once( ROOT . "/backend/Loaders/LOADMODULE_SQL.php" );

$insert1_operation = "onupdate";//insert or onupdate
$insert1_TableName = "productos";
$insert1_fields = ["id","ancho","alto","rin"];
$insert1_onupdate = ["ancho","alto","rin"]; //optional, No ONUPDATE;

$insert1_form_name = "productos-form.php";
$insert1_request_type = "post"; //optional, post,
// $insert1_redirect = "insert.php"; //optional, NULL.

// $insert1_action = "insert.php"; //optional, current file.

include_once( ROOT . "/backend/Module-SQL/CRUD-Create.php");
/* Eviroment: $con, $new_user_status, $new_user_message, $new_user_id, $NewUserForm*/
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Insert</title>
    <link rel="stylesheet" href="./backend/Forms/forms.css">
  </head>
  <body>
    <?php
      include_once (ROOT . "/backend/Forms/$insert1_form_name");
    ?>
  </body>
</html>
