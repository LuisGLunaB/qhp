<style media="screen">
  #carousel1.carousel .indicators{
    bottom: 0px !important;
  }
  #carousel1 .fondo-gris{
    background: #3F3D3C;
    background-image: url("images/sliders/fondo.fw.png");
    background-size: 50% auto;
    background-position: center center;
    background-repeat: no-repeat;
  }

  #carousel1 #slide1 img{
    position: absolute;
    right: 5%;
    bottom: 0%;
    height: 90%;
    width: auto;
  }
  #carousel1 #slide1 #logo{
    width: 50%;
    max-width: 450px;
    min-width: 180px;
    position: absolute;
    height: auto;
    top: 25%;
    left: 25%;
  }

  #carousel1 #slide2{
    background-image: url("images/sliders/slide2.jpg");
    background-size: cover;
    background-position: right center;
    background-repeat: no-repeat;
  }
  #carousel1 #slide3{
    background-image: url("images/sliders/slide3.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }
  #carousel1 #slide4{
    background-image: url("images/sliders/slide4.fw.png");
    background-size: cover;
    background-position: right bottom;
    background-repeat: no-repeat;
  }
  #carousel1 #slide5{
    background-image: url("images/sliders/slide5.fw.png");
    background-size: cover;
    background-position: right bottom;
    background-repeat: no-repeat;
  }

  @media screen and (max-width:992px) {
    #carousel1 #slide1 img{ height: 80%; right: -10%;}
    #carousel1 #slide1 #logo{top: 25%;left: 15%;}
	}
	/*MOBILE*/
	@media screen and (max-width:600px) {
    #carousel1 #slide1 img{ height: 80%; right: -10%;}
    #carousel1 #slide1 #logo{top: 20%;left: 5%;}


    @media screen and (orientation : landscape) {
      #carousel1 #slide1 img{ height: 90%; right: -5%;}
      #carousel1 #slide1 #logo{top: 15%;left: 10%;}
    }
  }
</style>


<div id="carousel1" class="carousel carousel-slider center" data-indicators="true">
   <!-- <div class="carousel-fixed-item center">
     <a class="btn waves-effect white grey-text darken-text-2">button</a>
   </div> -->
   <div id="slide1" class="carousel-item fondo-gris" href="#">
     <img src="./images/sliders/slide1.png" alt="">
     <img id="logo" src="./images/logo-blanco.png" alt="">
   </div>

   <div id="slide2" class="carousel-item fondo-gris" href="#"></div>
   <div id="slide3" class="carousel-item fondo-gris" href="#"></div>
   <div id="slide4" class="carousel-item fondo-gris" href="#"></div>
   <div id="slide5" class="carousel-item fondo-gris" href="#"></div>

 </div>

 <script type="text/javascript">

   var TransitionManager1;
   carousel_name1 = "#carousel1.carousel";
   $( carousel_name1 + '.carousel-slider').carousel(
     { fullWidth: true,
       indicators: true,
       duration: 450,
       onCycleTo : function($current_item, dragged) {
          // dragged: undefined(nothing)/true(dragging)/false(bullet click)
          clearTimeout(TransitionManager1);
          TransitionManager1 = setTimeout(function(){
              $( carousel_name1 ).carousel("next");
          }, 6500 );
       }
     }
   );

 </script>
