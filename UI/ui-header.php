<?php include_once( ROOT . "/UI/ui-form-functions.php"); ?>
<?php include_once( ROOT . "/UI/ui-toasts.php"); ?>

<div id="ui-header">

  <div class="left-align" style="display: inline-block; float: left;">

    <div class="ui-header-breadcrumbs left-align">
      <?php
      foreach($BreadCrumbs as $row){
        list($BC_url,$BC_text) = $row;
        $BC_text = TRANSLATE( $BC_text );
        echo "<a href='$BC_url'>$BC_text</a> <span> > </span>";
      }
      ?>
    </div>

    <div class="ui-header-title left-align">
      <h1><?php pTRANSLATE($SectionTitle); ?></h1>
    </div>
  </div>

  <div class="ui-main-buttons right-align" style="display: inline-block; float: right;">
    <?php
    foreach($MenuButtons as $row){
      list($MB_url,$MB_icon) = $row;
      $MB_text = TRANSLATE( $MB_icon );
      echo
      "<a href='$MB_url' data-position='bottom' data-delay='0' data-tooltip='$MB_text'
          class='btn-floating btn-medium tooltipped waves-effect waves-light blue darken-1'>
          <i class='material-icons'>$MB_icon</i>
      </a>";
    }
    ?>
  </div>

</div>
