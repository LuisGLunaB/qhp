
function replaceClass(jQueryElement,oldClass,NewClass){
  jQueryElement.removeClass( oldClass );
  jQueryElement.addClass( NewClass );
}

function toggleClasses(jQueryElement,Class1,Class2){

  if( jQueryElement.hasClass(Class1) ){
    replaceClass( jQueryElement, Class1, Class2 );
  }else{
    replaceClass( jQueryElement, Class2, Class1 );
  }

}

function toggleAnimation(jQueryElement,AnimationClass){
  toggleClasses( jQueryElement, "no-transformation", AnimationClass );
}
