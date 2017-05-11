<?php
include_once("$ROOT/html/metatags-configurations.php");
include_once("$ROOT/html/stylesheets.php");
include_once("$ROOT/UI/ui-stylesheets.php");
include_once("$ROOT/html/javascript-files.php");
?>

<script type="text/javascript">
  var LANGUAGE = '<?php echo LANGUAGE; ?>';
  var TRANSLATIONS = JSON.parse('<?php echo json_encode($TRANSLATIONS); ?>');

  //Funciones: TRANSLATE y pTRANSLATE;
</script>
