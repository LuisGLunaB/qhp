<?php
$webmaster = "webmaster@mkti.mx";
$contacto = "contacto@mkti.mx";
//$noreply = "contacto@mkti.mx";


function sendEmail($titulo,$mensaje,$to,$from = NULL,
	$template = "./php/email_template.html"){	
	
	//AQUI VA EL CORREO NO REPLY
	if(is_null($from)){$from = "contacto@mkti.mx";}
	
	$mailHeader = "From: $from\r\n";
	$mailHeader .= "Reply-To: $from\r\n";
	$mailHeader .= "MIME-Version: 1.0\r\n";
	$mailHeader .= "Content-type: text/html\r\n";
	$mailHeader .= 'X-Mailer: PHP/' . phpversion();
	
	$html = utf8_decode(file_get_contents($template));
	$mensaje = str_replace("@@mensaje@@",$mensaje,$html);
	//echo $header;
	
	$sus = false;
	try{
		$sus = mail ($to,$titulo,$mensaje,$mailHeader);
	}catch(Exception $e){
		print_r($e);
	}
	
	return $sus;
}
	
function verifyEmail($email,$id,$contrasena){
	$sus = false;
	$sus = 
	sendEmail(
	"Verifica tu cuenta de MKTi",
	'Gracias por registrarte en MKTi. 
	Por favor verifica tu cuenta dando clic en el siguiente enlace: <br />
	<a href="http://'.$_SERVER['HTTP_HOST'].
	'/verify.php?c=receive&ja='.$id.'&xa='.urlencode($contrasena).'">Verificar mi cuenta</a>',
	$email
	);
	
	return $sus;
}
?>