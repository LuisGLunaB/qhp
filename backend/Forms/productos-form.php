
<?php
  $buttonName = ($insert1_operation=="insert") ? "Agregar" : "Guardar";
  $_DAT = ($insert1_operation=="insert") ? "" : $_REQ;
?>
<form class="" action="<?php echo $insert1_action; ?>" method="<?php echo $insert1_request_type; ?>">
  <?php
    // echo SQLNavigate::InputSelectValueLabels("productos","id","marca",1001);
    // echo SQLNavigate::InputSelectDistinct("productos","marca","Pirelli");

    if( $insert1_operation=="onupdate"){
      echo SQLNavigate::InputDisabledText("id",3253);
      echo SQLNavigate::InputHiddenText("id",3253);
    }
    echo SQLNavigate::InputSelectDistinct("productos","ancho",$_DAT);
    echo SQLNavigate::InputSelectDistinct("productos","alto",$_DAT);
    echo SQLNavigate::InputSelectDistinct("productos","rin",$_DAT);

  ?>
  <input type="hidden" name="form" value="<?php echo substr($insert1_form_name,0,-4); ?>">
  <input class="button" name="<?php echo $insert1_operation; ?>" type="submit" value="<?php echo $buttonName; ?>">
  <?php echo $insert1_message; ?>
</form>
