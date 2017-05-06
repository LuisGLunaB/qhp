<style>
  .slide-0,.slide-1,.slide-2,.slide-3,.slide-4{
    background-size: cover;
    background-repeat: no-repeat;
    background-position: right bottom;
  }

  .slide-0{background-image: url("./images/sliders/slide-bg-0.png");}
  .slide-1{background-image: url("./images/sliders/slide-bg-1.png");}
  .slide-2{background-image: url("./images/sliders/slide-bg-2.png");}
  .slide-3{background-image: url("./images/sliders/slide-bg-3.png");}
  .slide-4{background-image: url("./images/sliders/slide-bg-4.png");}

  .carousel{
    background-color: #E3E3E3;
  }
  .indicator-item.active{
    background-color: #395794 !important;
  }
  .letra-blanca .first-text, .letra-blanca .second-text{
    text-shadow: 0 0 10px #787878;
  }
  .first-text, .second-text, .carousel-button{
    margin-top: 8px !important;
    margin-bottom: 8px !important;
  }
  .letra-blanca{
    color: white !important;
  }
  .letra-azul{
    color: #395794 !important;
  }

  .carousel-container{
    display: inline-block;
    top: 30%;
    position: relative;
    margin: 20px;
  }
  .carousel-container.left-align{
    left: -20%;
  }
  .carousel-container.right-align{
    right: -20%;
  }

  .first-text{
    font-size: 50px !important;
    line-height: 50px;
    font-weight: 700;
    margin: 0px;
    display: inline-block;
    clear: none;
  }

  .second-text{
    font-size: 22px !important;
    line-height: 22px;
    padding: 0px !important;
  }

  .carousel-button{
    text-align: center;;
    display: inline-block;
    font-size: 15px !important;
    font-weight: 700;
    min-width: 130px;
    color: white;
    text-decoration: none;
    padding: 16px !important;
    background-color: #395794;
  }
  .carousel-button:hover{
    background-color: #0d2861;
  }

  .arrow{
    background-color: rgba(0,0,0,0.4);
    bottom: 130px;
    height: 130px;
    width: 40px;
    position: absolute;
    display: block;
  }
  .arrow img{
    top: 55px;
    display: inline-block;
    position: relative;
    width: 20px;
    height: auto;
  }

  .arrow:hover{
    background-color: rgba(0,0,0,0.7);
  }

  @media screen and (max-width: 992px){
    .first-text{
      font-size: 38px !important;
      line-height: 38px;
    }

    .second-text{
      font-size: 22px !important;
      line-height: 22px;
    }
    .carousel-button{
      font-size: 13px !important;
      min-width: 100px;
      padding: 14px !important;
    }

    .carousel-container.left-align{left: -5%;}
    .carousel-container.right-align{right: -5%;}
    @media screen and (orientation : landscape) {
      .carousel-container{
        top: 35%;
      }
    }
  }

  @media screen and (max-width: 600px){
    .slide-0,.slide-1,.slide-2,.slide-3,.slide-4{
    }
    .carousel-button{
      font-size: 11px !important;
      min-width: 80px;
      padding: 12px !important;
    }

    .first-text{
      font-size: 26px !important;
      line-height: 26px;
    }

    .second-text{
      font-size: 14px !important;
      line-height: 14px;
    }
    .carousel-container.left-align{left: 0% !important;}
    .carousel-container.right-align{right: 0% !important;;}

    @media screen and (orientation : landscape) {
      .carousel-container{
        top: 8%;
      }
      .arrow{
        bottom: 10px;
      }
    }
  }
</style>

<div class="carousel carousel-slider center" style="height:100%;" data-indicators="true">

  <div class="carousel-fixed-item">
    <a class="arrow" onclick="clearTimeout(TransitionManager);CarouselPrev();" style="left: 0px;" href="#">
      <img src="<?php echo $ROOT; ?>/images/icons/left.png" alt="Flecha Izquierda del carrusel">
    </a>
    <a class="arrow" onclick="clearTimeout(TransitionManager);CarouselNext();" style="right: 0px;" href="#">
      <img src="<?php echo $ROOT; ?>/images/icons/right.png" alt="Flecha Izquierda del carrusel">
    </a>
  </div>

  <div class="slide-0 carousel-item" href="#">
    <div class="carousel-container center-align letra-blanca" style="">
      <p class="first-text fade-out-to-front duration-200">DR. HECTOR G. MIRAMONTES</p>
      <p class="second-text fade-out-to-front duration-200">FACIAL REJUVENATION CENTRE</p>
      <a class="carousel-button fade-out-to-front duration-200" href="#">ABOUT ME</a>
    </div>
  </div>

  <div class="slide-1 carousel-item" href="#">
    <div class="carousel-container left-align letra-azul" style="left:-11%;">
      <p class="first-text fade-out-to-front duration-200">DISCOVER OUR FACELIFTS</p>
      <p class="second-text fade-out-to-front duration-200">BROWSE OUR BEFORE & AFTER SOUNDSLIDES</p>
      <a class="carousel-button fade-out-to-front duration-200" href="#">DISCOVER</a>
    </div>
  </div>

  <div class="slide-2 carousel-item" href="#">
    <div class="carousel-container left-align letra-azul" style="left:-14%;">
      <p class="first-text fade-out-to-front duration-200">PERFECTION IN REACH</p>
      <p class="second-text fade-out-to-front duration-200">JAW AND NECKLIFT SURGERY</p>
      <a class="carousel-button fade-out-to-front duration-200" href="#">REACH</a>
    </div>
  </div>

  <div class="slide-3 carousel-item" href="#">
    <div class="carousel-container left-align letra-blanca" style="left:-31%;">
      <p class="first-text fade-out-to-front duration-200">OH MY!</p>
      <p class="second-text fade-out-to-front duration-200">OUR EYES HAVE IT</p>
      <a class="carousel-button fade-out-to-front duration-200" href="#">SEE MORE</a>
    </div>
  </div>

  <div class="slide-4 carousel-item" href="#">
    <div class="carousel-container left-align letra-azul" style="left:-7%;">
      <p class="first-text fade-out-to-front duration-200">SEND US YOUR HEAD SHOTS</p>
      <p class="second-text fade-out-to-front duration-200">GET AN ONLINE EVALUATION</p>
      <a class="carousel-button fade-out-to-front duration-200" href="#">GET A QUOTE</a>
    </div>
  </div>

</div>

<script type="text/javascript">

  $(document).ready(function(){$('.carousel').carousel();});
  setSliderHeight();
  var TransitionManager;
  window.slideNumber = -1;
  window.slideTotal = 5;
  CarouselMainLoop( 4000 );


  function setSliderHeight(){
    if( $(".carousel.carousel-slider").length ) {
      body_height = $(window).height();
      header_height = $( "header" ).height();
      $(".carousel.carousel-slider").carousel({fullWidth: true});
      $(".carousel.carousel-slider").attr("style", "height : " + (body_height-header_height) + "px");
    }
  }

  function CarouselPrev(){
    window.slideNumber = window.slideNumber - 1;
    if( window.slideNumber < 0 ){ window.slideNumber = window.slideTotal-1; }
    FadeInAnimations();
    $('.carousel').carousel('set', window.slideNumber);
  }
  function CarouselNext(){
    window.slideNumber = window.slideNumber + 1;
    if( window.slideNumber >= window.slideTotal ){ window.slideNumber = 0; }
    FadeInAnimations();
    $('.carousel').carousel('set', window.slideNumber );
  }

  function FadeInAnimations(){
    Slide = window.slideNumber;
    Showing = "no-transformation";
    FadeOutAnimation = "fade-out-to-front";
    //replaceClass( $(".first-text"), Showing, FadeOutAnimation );
    //replaceClass( $(".second-text"), Showing, FadeOutAnimation );
    //replaceClass( $(".carousel-button"), Showing, FadeOutAnimation );

    var TimeBetweenSteps = 300;
    setTimeout(function(){
      setTimeout(function(){
        replaceClass( $(".slide-" + Slide + " .first-text"), FadeOutAnimation, Showing );
      }, TimeBetweenSteps * 2 );

      setTimeout(function(){
        replaceClass( $(".slide-" + Slide + " .second-text"), FadeOutAnimation, Showing );
      }, TimeBetweenSteps * 3 );

      setTimeout(function(){
        replaceClass( $(".slide-" + Slide + " .carousel-button"), FadeOutAnimation, Showing );
      }, TimeBetweenSteps * 4 );
    }, 200 );

  }

  function CarouselMainLoop( duration ){
    CarouselNext();
    TransitionManager = setTimeout(function(){
      CarouselMainLoop( duration );
    }, duration );
  }

</script>
