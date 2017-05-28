
<form id="login-user-form" class="login-user-form" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="form" value="login-user">

  <input id="username" type="text" name="username" value="" placeholder="Nombre de usuario">
  <!-- <input id="email" type="text" name="email" value="" placeholder="Email"> -->
  <input type="password" name="password" value="" placeholder="Contraseña">

  <div class="form-error"><?php UI_ShowFormError(); ?></div>

  <input type="submit" name="submit" value="Entrar">

</form>
