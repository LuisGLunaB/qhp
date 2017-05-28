<?php
  function UI_FormButton($tooltip_translation_key,$icon,$class="save-button"){
    $tooltip = TRANSLATE($tooltip_translation_key);
    $class .= " $icon ";
    return '
      <div class="ui-form-buttons">
          <a href="javascript:{}"
            data-position="bottom" data-delay="1" data-tooltip="'.$tooltip.'"
            class="'.$class.' btn-floating btn-small tooltipped waves-effect waves-light blue darken-1">
            <i class="material-icons">'.$icon.'</i>
          </a>
      </div>
    ';
  }

  function UI_FormEditButton($class="save-button"){
    return UI_FormButton("guardar_cambios","mode_edit",$class);
  }

  function UI_ShowFormError($form_error=NULL){
    if(is_null($form_error)){
      if( array_key_exists( "form_error", $GLOBALS) ){
        echo $GLOBALS["form_error"];
      }else{
        // NULL was given, no form has been called.
      }
    }else{
      if($form_error != ""){
        echo $form_error;
      }else{
        // There is was no error with the form.
      }
    }
  }

?>
