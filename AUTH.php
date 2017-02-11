<?php
//Emailing con plantilla

//verify.php?c=send - verificar cuenta (emailing)
//verify.php?c=receive

//change.php?c=send - cambiar contraseña (emailing)
//change.php?c=receive

$user['id_usuario'] = 0;
$user['status'] = false;
$user['facebook_id'] = 0;
$user['facebook_token'] = 0;
$user['id_guest'] = 0;
$user['nivel'] = 0;
$user['verificado'] = 0;
$user['activo'] = 0;
$user['men'] = "";
//Crear cuenta
//Entrar
//Cambiar contraseña
//Verificar cuenta

function register(&$user, $email , $contrasena, $contrasena2, $otros = NULL ,
 $URL = "index.php", $verify = true){
	 
	global $sal;
	$chars = 8;
	
	//sleep(2);
	if(validEmail($email)){
		$where["email"] = $email;
		$sel = Q_select("tUsuarios","email",$where);
		if($sel['status']){
			if( sizeof($sel['data']) == 0){
				if($contrasena==$contrasena2){
					if(strlen($contrasena)>=$chars){
						//Preparar variables para el insert
						$variables = array();if(!is_null($otros)){$variables = $otros;}
						$variables["email"] = $email;
						$variables["contrasena"] = sha1($contrasena.$sal);
						if(!$verify){$variables["verificado"] = 1;}
						$fields = array();foreach($variables as $key => $val){array_push($fields,$key);}
						
						$ins = Q_insert("tUsuarios", $variables , $fields);
						if($ins['status']){
							//Enviar correo de verificacion
							if($verify){
								include_once("./php/EMAIL.php");
								verifyEmail(
								$variables["email"],
								encryptInt($ins["last"]),
								$variables["contrasena"]);
							}
							//Entrar a la cuenta
							login($user, $email, $variables["contrasena"] ,$URL, false);
							if(!$user['status']){
								$user['men'] .= "* Error al entrar a la cuenta después del registro.";
							}
						}else{
							//Error
							$user['men'] .= "* ".$ins['message']."<br />";
						}	
					}else{
						//Error
						$user['men'] .= "* La contraseña debe tener al menos $chars carácteres.<br />";
					}
				}else{
					//Error
					$user['men'] .= "* Las contraseñas no coinciden.<br />";
				}
			}else{
				//Error
				$user['men'] .= "* Este correo electrónico ya está registrado.<br />";
			}
		}else{
			//Error
			$user['men'] .= "* Error al conectar con la base de datos.<br />";
		}
	}else{
		//Error
		$user['men'] = "* El correo no es válido.<br />";
	}
}

function login( &$user, $email, $contrasena, $URL = "index.php", $encriptar = true ){
	global $sal;
	
	unsetCookie("xa");
	unsetCookie("ja");
	
	$maxintentos = 4;
	if(!isset($_COOKIE['a'])){ssetCookie("a",0,0.125);$intentos = 0;}else{$intentos = $_COOKIE['a'];}
	
	if($encriptar){$contrasena = sha1($contrasena.$sal);}
	
	sleep(2);
	$where['email'] = $email;
	$where['contrasena'] = $contrasena;
	
	if(!(($intentos) >= $maxintentos)){
		$sel = Q_select("tUsuarios",NULL,$where);
		if($sel['status']){
			if( sizeof($sel['data']) != 0){
				//Login
				$datos = $sel['data'][0];
				foreach($datos as $key => $value){$user[$key] = Q_clear($value);}
				$user['status'] = true;
				//Crear cookies
				ssetCookie("ja",encryptCookie($email));
				ssetCookie("xa",$contrasena);
				//Redireccionar
				header("Location: ".$URL." ");
			}else{
				//Error: 4 Intentos
				if(!isset($_COOKIE['a'])){
					$intentos = 1;
				}else{
					$intentos = ($_COOKIE['a'])+1;
				}
				ssetCookie("a",$intentos,0.125);
				$user['men'] .= "* Usuario o contraseña incorrectos. 
				Te quedan ".($maxintentos-$intentos)." intentos.<br />";
			}
		}else{
			//Error
			$user['men'] .= "* Error al conectar con la base de datos.<br />";
		}
	}else{
		//Error
		$user['men'] .= "* Usuario o contraseña incorrectos. Se acabaron tus intentos, tu cuenta se bloqueará por 3 horas por cuestiones de seguridad.<br />";
	}
}

function logout($URL = "index.php"){
	unsetCookie("xa");
	unsetCookie("ja");
	$_SESSION = array();
	if(isset($_SESSION['live'])){unset($_SESSION['live']);}
	if(session_status() != PHP_SESSION_NONE){session_destroy();}
	header("Location: $URL");
}

function diasCookie($x){
	return (time()+(60*60*24*$x));
}

function unsetCookie($x){
	if(isset($_COOKIE[$x])) {
		unset($_COOKIE[$x]);
		setcookie($x, "", time()-3600,"/");
	}
}

function ssetCookie($key,$value,$days = 365){
	setcookie($key, $value,diasCookie($days),"/");
}

function encryptCookie($value){
	global $sal;
   if(!$value){return false;}
   $key = $sal;
   $text = $value;
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
   return trim(base64_encode($crypttext)); //encode for cookie
}

function decryptCookie($value){
	global $sal;
   if(!$value){return false;}
   $key = $sal;
   $crypttext = base64_decode($value); //decode cookie
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
   return trim($decrypttext);
}

function validEmail($email)
	{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || 
 checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

function decryptInt($x){return $x;}
function encryptInt($x){return $x;}

function validate(&$user, $restart = false, $URL = "login.php"){
	//Generar o tomar id_guest
	if(isset($_COOKIE['gu'])){
		$user['id_guest'] = $_COOKIE['gu'];
	}else{
		$id_guest = sha1(mt_rand(1,100000000));
		ssetCookie("gu",$id_guest,365*2);
		$_COOKIE['gu'] = $id_guest;
		$user['id_guest'] = $id_guest;
	}
	
	//Validar sesión
	$user['status'] = false;
	if (isset($_SESSION['live']) && session_status() != PHP_SESSION_NONE &&$restart = false) {
    	//La sesión ya estaba iniciada
		//echo "Sesión vieja";
		foreach($_SESSION as $key => $value){$user[$key] = $value;}
		$user['status'] = true;
	}else{
		//echo "Sesión nueva";
		//La sesión se acaba de iniciar
		//Verificar cookies
		if( isset($_COOKIE['ja']) && isset($_COOKIE['xa']) ){
			$where['email'] = decryptCookie($_COOKIE['ja']);
			$where['contrasena'] = $_COOKIE['xa'];
			$sel = Q_select("tUsuarios",NULL,$where,"",NULL);
			if($sel['status']){
				if( sizeof($sel['data']) != 0){
					//Login
					$datos = $sel['data'][0];
					
					foreach($datos as $key => $value){
						$_SESSION[$key] = Q_clear($value);
						$user[$key] = $_SESSION[$key];
					}
					
					$_SESSION['live'] = true;
					$user['status'] = true;
					if (session_status() == PHP_SESSION_NONE) {session_start();}
					
					//Validar Token de Facebook
					if($user['facebook_id']!=0){
						//include_once("./facebook/FACEBOOK.php");
						//Actualizar Token, si hay un error, imprimirlo.
						global $fb;
						$user["facebook_token"] = FB_longToken($fb, $user['facebook_token'] );
						$sus = FB_save_token( $user['id_usuario'] , $user["facebook_token"] );

						if(!$sus){echo $sus;}
					}
					
				}else{
					//Las cookies no coinciden
					unsetCookie("xa");
					unsetCookie("ja");
					$user['men'] .= "* La sesión ya caducó.<br />";
					header("Location: ".$URL."?caduco=1 ");
				}	
			}else{
				//Error
				$user['men'] .= "* Error al conectar con la base de datos.<br />";
			}
		}else{
			//Falta una cookie
			unsetCookie("xa");
			unsetCookie("ja");
		}
	}
}
?>