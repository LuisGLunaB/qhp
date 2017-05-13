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

  function clear_form_error(Form){
      $(".form-error",Form).html("");
  }
  function set_form_error(error_message,Form){
      $(".form-error",Form).html(error_message);
  }
  function set_form_error_TRANSLATE(error_message_key,Form){
    set_form_error( TRANSLATE(error_message_key) , Form);
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
