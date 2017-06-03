<?php

class EmailerObject{
  public function Send($to,$title,$message){
    try{
        return mail($to, $title, $message, $this->getHeaders(), "-f$this->From" );
    }catch(Exception $e){
        $this->errorMessage = "Error al enviar correo. " . $e->getMessage();
        return False;
    }
  }
  public function SendWithTemplate($to,$title,$AssociativeMessage,$Template=NULL){
    $AssociativeMessage = self::convertToAssociative($AssociativeMessage); //TypeForcing
    $Template = (self::isTemplateEmpty($Template)) ? $this->getTemplate() : $Template;

    $message = self::mergeMessageWithTemplate($AssociativeMessage,$Template,$this->Placeholders);
    return $this->Send($to,$title,$message);
  }
  public function getTemplate(){
    return NULL;
  }

  public function setFrom($From){
    $this->From = $From;
  }
  public function getFrom(){
    return $this->From;
  }
  public function setPlaceholders($Placeholders){
    $this->Placeholders = $Placeholders;
  }

  public function getHeaders(){
		$headers =
			"MIME-Version: 1.0\r\n".
			"From: $this->From\r\n".
			"Content-type: text/html; charset=utf-8\r\n".
			"X-Priority: 1\r\n".
      "Reply-To: $this->From\r\n".
      "X-Mailer: PHP/" . phpversion()
		;
		return $headers;
	}

  public function status(){
    return ( $this->errorMessage() == "" );
  }
  public function errorMessage(){
    return $this->errorMessage;
  }

  protected static function isMessageAssociative($message){
    return is_array($message);
  }
  protected static function convertToAssociative($AssociativeMessage){
    if ( ! self::isMessageAssociative($AssociativeMessage) ){
      $AssociativeMessage = array("message"=>$AssociativeMessage);
    }
    return $AssociativeMessage;
  }
  protected static function isTemplateEmpty($Template){
    return ( is_null($Template) or ($Template=="") );
  }
  protected static function mergeMessageWithTemplate($AssociativeMessage,$Template,$Placeholders="@@"){
    if( self::isTemplateEmpty($Template) ){
      $message = $AssociativeMessage["message"];
    }else{
      $message = self::replacePlaceholdersWithValues($AssociativeMessage,$Template,$Placeholders);
      $message = self::deleteUnusedPlaceholders($message.$Placeholders);
    }
    return $message;
  }

  protected static function replacePlaceholdersWithValues($Assoc,$Text,$Placeholders="@@"){
    $XX = $Placeholders;
    foreach($Assoc as $key => $value){
      $Text = str_replace( $XX.$key.$XX, $value, $Text);
    }
    return $Text;
  }
  protected static function deleteUnusedPlaceholders($Text,$Placeholders="@@"){
    $XX = $Placeholders;
    return preg_replace("/$XX.*?$XX/", "", $Text);
  }

  public function __construct($From){
    $this->setFrom($From);
  }

  protected $From = NULL;
  protected $Placeholders = "@@";
  protected $errorMessage = "";

}
