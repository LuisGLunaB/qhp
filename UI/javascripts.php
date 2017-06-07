<script src="<?php echo $ROOT;?>/backend/Forms/form-validation-javascript.js"></script>
<script src="<?php echo $ROOT;?>/UI/ui-javascript.js"></script>
<script src="<?php echo $ROOT;?>/javascript/main.js"></script>

<script type="text/javascript">
  <?php
   if( ! $hasAccessToProtectedSections ){
     echo '$(".protected-section").remove();';
   }
   if( ! $isSuperUser ){
     echo '$(".super-protected-section").remove();';
   }
  ?>
</script>
