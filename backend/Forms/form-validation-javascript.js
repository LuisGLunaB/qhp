  function isTextBlank( inputName ){
    Input = getValue(inputName);
    return (
         (Input == "" )
      || (Input == " ")
      || (Input == ".")
      || (Input == "-")
    );
  }

  function getValue( inputName ){
    return document.getElementsByName(inputName)[0].value;
  }

  function ValuesMatch( inputName1, inputName2 ){
    return (getValue(inputName1) == getValue(inputName2));
  }

  function ValuesDontMatch( inputName1, inputName2 ){
    return (getValue(inputName1) != getValue(inputName2));
  }

  function setError( inputName ){
    alert("a");
    document.getElementById( inputName ).className = "error";
    alert("b");
  }

  function ValidateNewUser(){
    var submit = true;
    if( isTextBlank("email") ) {
      submit = false;
      setError("email");
    }
    if( isTextBlank("password") ) { submit = false; }
    if( isTextBlank("password-verification") ) { submit = false; }
    if( ValuesDontMatch("password","password-verification") ) { submit = false; }

    if( submit ){
      document.getElementById("new-user-form").submit();
    }else{
      alert("error");
    }
  }
