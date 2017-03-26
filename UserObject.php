<?php
//Refactor tryToLogin / loadUserData
class UserObject extends SQLBasicSelector{
  public $con = NULL;
  protected $IdentificationCookie = "_xa";
  protected $DeviceCookie = "_ja";
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

  protected $CredentialsFields = ['id','username','email',"password"];
  protected $IdentificationFields = ['id','username','email'];
  public $lastId = NULL;

  public $LoginCallback = "index.php";
  public $LogoutCallback = "index.php";

  public function __construct($con=NULL){
    parent::__construct($this->TableName, NULL, $con);
  }

  # New User Methods
  public function NewUser(array $UserData,$login=True,$is_verified=1){
    if( ! $this->checkIfUserExists($UserData) ){
      // Grant basic access
      $maskedUserData = self::maskAssocArray($UserData,$this->getTableFields());
      $this->grantBaseAccess($maskedUserData);

      // Set Password and Verification
      $obscuredPassword = self::ObscurePassword( $maskedUserData["password"] );
      $maskedUserData["password"] = $obscuredPassword;
      $maskedUserData["is_verified"] = (int) $is_verified;

      //Try to Insert
      $this->lastId = $this->INSERTUSER($maskedUserData);
      if( is_null($this->lastId) ){
        $this->ErrorManager->handleError("Error al Agregar Usuario a la base de datos." );
      }

      // Login if everything ran properly
      if( $login and $this->status() ){
        $this->LoginWithObscuredPassword( $this->lastId, $obscuredPassword );
      }
      return [ $this->lastId, $obscuredPassword ];
    }else{
      $this->ErrorManager->handleError("Este usuario ya existe." );
      $this->Logout();
      return [NULL,NULL];
    }
  }
  public function checkIfUserExists(array $UserData){
    $MaskedUserData = self::maskAssocArray($UserData,$this->IdentificationFields);
    $Exists = $this->EXISTS($MaskedUserData);
    return $Exists;
  }
  public function grantBaseAccess(&$UserData){
    $UserData["level"] = 0;
    $UserData["type"] = "Regular";
    $UserData["is_active"] = 1;
  }
  public function INSERTUSER($maskedUserData){
    $INSERT = new SQLInsert( $this->TableName, NULL, $this->con );
  	$INSERT->INSERT( $maskedUserData );
  	if( ! $INSERT->execute() ){
      $this->ErrorManager->handleError("Error al crear nuevo usuario.", $INSERT->ErrorManager->getErrorObject() );
    }
    return $INSERT->lastId;
  }
  public static function ObscurePassword($password){
    $sal = "viveriveniversumvivusvici";
    $encrypted_password = sha1($sal.$password);
    return $encrypted_password;
  }

  # Login Methods
  public function Login($emailOrUsername,$password){
    $success = False;
    $obscuredPassword = self::ObscurePassword($password);
    $credentials["password"] = $obscuredPassword;
    $credentials["email"] = $emailOrUsername;

    if( $this->credentialsAreValid($credentials) ){
      $success = $this->tryToLogin($credentials,False);
    }else{
      unset($credentials["email"]);
      $credentials["username"] = $emailOrUsername;
      if( $this->credentialsAreValid($credentials) ){
        $success = $this->tryToLogin($credentials);
      }else{
        $this->ErrorManager->handleError( $this->message() );
      }
    }
    return $success;
  }
  public function LoginWithObscuredPassword($id,$obscuredPassword){
    $credentials = array(
      "id"=> (int) $id,
      "password"=>$obscuredPassword
    );
    return $this->tryToLogin($credentials);
  }
  # Autentication and Login methods
  protected function tryToLogin($credentials,$LogoutOnFailure=True){
    $success = False;

    $credentials = self::maskAssocArray($credentials,$this->CredentialsFields);
    if( $this->credentialsAreValid($credentials) ){
      $success = $this->loadUserData($credentials);
      if($success){
        $this->is_logged = True;
        $this->saveCredentials();
      }else{
        $this->ErrorManager->handleError( $this->message() );
      }
    }else{
      $this->ErrorManager->handleError( $this->message() );
    }

    if( ( ! $success ) and $LogoutOnFailure ){
      $this->Logout();
    }
    return $success;
  }
  public function credentialsAreValid($credentials){
    $credentials = self::maskAssocArray($credentials,$this->CredentialsFields);
    if( sizeof($credentials)>0 ){
      return $this->EXISTS($credentials);
    }else{
      $this->ErrorManager->handleError("No hay suficientes datos para verificar el acceso.");
      return False;
    }
  }

  # Load User Data from Database
  protected function loadUserData($credentials){
    $success = False;

    $credentials = self::maskAssocArray($credentials,$this->CredentialsFields);
    $this->WHERE($credentials);
    $this->PAGE( 0, 1);
    $UserData = $this->execute();
    $this->WHERE->clear();
    $success = $this->status();

    if($success){
      if( sizeof($UserData)>0 ){
        $success = $this->setAttributes($UserData[0]);
        if( ! $success ){
          $this->ErrorManager->handleError( $this->message() );
        }
      }else{
        $success = False;
        $this->ErrorManager->handleError("No hay registro para este usuario.");
      }
    }else {
      $this->ErrorManager->handleError( $this->message() );
    }

    return $success;
  }
  protected function setAttributes($UserData){
    $success = False;
    if(sizeof($UserData)>0){
      $success = True;
      $this->UserData = $this->maskWithMyFields($UserData);

      foreach($this->UserData as $key => $value){
        try{
          $evaluateNewAttribute = '$this->'.$key.' = &$this->UserData["'.$key.'"];';
          eval($evaluateNewAttribute);
        }catch (Exception $e){
          $success = False;
          $this->ErrorManager->handleError("Error al extraer información del usuario.", $e, $exitExecution=True);
        }
      }

      return $success;
    }else{
      $this->ErrorManager->handleError("El Registro de éste usuario está vacío.");
      return False;
    }
  }
  public function saveCredentials(){
    $this->setCredentialsCookie();
  }
  public function setCredentialsCookie(){
    $this->setIdentificationCookie();
    $this->setDeviceCookie();
  }
  public function setIdentificationCookie(){
    $RandomInteger = rand(10000,99999);
    $RandomString = sha1("a".rand(10000,99999));

    $RealInteger = $this->id;
    $RealString = $this->password;

    $value = "$RandomString:$RealInteger:$RandomInteger:$RealString";
    $OneDay = 86400;

    $_COOKIE[$this->IdentificationCookie] = $value;
    setcookie($this->IdentificationCookie, $value, time() + ($OneDay*100), "/");
  }
  public function setDeviceCookie(){
    $OneDay = 86400;
    $value = self::getDeviceCookie();
    $_COOKIE[$this->DeviceCookie] = $value;
    setcookie($this->DeviceCookie, $value, time() + ($OneDay*100), "/");
  }
  protected static function getDeviceCookie(){
    $salt = "viveriveniversumvivusvici";
    return sha1($salt . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
  }

  // Login with Cookie or Session
  public function LoginWithCookie(){
    $success = False;
    if( $this->isCookieLoginPossible() ){
      if( $this->isThisDeviceValid() ){
        list($id,$obscuredPassword) = $this->getCredentialsFromCookie();
        $success = $this->LoginWithObscuredPassword($id,$obscuredPassword);
        if( ! $success ){
          $this->ErrorManager->handleError( $this->message() );
        }
      }else{
        $this->ErrorManager->handleError( "Error al entrar a la cuenta: sesión duplicada." );
        $this->Logout();
      }
    }
    return $success;
  }
  public function isCookieLoginPossible(){
    return ($this->isIdentificationCookieSet() and $this->isDeviceCookieSet() );
  }
  protected function isThisDeviceValid(){
    return ( $_COOKIE[$this->DeviceCookie] == self::getDeviceCookie() );
  }
  public function isIdentificationCookieSet(){
    return key_exists($this->IdentificationCookie,$_COOKIE);
  }
  public function isDeviceCookieSet(){
    return key_exists($this->DeviceCookie,$_COOKIE);
  }
  public function getCredentialsFromCookie(){
    $id = NULL;
    $obscuredPassword = NULL;
    if( $this->isIdentificationCookieSet() ){
      list($rubish1,$id,$rubish2,$obscuredPassword) = explode(":",$_COOKIE[$this->IdentificationCookie]);
    }
    return [$id,$obscuredPassword];
  }

  # Logout and Cleaning Methods
  public function Logout(){
    $this->clearUserData();
    $this->deleteCredentials();
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
  public function deleteCredentials(){
    $this->unsetCredentialsCookie();
  }
  public function unsetCredentialsCookie(){

    if( $this->isIdentificationCookieSet() ){
      setcookie($this->IdentificationCookie, "", time() - 100000, "/");
      if(isset($_GET[$this->IdentificationCookie] )){
        unset( $_GET[$this->IdentificationCookie] );
      }
    }

    if( $this->isDeviceCookieSet() ){
      setcookie($this->DeviceCookie, "", time() - 100000, "/");
      if(isset($_GET[$this->DeviceCookie] )){
        unset( $_GET[$this->DeviceCookie] );
      }
    }

  }

  # Other methods
  public function isLogged(){
    return $this->is_logged;
  }
  public static function getROOT(){
    $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
    return $root;
  }
}
