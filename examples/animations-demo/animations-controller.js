
// Listener function to play around: Click on the H1 elements in order to change their type on the fly.
$(document.body).on('click', 'h1' ,function(){
  toggleAnimation( $(this), "fade-out-to-front" );
});


// Configure and start animation loop
durationBetweenElements = 300;
durationBetweenAnimations = 2000;
AnimationMainLoop( durationBetweenElements, durationBetweenAnimations);

function AnimationMainLoop( durationBetweenElements, durationBetweenAnimations){
    Class1 = "no-transformation";
    Class2 = "fade-out-to-front";

    setTimeout(function(){
        toggleClasses( $("#element1"), Class1 , Class2);
        toggleClasses( $("#element4"), Class1 , Class2);
        toggleClasses( $("#element7"), Class1 , Class2);
    }, durationBetweenElements * 1 );

    setTimeout(function(){
        toggleClasses( $("#element2"), Class1 , Class2);
        toggleClasses( $("#element5"), Class1 , Class2);
        toggleClasses( $("#element8"), Class1 , Class2);
    }, durationBetweenElements * 2 );

    setTimeout(function(){
        toggleClasses( $("#element3"), Class1 , Class2);
        toggleClasses( $("#element6"), Class1 , Class2);
        toggleClasses( $("#element9"), Class1 , Class2);
    }, durationBetweenElements * 3 );

    /*
    You could also do:
      toggleAnimation( $("#element1"), Class2);
    Instead of:
      toggleClasses( $("#element1"), Class1 , Class2);
    */

  //Call yourself for an eternal loop.
  setTimeout(function(){
    AnimationMainLoop( durationBetweenElements, durationBetweenAnimations );
  }, durationBetweenAnimations );

}
