
  function notEmptyFormValue(name,Form){
    return (! isEmptyFormValue(name,Form) );
  }
  function isEmptyFormValue(name,Form){
    return isEmptyString( GetFormValue(name,Form) );
  }
  function GetFormValue(name,Form){
    return $("*[name="+name+"]",Form).val();
  }

  function isEmptyString(Input){
    Input = String(Input);
    Input = Input.trim();
    return (
         Input==""
      || Input==" "
      || Input == "."
      || Input == "-"
      || Input.length == 0
      || Input == null
      || Input == undefined
    );
  }

  function LockForm($Form){
    $Form.css( "pointer-events", "none" );
    $Form.css( "opacity", "0.3" );
  }
  function UnlockForm($Form){
    $Form.css( "pointer-events", "auto" );
    $Form.css( "opacity", "1.0" );
  }

  function clear_form_error(Form){
      $(".form-error",Form).html("");
  }
  function set_form_error(error_message,Form){
      $(".form-error",Form).html(error_message);
  }
  function set_form_error_TRANSLATE(error_message_key,Form){
    set_form_error( TRANSLATE(error_message_key) , Form);
  }

  function isNumericCharacter( character ){
    character = character.toString();
    var NumericCharacters = ["0","1","2","3","4","5","6","7","8","9",".","-"];
    return ( NumericCharacters.indexOf( character ) > -1 );
  }

  function onlyNumericCharacters(text){
    text = text.toString();
    var OnlyNumeric = [];
    for (var i = 0, len = text.length; i < len; i++) {
      var char = text[i];
      if( isNumericCharacter( char ) ){
        OnlyNumeric.push( char );
      }
    }
    value = Number(OnlyNumeric.join(""));
    if ( value == 0.0 ) value = "";
    return value;
  }

  function File_GetExtension(input) {
    if (input.files && input.files[0]) {
        var extension = input.files[0].name.split('.').pop().toLowerCase();
        return extension;
    }
  }
  function File_GetFilename(input) {
    if (input.files && input.files[0]) {
        var filename = input.files[0].name;
        return filename;
    }
  }
  function File_isExtention(input,ValidExtensions){
    var extension = File_GetExtension(input);
    return ( ValidExtensions.indexOf(extension) > -1);
  }
  function File_isImage(input){
    var ImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    return File_isExtention(input,ImageExtensions);
  }
  function File_ShowThumbnail(input) {
    clase =  $(input).attr('id');
    $('.'+clase).html( File_GetFilename(input) );

    if( File_isImage(input) ){
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
              image_source = e.target.result;
              $('.'+clase).css("background-image", "url(" + image_source + ")");
          }
          reader.readAsDataURL(input.files[0]);
      }
    }else{
      var extension = File_GetExtension(input);
      $('.'+clase).css("background-image", 'url("./UI/icons/' + extension + '-icon.fw.png" )');
    }
  }

  //
  // function isTextBlank( inputName ){
  //   Input = getValue(inputName);
  //   return isEmptyString(Input);
  // }
  // function getValue( inputName ){
  //   return document.getElementsByName(inputName)[0].value;
  // }
  // function ValuesMatch( inputName1, inputName2 ){
  //   return (getValue(inputName1) == getValue(inputName2));
  // }
  // function ValuesDontMatch( inputName1, inputName2 ){
  //   return (getValue(inputName1) != getValue(inputName2));
  // }
  // function setError( inputName ){
  //   document.getElementById( inputName ).className = "error";
  // }
  // function ValidateNewUser(){
  //   var submit = true;
  //   if( isTextBlank("email") ) {
  //     submit = false;
  //     setError("email");
  //   }
  //   if( isTextBlank("password") ) { submit = false; }
  //   if( isTextBlank("password-verification") ) { submit = false; }
  //   if( ValuesDontMatch("password","password-verification") ) { submit = false; }
  //
  //   if( submit ){
  //     document.getElementById("new-user-form").submit();
  //   }else{
  //     alert("error");
  //   }
  // }
