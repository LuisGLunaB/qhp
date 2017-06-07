
<div id="ui-sidebar">

  <div id="store-menu" class="ui-sidebar-item waves-effect protected-section">
    <a class="ui-sidebar-button" href="stores.php">
      <img class="ui-icon" src="<?php echo $ROOT;?>/UI/icons/store-icon.fw.png" alt="">
      <div class="ui-sidetext"> <?php pTRANSLATE("tiendas"); ?> </div>
    </a>

    <!-- <div class="ui-sidebar-subitems" style="display: none;">
      <a href="stores.php"><?php pTRANSLATE("ver_todas"); ?></a>
      <a href="create_store.php"><?php pTRANSLATE("nueva_tienda"); ?></a>
    </div> -->
  </div>

  <div id="category-menu" class="ui-sidebar-item waves-effect">
      <a class="ui-sidebar-button" href="categories.php">
        <img class="ui-icon" src="<?php echo $ROOT;?>/UI/icons/categories-icon.fw.png" alt="">
        <div class="ui-sidetext"> <?php pTRANSLATE("categorias"); ?> </div>
      </a>

      <!-- <div class="ui-sidebar-subitems" style="display: none;">
        <a href="categories.php"><?php pTRANSLATE("ver_todas"); ?></a>
        <a href="create_category.php"><?php pTRANSLATE("nueva_categoria"); ?></a>
      </div> -->
  </div>

</div>

<script type="text/javascript">
  menu_array = ["store-menu","category-menu"]; //Cookie name = id #name
  for (var i in menu_array) {
    InitialHideOrShow( menu_array[i] );
  }

  $ui_sidebar = $("#ui-sidebar");
  $sidebar_buttons = $(".ui-sidebar-button", $ui_sidebar);

  $sidebar_buttons.click( function(){
    $clicked_item = $(this).parent();
    $clicked_s_subitems = $( ".ui-sidebar-subitems", $clicked_item );
    HideOrShowAndCookie( $clicked_s_subitems , $clicked_item.attr('id') );
  });

  function InitialHideOrShow(cookie_name){
    var status = getCookie(cookie_name);
    if( status=="" || status=="hidden" ){
      $("#" + cookie_name + " .ui-sidebar-subitems").hide();
    }else{
      $("#" + cookie_name + " .ui-sidebar-subitems").show();
    }
  }
  function HideOrShowAndCookie($element,cookie_name){
    if( $element.is(":visible") ){
      $element.hide();
      setCookie( cookie_name ,"hidden");
    }else{
      setCookie( cookie_name ,"shown");
      $element.show();
    }
  }
  function HideOrShow($element){
    if( $element.is(":visible") ){
      $element.hide();
    }else{
      $element.show();
    }
  }
  function setCookie(cname, cvalue, exdays=365) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }
  function getCookie(cname) {
      var name = cname + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var ca = decodedCookie.split(';');
      for(var i = 0; i <ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
              c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
              return c.substring(name.length, c.length);
          }
      }
      return "";
  }

</script>
