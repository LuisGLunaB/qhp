<form id="new-user-form" class="new-user-form" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="form" value="new-user">
<input type="hidden" name="login_after_insert" value="1">
<input type="hidden" name="is_verified" value="1">

  <input id="email" type="text" name="email" value="" placeholder="Email">
  <input type="password" name="password" value="" placeholder="Contraseña">
  <input type="password" name="password_verification" value="" placeholder="Repetir Contraseña">

  <div class="form-error"><?php showFormError(); ?></div>

  <input type="submit" name="submit" value="Crear Cuenta">

</form>
