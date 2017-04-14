<?php
class EMAIL{
	Public $From = NULL;
	Public $To = NULL;
	Public $Title = NULL;
	Public $Message = "";

	Protected $Template = NULL;

	Public $Reply = NULL;
	Public $CC = NULL;
	Public $CCO = NULL;

	protected $status = False;
	protected $message = "";

	protected $debugging = False;

	# Construction methods
	public function __construct($From=NULL,$To=NULL,$Title=NULL,$Message=""){
		$this->SetDetails($From,$To,$Title,$Message);
		$this->matchGlobalDebuggingStatus();
	}
	public function SetDetails($From,$To,$Title,$Message){
		$this->From = $From;
		$this->To = $To;
		$this->Title = $Title;
		$this->Message = $Message;
	}
	private function matchGlobalDebuggingStatus(){
		if( isset($GLOBALS["debugging"]) ){
			$this->debugging = $GLOBALS["debugging"];
		}
	}

	# Template Management methods
	public function SetTemplate($TemplateURL){
		$this->Template = utf8_decode(file_get_contents($TemplateURL));
	}
	public function UnsetTemplate(){
		$this->Template = NULL;
	}
	public function hasTemplate(){
		return ( ! is_null($this->Template) );
	}

	public function Send($Message = NULL){
		if( ! is_null($Message) ){
			$this->Message = $Message;
		}
		$CompleteEmail = self::MergeTemplateAndMessage($this->Message,$this->Template);
		return $this->tryToSend($CompleteEmail);
	}
	public static function MergeTemplateAndMessage($messageArray,$Template){
		if( !	is_array($messageArray) ){
			$messageArray = array("message"=>$messageArray);
		}

		if( ! is_null($Template) ){
			$CompleteEmail = $Template;
			foreach($messageArray as $key => $string){
				$CompleteEmail = str_replace("@@$key@@",$string,$CompleteEmail);
			}
		}else{
			$CompleteEmail = $messageArray["message"];
		}

		return $CompleteEmail;
	}
	public function tryToSend($CompleteEmail){
		$this->status = False;

		if( $this->isEmailDataComplete() ){
				if( self::isValidEmail($this->From) ){
						if( self::isValidEmail($this->To) ){
								try{
										$this->status = mail($this->To, $this->Title, $CompleteEmail, $this->getHeaders(),"-f$this->From" );
										if( ! $this->status ){
												$this->message = "El correo no es válido para ser enviado.";
										}
								}catch(Exception $e){
										$this->message = "Error al enviar correo." . $e->getMessage();
								}
						}else{
								$this->message = "El correo de destino no es válido.";
						}
				}else{
						$this->message = "El correo de envío no es válido.";
				}
		}else{
				$this->message = "Datos insuficientes para mandar el correo.";
		}

		$this->alertIfEmailFailed();
		return $this->status;
	}
	protected function alertIfEmailFailed(){
		if( ( ! $this->status) and $this->debugging ){
			echo printf("<script>alert('%s');</script>", $this->message );
		}
	}

	public function isEmailDataComplete(){
		return (
			(
					  self::hasData($this->From)
				and self::hasData($this->To)
				and self::hasData($this->Title)
				and self::hasData($this->Message)
			)
		);
	}
	protected function hasData($x){
		return ( ! self::hasNoData($x) );
	}
	protected function hasNoData($x){
		return ( is_null($x) or $x=="" );
	}

	public static function isValidEmail($email,$evaluate=False){
		$isValid = True;
		if($evaluate){
	    $atIndex = strrpos($email, "@");
	    if ( is_bool($atIndex) && !$atIndex ){
	       $isValid = False;
	    }else{

	       $domain = substr($email, $atIndex+1);
	       $local = substr($email, 0, $atIndex);
	       $localLen = strlen($local);
	       $domainLen = strlen($domain);

	       if ($localLen < 1 || $localLen > 64){
	          $isValid = False;
	       }else if ($domainLen < 1 || $domainLen > 255){
	          $isValid = False;
	       }else if ($local[0] == '.' || $local[$localLen-1] == '.'){
	          $isValid = False;
	       }else if ( preg_match('/\\.\\./', $local) ){
	          $isValid = False;
	       }else if ( ! preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) ){
	          $isValid = False;
	       }else if ( preg_match('/\\.\\./', $domain) ){
	          $isValid = False;
	       }else if ( ! preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local) ) ){
	          if ( ! preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local) ) ){
	             $isValid = False;
	          }
	       }

	       if ( $isValid && ! (checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) ){
	          $isValid = False;
	       }
	    }
		}
    return $isValid;
	}

	public function getHeaders(){
		$Headers =
			"MIME-Version: 1.0\r\n".
			"From: $this->From\r\n".
			"Content-type: text/html; charset=utf-8\r\n".
			"X-Priority: 1\r\n"
		;

		$Headers = ( is_null($this->Reply) ) ?
			$Headers."Reply-To: $this->From\r\n" : $Headers."Reply-To: $this->Reply\r\n";
		$Headers = ( is_null($this->CC) ) ?
			$Headers : $Headers."CC: $this->CC\r\n";
		$Headers = ( is_null($this->CCO) ) ?
			$Headers : $Headers."CCO: $this->CCO\r\n";

		$Headers .= "X-Mailer: PHP/" . phpversion();

		return $Headers;
	}
	public function status(){
		return $this->status;
	}
	public function message(){
		return $this->message;
	}

	public static function ROOT(){
    // Get Full domain's URL
    $http = ( ! empty($_SERVER["HTTPS"]) ? "https":"http");
    $ROOT =  "$http://$_SERVER[HTTP_HOST]/";
    return $ROOT;
  }
}


function SendVerificationEmail($From,$To,$Title,$NAME,$VERIFICATION_URL){

	$success = SendEmailWithTemplate(
		$From, $To ,$Title,
		array("NAME" => $NAME,"VERIFICATION_URL" => $VERIFICATION_URL),
		MODULE_ROUTE_Email . "verification_template.php"
	);

	if( ! $success ) {
		echo "Error al enviar email de verificación. ";
	}

	return $success;
}

function SendEmailWithTemplate($From, $To , $Title, $message, $Template){
	$EMAIL = new EMAIL( $From, $To , $Title, $message );
	$EMAIL->SetTemplate( $Template );
	return $EMAIL->Send();
}

/*
$debugging = True;
$EMAIL = new EMAIL( "contacto@mkti.mx", "luis.g.luna18@gmail.com", "Prueba de envío ñ", "Hola mundóñ!");
$EMAIL->SetTemplate( MODULE_ROUTE_Email . "verification_template.html" );
if( $EMAIL->Send() ) {
	echo "Bien.";
}else{
	echo "Mal: $EMAIL->message() ";
}
*/

?>
