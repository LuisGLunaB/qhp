<link href="<?php echo $ROOT;?>/backend/Forms/forms.css" rel="stylesheet" type="text/css" />

<form id="create-store-form" action="" method="post">
  <label for="store_name">Nombre de la tienda</label>
  <input type="text" name="store_name" id="store_name" value="">

  <div class="ui-form-buttons">
    <input type="hidden" name="form" value="create-store-form">

    <?php showFormError(); ?>

    <a href="javascript:{}" onclick="$('#create-store-form').submit();"
      data-position="bottom" data-delay="1" data-tooltip="Guardar"
      class="btn-floating btn-small tooltipped waves-effect waves-light blue darken-1">
      <i class="material-icons">add</i>
    </a>

  </div>
</form>
