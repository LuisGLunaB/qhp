<?php
class EMAIL{
	Public $From = NULL;
	Public $To = NULL;
	Public $Title = NULL;
	Public $Message = "";

	Protected $Template = NULL;

	Public $Reply = NULL;
	Public $CC = NULL;
	Public $CCO = NULLM

	protected $status = False;
	protected $message = "";

	protected $debugging = False;

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
		$CompleteEmail = self::MergeTemplate($this->Message,$this->Template);
		$this->tryToSend($CompleteEmail);
	}
	public static function MergeTemplate($assocValues,$Template){
		$CompleteEmail = $Template;
		if( !	is_array($assocValues) ){
			$assocValues = array("message"=>$assocValues);
		}

		foreach($assocValues as $key => $value){
			$CompleteEmail = str_replace("@#$key#@",$value,$CompleteEmail);
		}
		return $CompleteEmail;
	}
	public function tryToSend($CompleteEmail){
		$this->status = False;
		try{
			$this->status = mail($this->To, $this->Title, $CompleteEmail, $this->getHeaders() );
		}catch(Exception $e){
			$this->status = False;
			$this->message = "Error al enviar correo." $e->getMessage();
			if( $this->debugging ){
				echo $this->message;
			}
		}
		return $success;
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
	}

	public function status(){
		return $this->status;
	}
	public function message(){
		return $this->message;
	}
	public static function getROOT(){
    $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
    return $root;
  }
}

?>
