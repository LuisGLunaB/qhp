
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
    $toastDuration = 3000;
    $wait = 400;
    $x = rand(0,10000); //Random variable names so Toasts don't overwrite.
    echo '
      <script>
        var $toastContent'.$x.' = $("<span>'.$toastMessage.'</span>");
        setTimeout(function(){
            Materialize.toast($toastContent'.$x.', '.$toastDuration.', "ui-toast");
        }, '.$wait.');
      </script>
    ';
    // create-store
  }
  function getToastMessage($toast){
    return TRANSLATE( $toast."-toast" );
  }

  showProcessToast();
?>
