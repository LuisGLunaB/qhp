
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
      <script type="text/javascript">
      $('input.autocomplete').autocomplete({
        data: {
          "Apple": null,
          "Microsoft": null,
          "Google": 'http://placehold.it/250x250'
        },
        limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
        onAutocomplete: function(val) {
          // Callback function when value is autcompleted.
        },
        minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
      });
      </script>

      <div class="container">
        <!-- Scaled in -->
        <a id="scale-demo" href="#!" class="btn-floating btn-large scale-transition">
          <i class="material-icons">add</i>
        </a>

        <!-- Scaled out -->
        <a id="scale-demo" href="#!" class="btn-floating btn-large scale-transition scale-out">
          <i class="material-icons">add</i>
        </a>
        <blockquote>
          This is an example quotation that uses the blockquote tag.
        </blockquote>
        <p class="flow-text">Un defecto común que hemos visto en muchos frameworks es la falta de soporte para el texto verdaderamente responsivo. Mientras que otros elementos de la página responden fluidamente, el texto aún se redimensiona sobre una base fija. Para mejorar este problema, para páginas cargadas de texto, hemos creado una clase que escala de manera fluida el tamaño del texto y la altura de línea para optimizar la legibilidad del usuario. La longitud de línea se mantiene entre 45-80 caracteres y la altura de línea se escala para ser más grande en las pantallas más pequeñas.

Para ver el Flujo de Texto en acción, cambie el tamaño de su navegador lentamente y verá cambiar el tamaño de este texto. ¡Utilice el botón de arriba para activar o desactivar el flujo de texto y ver la diferencia!</p>

        <a class="btn btn-floating pulse"><i class="material-icons">menu</i></a>
        <a class="btn btn-floating btn-large pulse"><i class="material-icons">cloud</i></a>
        <a class="btn btn-floating btn-large cyan pulse"><i class="material-icons">edit</i></a>

            <div class="col s12 m2">
              <p class="z-depth-1">z-depth-1</p>
            </div>
            <div class="col s12 m2">
              <p class="z-depth-2">z-depth-2</p>
            </div>
            <div class="col s12 m2">
              <p class="z-depth-3">z-depth-3</p>
            </div>
            <div class="col s12 m2">
              <p class="z-depth-4">z-depth-4</p>
            </div>
            <div class="col s12 m2">
              <p class="z-depth-5">z-depth-5</p>
            </div>

            <table class="responsive-table highlight">
                    <thead>
                      <tr>
                          <th>Name</th>
                          <th>Item Name</th>
                          <th>Item Price</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr>
                        <td>Alvin</td>
                        <td>Eclair</td>
                        <td>$0.87</td>
                      </tr>
                      <tr>
                        <td>Alan</td>
                        <td>Jellybean</td>
                        <td>$3.76</td>
                      </tr>
                      <tr>
                        <td>Jonathan</td>
                        <td>Lollipop</td>
                        <td>$7.00</td>
                      </tr>
                    </tbody>
                  </table>

        <img class="responsive-img" src="images/yuna.jpg">

         <div class="col s12 m8 offset-m2 l6 offset-l3">
           <div class="card-panel grey lighten-5 z-depth-1">
             <div class="row valign-wrapper">
               <div class="col s2">
                 <img src="images/yuna.jpg" alt="" class="circle responsive-img"> <!-- notice the "circle" class -->
               </div>
               <div class="col s10">
                 <span class="black-text">
                   This is a square image. Add the "circle" class to it to make it appear circular.
                 </span>
               </div>
             </div>
           </div>
         </div>

         <div class="video-container">
             <iframe width="853" height="480" src="//www.youtube.com/embed/Q8TXgCzxEnw?rel=0" frameborder="0" allowfullscreen></iframe>
         </div>

         <div class="row">
           <div class="col s12 m7">
             <div class="card">
               <div class="card-image">
                 <img src="images/sample-1.jpg">
                 <span class="card-title">Card Title</span>
               </div>
               <div class="card-content">
                 <p>I am a very simple card. I am good at containing small bits of information.
                 I am convenient because I require little markup to use effectively.</p>
               </div>
               <div class="card-action">
                 <a href="#">This is a link</a>
               </div>
             </div>
           </div>
         </div>

         <div class="col s12 m7">
           <h2 class="header">Horizontal Card</h2>
           <div class="card horizontal">
             <div class="card-image">
               <img src="http://lorempixel.com/100/190/nature/6">
             </div>
             <div class="card-stacked">
               <div class="card-content">
                 <p>I am a very simple card. I am good at containing small bits of information.</p>
               </div>
               <div class="card-action">
                 <a href="#">This is a link</a>
               </div>
             </div>
           </div>
         </div>

         <div class="card">
           <div class="card-image waves-effect waves-block waves-light">
             <img class="activator" src="images/office.jpg">
           </div>
           <div class="card-content">
             <span class="card-title activator grey-text text-darken-4">Card Title<i class="material-icons right">more_vert</i></span>
             <p><a href="#">This is a link</a></p>
           </div>
           <div class="card-reveal">
             <span class="card-title grey-text text-darken-4">Card Title<i class="material-icons right">close</i></span>
             <p>Here is some more information about this product that is only revealed once clicked on.</p>
           </div>
         </div>

         <ul class="collection">
           <li class="collection-item avatar">
             <img src="images/yuna.jpg" alt="" class="circle">
             <span class="title">Title</span>
             <p>First Line <br>
                Second Line
             </p>
             <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
           </li>
           <li class="collection-item avatar">
             <i class="material-icons circle">folder</i>
             <span class="title">Title</span>
             <p>First Line <br>
                Second Line
             </p>
             <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
           </li>
           <li class="collection-item avatar">
             <i class="material-icons circle green">insert_chart</i>
             <span class="title">Title</span>
             <p>First Line <br>
                Second Line
             </p>
             <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
           </li>
           <li class="collection-item avatar">
             <i class="material-icons circle red">play_arrow</i>
             <span class="title">Title</span>
             <p>First Line <br>
                Second Line
             </p>
             <a href="#!" class="secondary-content"><i class="material-icons">grade</i></a>
           </li>
         </ul>

         <div class="input-field col s12">
             <select>
               <option value="" disabled selected>Choose your option</option>
               <option value="1">Option 1</option>
               <option value="2">Option 2</option>
               <option value="3">Option 3</option>
             </select>
             <label>Materialize Select</label>
           </div>

           <div class="input-field col s12">
             <select multiple>
               <option value="" disabled selected>Choose your option</option>
               <option value="1">Option 1</option>
               <option value="2">Option 2</option>
               <option value="3">Option 3</option>
             </select>
             <label>Materialize Multiple Select</label>
           </div>

           <div class="input-field col s12">
             <select multiple>
               <optgroup label="team 1">
                 <option value="1">Option 1</option>
                 <option value="2">Option 2</option>
               </optgroup>
               <optgroup label="team 2">
                 <option value="3">Option 3</option>
                 <option value="4">Option 4</option>
               </optgroup>
             </select>
             <label>Optgroups</label>
           </div>

           <div class="input-field col s12 m6">
             <select class="icons">
               <option value="" disabled selected>Choose your option</option>
               <option value="" data-icon="images/sample-1.jpg" class="circle">example 1</option>
               <option value="" data-icon="images/office.jpg" class="circle">example 2</option>
               <option value="" data-icon="images/yuna.jpg" class="circle">example 3</option>
             </select>
             <label>Images in select</label>
           </div>
           <div class="input-field col s12 m6">
             <select class="icons">
               <option value="" disabled selected>Choose your option</option>
               <option value="" data-icon="images/sample-1.jpg" class="left circle">example 1</option>
               <option value="" data-icon="images/office.jpg" class="left circle">example 2</option>
               <option value="" data-icon="images/yuna.jpg" class="left circle">example 3</option>
             </select>
             <label>Images in select</label>
           </div>

           <label>Browser Select</label>
           <select class="browser-default">
             <option value="" disabled selected>Choose your option</option>
             <option value="1">Option 1</option>
             <option value="2">Option 2</option>
             <option value="3">Option 3</option>
           </select>

           <!-- Switch -->
           <div class="switch">
             <label>
               No
               <input type="checkbox">
               <span class="lever"></span>
               Sí
             </label>
           </div>


         <video class="responsive-video" controls>
           <source src="movie.mp4" type="video/mp4">
         </video>

          <input type="date" class="datepicker">

          <div class="row">
            <div class="col s12">
              <div class="row">
                <div class="input-field col s12">
                  <i class="material-icons prefix">textsms</i>
                  <input type="text" id="autocomplete-input" class="autocomplete">
                  <label for="autocomplete-input">Autocomplete</label>
                </div>
              </div>
            </div>
          </div>

          <nav>
            <div class="nav-wrapper">
              <form>
                <div class="input-field">
                  <input id="search" type="search" required>
                  <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                  <i class="material-icons">close</i>
                </div>
              </form>
            </div>
          </nav>

<ul class="pagination">
  <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
  <li class="active"><a href="#!">1</a></li>
  <li class="waves-effect"><a href="#!">2</a></li>
  <li class="waves-effect"><a href="#!">3</a></li>
  <li class="waves-effect"><a href="#!">4</a></li>
  <li class="waves-effect"><a href="#!">5</a></li>
  <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
</ul>

<div class="progress">
    <div class="determinate" style="width: 30%"></div>
</div>

<div class="progress">
    <div class="indeterminate"></div>
</div>

<div class="preloader-wrapper big active">
    <div class="spinner-layer spinner-blue-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div><div class="gap-patch">
        <div class="circle"></div>
      </div><div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>

  <div class="preloader-wrapper active">
    <div class="spinner-layer spinner-red-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div><div class="gap-patch">
        <div class="circle"></div>
      </div><div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>

  <div class="preloader-wrapper small active">
    <div class="spinner-layer spinner-green-only">
      <div class="circle-clipper left">
        <div class="circle"></div>
      </div><div class="gap-patch">
        <div class="circle"></div>
      </div><div class="circle-clipper right">
        <div class="circle"></div>
      </div>
    </div>
  </div>
          <script type="text/javascript">
              $(document).ready(function() {
                $('select').material_select();
              });

              $('.datepicker').pickadate({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 5 // Creates a dropdown of 15 years to control year
              });

          </script>

      </div>
    </body>
  </html>
