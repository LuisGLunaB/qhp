<style media="screen">
  .gray-bar{
    width: 140px;
    height: 38px;
    background-color: #353535;
    z-index: 102;
    position: fixed;
    right: 50%;
    left: 50%;
    margin-left: -70px;
    text-align: center;
  }
  .gray-bar img{
    height: 100%;
    padding: 7px;
    opacity: 0.4;
  }

  .gray-bar.top{
    top: 0px;
    -webkit-border-radius: 0 0 15px 15px;
    border-radius: 0 0 15px 15px;
  }

  .gray-bar.bottom{
    bottom: 0px;
    -webkit-border-radius: 15px 15px 0 0;
    border-radius: 15px 15px 0 0;
  }

  .fb-gris img, .inta-gris img{
    width: 40px;
    height: auto;
    position: fixed;
    z-index: 101;
    top: 10px;
    right: 10px;
  }
  .inta-gris img{right: 55px;}
  .logo-index{
    padding: 4px;
    height: 60px;
    width: auto;
  }
  .top-menu{
    background-color: #323031;
    height: 60px;
    width: 100%;
    position: fixed;
    display: inline;
    z-index: 103;
    left: 0px;
    color: white !important;
  }
  .top-menu .item{
    background-color: #323031;
    font-weight: 700;
    color: white !important;
    width: 24%;
    text-align: center;
    font-size: 17px;
    padding-top: 13px;
    padding-bottom: 13px;
    margin-top: 5px;
    margin-bottom: 3px;
    display: inline-block;
    border: 0px;
    border-right: 2px;
    border-color: white;
    border-style: solid;
  }
  .top-menu .item:hover{
    background-color: #252525;
  }
  .top-menu .social img{
    width: 34px;
    height: auto;
    margin-top: 12px;
    padding: 1px;
    opacity: 0.8;
  }
  .top-menu .social img:hover{opacity: 1.0;}
  /*MOBILE*/

  @media screen and (max-width:992px) {
    .gray-bar,.fb-gris img, .inta-gris img{
      display: none;
    }
  }
  @media screen and (max-width:600px) {
    .gray-bar{
      width: 120px;
      height: 34px;
      margin-left: -60px;
    }
    .fb-gris img, .inta-gris img{
      width: 30px;
    }
  }
</style>

<a class="fb-gris" href="#"><img src="./images/icons/fb_gris.png" alt="Facebook Button"></a>
<a class="inta-gris" href="#"><img src="./images/icons/inta_gris.png" alt="Facebook Button"></a>

<div class="row top-menu" style="top: -61px; opacity: 0.0;">
  <div class="col s12 m12 l3 center-align">
    <a href="index.php"><img class="logo-index" src="./images/logo-blanco.png" alt="TIOUI Jewerly"></a>
  </div>
  <div class="col s0 m0 l7">
    <a class="duration-300 waves-effect waves-custom item" href="#">Home</a>
    <a class="duration-300 waves-effect waves-custom item" href="#">Shop</a>
    <a class="duration-300 waves-effect waves-custom item" href="#">Collections</a>
    <a class="duration-300 waves-effect waves-custom item" href="#">Contact Us</a>
  </div>
  <div class="social col s0 m0 l2 right-align">
    <a href="#"><img class="duration-300" src="./images/icons/insta_blanco.png" alt="Intagram account"></a>
    <a href="#"><img class="duration-300" src="./images/icons/fb_blanco.png" alt="Facebook account"></a>
  </div>
</div>

<a onclick="showTopMenu();" href="javascript:" class="gray-bar top"><img src="./images/icons/up.fw.png" alt="Show Upper Menu"></a>
<!-- <a onclick="showBottomMenu();"href="javascript:" class="gray-bar bottom"><img src="./images/icons/down.fw.png" alt="Show Lower Menu"></a> -->

<script type="text/javascript">
  function showTopMenu(){
    $( ".top-menu" ).animate({
      opacity: 1.0,
      top: "0px"
    }, 500);
  }
  function showBottomMenu(){
    alert("down");
  }
</script>
