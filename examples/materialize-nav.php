
  <!DOCTYPE html>
  <html>
    <head>
      <!--Import Google Icon Font-->
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="./materialize/css/materialize.min.css"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>

    <body>
      <!--Import jQuery before materialize.js-->
      <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="./materialize/js/materialize.min.js"></script>


      <ul id="slide-out" class="side-nav">
          <li><div class="userView">
            <div class="background">
              <img src="images/office.jpg">
            </div>
            <a href="#!user"><img class="circle" src="images/yuna.jpg"></a>
            <a href="#!name"><span class="white-text name">John Doe</span></a>
            <a href="#!email"><span class="white-text email">jdandturk@gmail.com</span></a>
          </div></li>
          <li><a href="#!"><i class="material-icons">cloud</i>First Link With Icon</a></li>
          <li><a href="#!">Second Link</a></li>
          <li><div class="divider"></div></li>
          <li><a class="subheader">Subheader</a></li>
          <li><a class="waves-effect" href="#!">Third Link With Waves</a></li>
        </ul>
        <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>

       <script type="text/javascript">
        //$(".button-collapse").sideNav();
        $('.button-collapse').sideNav({
            menuWidth: 300, // Default is 300
            edge: 'left', // Choose the horizontal origin
            closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
            draggable: true // Choose whether you can drag to open on touch screens
          }
        );

       //$('.button-collapse').sideNav('show');
       //$('.button-collapse').sideNav('hide');
       //$('.button-collapse').sideNav('destroy');
       </script>

    </body>
  </html>
