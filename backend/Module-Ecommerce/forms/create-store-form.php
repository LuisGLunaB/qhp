<form id="create-store-form" action="" method="post">
<input type="hidden" name="form" value="create-store-form">

    <label for="store_name"><?php pTRANSLATE("nombre_de_la_tienda"); ?></label>
    <input type="text" name="store_name" id="store_name" value="">

    <div class="form-error"><?php showFormError(); ?></div>

    <div class="ui-form-buttons">
        <a href="javascript:{}" onclick="$('#create-store-form').submit();"
          data-position="bottom" data-delay="1" data-tooltip="<?php pTRANSLATE("guardar"); ?>"
          class="btn-floating btn-small tooltipped waves-effect waves-light blue darken-1">
          <i class="material-icons">add</i>
        </a>
    </div>

</form>
