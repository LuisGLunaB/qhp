
<?php
  function showProcessToast(){
    if( array_key_exists("process",$_GET) ){
        $toast = $_GET["process"];

        if( array_key_exists("status",$_GET) ){
          if( $_GET["status"] ){
            showToast($toast);
          }else{
            showToast("status-false");
          }
        }else{
          showToast("no-status");
        }
    }
  }
  function showToast($toast){
    $toastMessage = getToastMessage($toast);
    $rand = rand(0,1000);
    echo '
      <script>
        var $toastContent'.$rand.' = $("<span>'.$toastMessage.'</span>");
        Materialize.toast($toastContent'.$rand.', 3000, "ui-toast");
      </script>
    ';
    // create-store
  }
  function getToastMessage($toast){
    return TRANSLATE( $toast."-toast" );
  }

  showProcessToast();
?>
