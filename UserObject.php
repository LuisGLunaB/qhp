<?php
/*
TODO:
  Check if user Exists before inserting
  Login function
  Refactoring
*/
class UserObject extends SQLBasicSelector{
  public $con = NULL;
  protected $CookieName = "_xa";
  public $TableName = "users";

  protected $id = NULL;
  public $username = "";
  public $password = "";
  public $email = "";
  public $type = "";
  public $level = NULL;
  public $is_verified = NULL;
  public $is_active = NULL;

  public $UserData = array();
  protected $is_logged = False;

  public $lastId = NULL;

  public function __construct($con=NULL){
    parent::__construct($this->TableName, NULL, $con);
  }

  public function NewUser(array $UserData,$login=True,$is_verified=1){
    $fieldsMask = $this->getTableFields();

    $maskedUserData = self::maskAssocArray($UserData,$fieldsMask);
    $this->grantBaseAccess($maskedUserData);

    $obscuredPassword = self::ObscurePassword( $maskedUserData["password"] );
    $maskedUserData["password"] = $obscuredPassword;
    $maskedUserData["is_verified"] = $is_verified;

    $INSERT = new SQLInsert( $this->TableName, $fieldsMask, $this->con );
  	$INSERT->INSERT( $maskedUserData );
  	if( ! $INSERT->execute() ){
      $this->ErrorManager->handleError("Error when creating New User.", $e );
    }

    $this->lastId = $INSERT->lastId;
    if($login){
      $this->LoginWithObscuredPassword( $this->lastId, $obscuredPassword );
    }

    return [ $this->lastId, $obscuredPassword ];
  }
  public function grantBaseAccess(&$UserData){
    $UserData["level"] = 0;
    $UserData["type"] = "Regular";
    $UserData["is_active"] = 1;
  }
  public static function ObscurePassword($password){
    $sal = "viveriveniversumvivusvici";
    $encrypted_password = sha1($sal.$password);
    return $encrypted_password;
  }

  public function LoginWithObscuredPassword($id,$obscuredPassword){
    $credentials = array(
      "id"=>$id,
      "password"=>$obscuredPassword
    );
    return $this->tryToLogin($credentials);
  }
  protected function tryToLogin($credentials){
    $success = False;
    if( $this->credentialsAreValid($credentials) ){
      $success = $this->loadUserData($credentials);
      if($success){
          $this->is_logged = True;
          $this->saveCredentials();
        }
    }else{
      $this->ErrorManager->handleError("Usuario o Contraseña Incorrectos");
      $this->Logout();
    }
    return $success;
  }
  public function credentialsAreValid($credentials){
    return $this->EXISTS($credentials);
  }
  protected function loadUserData($credentials){
    $success = False;
    $this->WHERE($credentials);
    $this->PAGE( 0, 1);
    $UserData = $this->execute();
    $this->WHERE->clear();

    $success = $this->status();
    if(sizeof($UserData)>0){
      $success = $this->setAttributes($UserData[0]);
    }else{
      $success = False;
    }

    return $success;
  }
  protected function setAttributes($UserData){
    if(sizeof($UserData)>0){
      $UserData = $this->maskWithMyFields($UserData);
      $this->UserData = $UserData;
      /*
      foreach($UserData as $key => $value){
        eval('$this->'.$key.' = &$this->UserData["'.$key.'"];');
      }
      */

      $this->id = &$this->UserData["id"];
      $this->username = &$this->UserData["username"];
      $this->password = &$this->UserData["password"];
      $this->email = &$this->UserData["email"];
      $this->type = &$this->UserData["type"];
      $this->level = &$this->UserData["level"];

      $this->is_verified = &$this->UserData["is_verified"];
      $this->is_active = &$this->UserData["is_active"];
      return True;
    }else{
      return False;
    }
  }

  public function saveCredentials(){
    $this->setCredentialsCookie();
  }
  public function deleteCredentials(){
    $this->unsetCredentialsCookie();
  }

  public function setCredentialsCookie(){
    $RandomInteger = rand(10000,99999);
    $RandomString = sha1("a".rand(10000,99999));

    $RealInteger = $this->id;
    $RealString = $this->password;

    $value = "$RandomString:$RealInteger:$RandomInteger:$RealString";
    $OneDay = (86400 * 30);
    $_COOKIE[$this->CookieName] = $value;
    setcookie($this->CookieName, $value, time() + ($OneDay*90), "/");
  }
  public function unsetCredentialsCookie(){
    if( $this->isCookieSet() ){
      setcookie($this->CookieName, "", time() - 100000, "/");
      if(isset($_GET[$this->CookieName] )){
        unset( $_GET[$this->CookieName] );
      }
    }
  }
  public function LoginWithCookie(){
    $success = False;
    if( $this->isCookieSet() ){
      list($id,$obscuredPassword) = $this->getCredentialsFromCookie();
      $success = $this->LoginWithObscuredPassword($id,$obscuredPassword);
    }
    return $success;
  }
  public function isCookieSet(){
    return key_exists($this->CookieName,$_COOKIE);
  }
  public function getCredentialsFromCookie(){
    $id = NULL;
    $obscuredPassword = NULL;
    if( $this->isCookieSet() ){
      list($rubish1,$id,$rubish2,$obscuredPassword) = explode(":",$_COOKIE[$this->CookieName]);
    }
    return [$id,$obscuredPassword];
  }

  public function Login($email,$password){

  }

  public function Logout(){
    $this->clearUserData();
    $this->deleteCredentials();
  }

  public function isLogged(){
    return $this->is_logged;
  }
  public static function getROOT(){
    $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
    return $root;
  }
  protected function clearUserData(){
    $this->id = NULL;
    $this->username = "";
    $this->password = "";
    $this->email = "";
    $this->type = "";
    $this->level = NULL;

    $this->is_verified = NULL;
    $this->is_active = NULL;

    $this->UserData = array();
    $this->is_logged = False;
  }
}
