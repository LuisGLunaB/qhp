<?php
class UserObject extends SQLBasicSelector{
  # Meta variables
  protected $con = NULL;
  public $TableName = "users";
  protected $IdentificationCookie = "_xa";
  protected $DeviceCookie = "_ja";

  # User attributes
  public $user_id = NULL;
  public $email = "";
  public $username = "";
  public $password = "";
  public $type = "";
  public $level = NULL;
  public $is_verified = NULL;
  public $is_active = NULL;

  public $new_user_level = 1;

  # UserObject attributes
  public $UserData = array();
  public $is_logged = False;
  public $lastId = NULL;

  # Identification Fields
  public $field_user_id = "user_id";
  public $field_username = "username";
  public $field_email = "email";
  public $field_password = "password";

  public $field_verification_key = "verification_key";
  public $field_is_verified= "is_verified";

  protected $CredentialsFields = [];
  protected $IdentificationFields = [];

  ## METHODS ##
  public function __construct($con=NULL){
    parent::__construct($this->TableName, NULL, $con);
    $this->setCredentialsFields();
  }
  public function setCredentialsFields(){
       $this->CredentialsFields = [ $this->field_user_id, $this->field_username, $this->field_email, $this->field_password ];
    $this->IdentificationFields = [ $this->field_user_id, $this->field_username, $this->field_email ];
  }

  # Login methods
  public function Login($emailOrUsername,$password){
    $success = False;
    $this->clearUserData();
    $this->deleteCredentials();

    $obscuredPassword = self::ObscurePassword($password);
    $credentials["password"] = $obscuredPassword;
    $credentials["email"] = $emailOrUsername;

    if( $this->credentialsAreValid($credentials) ){
      $success = $this->tryToLogin($credentials);
    }else{
      unset($credentials["email"]);
      $credentials["username"] = $emailOrUsername;
      if( $this->credentialsAreValid($credentials) ){
        $success = $this->tryToLogin($credentials);
      }else{
          if( $this->status() ){
            $this->ErrorManager->handleError( "Usuario o contraseña Incorrectos." );
          }else{
            $this->ErrorManager->handleError( $this->message() );
          }
      }
    }

    if($success){ $this->saveCredentials(); }

    return $success;
  }
  public function LoginWithObscuredPassword($user_id,$obscuredPassword){
    $this->clearUserData();
    $credentials = array(
      "user_id"=> (int) $user_id,
      "password"=>$obscuredPassword
    );

    return $this->tryToLogin($credentials);
  }
  protected function tryToLogin($credentials,$LogoutOnFailure=True){
    $success = False;

    $credentials = self::maskAssocArray($credentials,$this->CredentialsFields);
    if( $this->hasEnoughIdentificationFields($credentials) ){
        if( $this->credentialsAreValid($credentials) ){

            $UserData = $this->pullUserData($credentials);
            if( $this->status() ){
                if( $this->setUserData($UserData) ){
                  $success = True;
                }else{
                  $this->ErrorManager->handleError( $this->message() );
                } // Error when setting Object attributes
            }else{
              $this->ErrorManager->handleError( $this->message() );
            } // Error when pulling Data If
        }else{
          $this->ErrorManager->handleError( $this->message() );
        } // Bad Credentials If
    }else{
      $this->ErrorManager->handleError( "No hay suficientes datos para iniciar la sesión." );
    } // Check for proper idenfication fiels

    if( (!$success) and $LogoutOnFailure ){ $this->Logout(); }

    return $success;
  }

  # Functionals
  public function assertLevel($required_level=1, $redirect_url=NULL ){
    if( (!$this->isLogged()) or ($this->level < $required_level) ){
      $this->Logout($redirect_url);
    }
  }
  public static function FullLoginWithCookie(){
    $User = new UserObject();
    return $User->LoginWithCookie();
  }
  public static function FullLoginWithCookieLevel($required_level=1, $redirect_url=NULL){
    $User = new UserObject();
    $User->LoginWithCookie();
    $User->assertLevel($required_level,$redirect_url);
    return $User;
  }

  # Authentication and Security methods
  public function credentialsAreValid($credentials){
    $credentials = self::maskAssocArray($credentials,$this->CredentialsFields);
    if( sizeof($credentials)>0 ){
      return $this->EXISTS($credentials);
    }else{
      $this->ErrorManager->handleError("No hay suficientes datos para verificar el acceso.");
      return False;
    }
  }
  public function hasEnoughIdentificationFields($NewUserData){
    return
    (
      (    key_exists("username",$NewUserData)
        or key_exists("email",$NewUserData)
        or key_exists("user_id",$NewUserData)
      )and key_exists("password",$NewUserData)
    );
  }
  protected function ObscurePassword($password){
    return sha1( $this->getSalt() . $password );
  }
  private function getSalt(){
    return "viveriveniversumvivusvici";
  }
  public function isLogged(){
    return $this->is_logged;
  }


  # Data retrieving methods
  protected function pullUserData($credentials){
    $credentials = self::maskAssocArray($credentials,$this->CredentialsFields);
    $this->WHERE($credentials);
    $this->PAGE( 0, 1);

    $UserData = $this->execute();
    $this->WHERE->clear();

    if(sizeof($UserData)>0){
      $UserData = $UserData[0];
    }
    return $UserData;
  }
  protected function setUserData($UserData){
    $success = False;
    if( sizeof($UserData)>0 ){
      $success = True;
      $this->is_logged = $success;
      $this->UserData = $UserData;
      foreach($this->UserData as $key => $value){
        try{
          eval('$this->'.$key.' = &$this->UserData["'.$key.'"];');
        }catch (Exception $e){
          $success = False;
          $this->is_logged = False;
          $this->ErrorManager->handleError("Error al extraer información del usuario.", $e, $exitExecution=True);
        } // Try to evaluate attribute Assigments
      } // Foreach Loop
    }else{
      $success = False;
      $this->ErrorManager->handleError("El Registro de éste usuario está vacío.");
    } // Empty Data If

    return $success;
  }

  # Credentials setters
  public function saveCredentials(){
    $this->setCredentialsCookie();
  }
  public function setCredentialsCookie(){
    $this->setIdentificationCookie();
    $this->setDeviceCookie();
  }
  public function setIdentificationCookie(){
    $RandomInteger = rand(100,9999999);
    $RandomString = sha1("a".rand(10000,99999));

    $RealInteger = $this->user_id;
    $RealString = $this->password;

    $IntegerNoise1 = rand(10,99);
    $IntegerNoise2 = rand(10,99);

    $PlusThreeMonths = (time() + 86400 * 90);
    $CookieValue = "$RandomString:$IntegerNoise1$RealInteger$IntegerNoise2:$RandomInteger:$RealString";
    $_COOKIE[$this->IdentificationCookie] = $CookieValue;
    setcookie($this->IdentificationCookie, $CookieValue, $PlusThreeMonths, "/",$_SERVER['SERVER_NAME']);
  }
  public function setDeviceCookie(){
    $PlusThreeMonths = (time() + 86400 * 90);
    $CookieValue = self::getDeviceCookie();
    $_COOKIE[$this->DeviceCookie] = $CookieValue;
    setcookie($this->DeviceCookie, $CookieValue, $PlusThreeMonths, "/",$_SERVER['SERVER_NAME']);
  }
  protected function getDeviceCookie(){
    return sha1( $this->getSalt() . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"] );
  }

  # Logout and Cleaning methods
  public function Logout($redirect_url=NULL){
    $this->clearUserData();
    $this->deleteCredentials();
    if( !is_null($redirect_url) ){
      header("Location: $redirect_url ");
    }
  }
  public function clearUserData(){
    $this->user_id = NULL;
    $this->email = "";
    $this->username = "";
    $this->password = "";

    $this->type = "";
    $this->level = NULL;
    $this->is_verified = NULL;
    $this->is_active = NULL;

    $this->UserData = array();
    $this->is_logged = False;
  }
  protected function deleteCredentials(){
    $this->unsetCredentialsCookie();
  }
  protected function unsetCredentialsCookie(){
    $MinusOneYear = (time() - 86400 * 365);

    if( $this->isIdentificationCookieSet() ){
      setcookie($this->IdentificationCookie, "deleted", $MinusOneYear, "/", $_SERVER['SERVER_NAME'] );
      if(isset($_COOKIE[$this->IdentificationCookie] )){
        unset( $_COOKIE[$this->IdentificationCookie] );
      }
    }

    if( $this->isDeviceCookieSet() ){
      setcookie($this->DeviceCookie, "deleted", $MinusOneYear, "/", $_SERVER['SERVER_NAME']);
      if(isset($_COOKIE[$this->DeviceCookie] )){
        unset( $_COOKIE[$this->DeviceCookie] );
      }
    }

  }

  # Login with Cookie methods
  public function LoginWithCookie(){
    $success = False;

    if( $this->isCookieLoginPossible() ){
      if( $this->isThisDeviceValid() ){
        list($user_id,$obscuredPassword) = $this->getCredentialsFromCookie();

        if( $this->LoginWithObscuredPassword($user_id,$obscuredPassword) ){
          $success = True;
        }else{
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
    return
    (
      $this->isIdentificationCookieSet()
      and $this->isDeviceCookieSet()
    );
  }
  public function isIdentificationCookieSet(){
    return key_exists($this->IdentificationCookie,$_COOKIE);
  }
  public function isDeviceCookieSet(){
    return key_exists($this->DeviceCookie,$_COOKIE);
  }
  public function isThisDeviceValid(){
    return ( $_COOKIE[$this->DeviceCookie] == self::getDeviceCookie() );
  }
  public function getCredentialsFromCookie(){
    $user_id = NULL;
    $obscuredPassword = NULL;

    if( $this->isIdentificationCookieSet() ){
      list($rubish1,$noisy_id,$rubish2,$obscuredPassword) = explode(":",$_COOKIE[$this->IdentificationCookie]);
      $noisy_id = strval($noisy_id);
      $user_id = (int) substr($noisy_id,2,-2);
    }

    return [$user_id,$obscuredPassword];
  }

  # New User methods
  public function NewUser(array $UserData,$LoginAfterInsert=True,$is_verified=True,$checkRegisters=False){
    $success = False;
    $ErrorMessage = $this->ValidateNewUserInput($UserData);

    if( $ErrorMessage == "" ){
        $UserData = $this->maskUserDataAndGrantDefaultAccess($UserData, $is_verified);
        $UserData["password"] = self::ObscurePassword( $UserData["password"] );

        $this->INSERTUSER($UserData);

        if( ! is_null($this->lastId) ){
            $success = True;
            if( $LoginAfterInsert ){
                $UsernameOrEmail = self::extractEmailOrUsername($UserData);
                if( ! $this->LoginWithObscuredPassword( $UsernameOrEmail, $UserData["password"] ) ){
                  $this->ErrorManager->handleError("Error al entrar a la Cuenta recien creada.");
                }
            }
        }else{$this->ErrorManager->handleError("Error al Agregar Usuario a la base de datos." );}
    }

    return $success;
  }
  public function ValidateNewUserInput($UserData,$checkRegisters=False){
    $ErrorMessage = "";
    $ErrorMessage .= ( $this->hasEnoughIdentificationFields($UserData) ) ? "" :
                       "Datos insuficientes para crear Usuario nuevo.";
    $ErrorMessage .= ( ! $this->checkIfUserExists($UserData) ) ? "" :
                       "Este usuario ya existe.";
    $ErrorMessage .= ( self::isValidEmail($UserData["email"], $checkRegisters) or key_exists("username",$UserData)) ? "" :
                       "El email no es válido.";
    return $ErrorMessage;
  }
  public static function extractEmailOrUsername($UserData){
    return ( key_exists("username",$UserData) ) ? $UserData["username"] : $UserData["email"];
  }
  public function maskUserDataAndGrantDefaultAccess($UserData, $is_verified){
    $maskedUserData = self::maskAssocArray($UserData,$this->getTableFields());
    $maskedUserData = $this->grantBaseAccess($maskedUserData);
    $maskedUserData["is_verified"] = (int) $is_verified;
    return $maskedUserData;
  }

  public function checkIfUserExists(array $UserData){
    $MaskedUserData = self::maskAssocArray($UserData,$this->IdentificationFields);
    return ( $this->EXISTS($MaskedUserData) );
  }
  protected function grantBaseAccess($NewUser){
    $NewUser["level"] = $this->new_user_level;
    $NewUser["is_active"] = 1;

    return $NewUser;
  }
  protected function INSERTUSER($UserData){
    $this->lastId = NULL;

    if( $this->hasEnoughIdentificationFields($UserData) ){
      $INSERT = new SQLInsert( $this->TableName, NULL, $this->con );
    	$INSERT->INSERT( $UserData );
    	if( $INSERT->execute() ){
          // Insertion was succesful;
          $this->lastId = $INSERT->lastId;
      }else{
        $this->ErrorManager->handleError("Error al crear nuevo usuario.", $INSERT->ErrorManager->getErrorObject() );
      }
    }else{
      $this->ErrorManager->handleError("Datos insuficientes para crear Usuario nuevo.");
    }

    return $this->lastId;
  }

  # Other methods
  public static function isValidEmail($email,$checkRegisters=False){
		$isValid = True;
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

       if( $checkRegisters ){
         //Check MX and A registers online for given email
         if ( $isValid && ! (checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) ){
            $isValid = False;
         }
       }

    }
    return $isValid;
	}

  public static function ROOT(){
    // Get Full domain's URL
    $http = ( ! empty($_SERVER["HTTPS"]) ? "https":"http");
    $ROOT =  "$http://$_SERVER[HTTP_HOST]/";
    return $ROOT;
  }
}

function VerifyUser($verificationid){
  $User = new UserObject();
  $verficationWhere = array("$User->field_verification_key" => $verificationid);

  if( $User->EXISTS($verficationWhere) ){
    require_once( MODULE_ROUTE_SQL . "SQLUpdate.php" );
    $UPDATE = new SQLUpdate( $User->TableName, [ $User->field_verification_key, $User->field_is_verified ], $User->getConnection() );
    list( $verification, $id) = explode("-",$verificationid,2);
  	$set = array(
      "$User->field_verification_key" => "",
      "$User->field_is_verified" => 1
    );
  	$UPDATE->SET( $set );
    $UPDATE->WHERE( $verficationWhere );
  	if( $UPDATE->execute() ){
      return True;
    }else{
      echo "Error al actualizar el estado de verificación del usuario. $UPDATE->message() ";
      return False;
    }
  }else{
    echo "El link de verficación no es válido.";
    return False;
  }
}

function CreateVerificationKey($user_id){
  $user_id = (int) $user_id;
  require_once( MODULE_ROUTE_SQL . "SQLUpdate.php" );

  $key = sha1( rand(1,100000000) ) . "-" . $user_id;
  $User = new UserObject();
  $UPDATE = new SQLUpdate( $User->TableName, [ $User->field_verification_key ], $User->getConnection() );

  $UPDATE->SET( array("$User->field_verification_key" => $key) );
  $UPDATE->WHERE( array("$User->field_user_id" => $user_id) );

  if( $UPDATE->execute() ){
    return $key;
  }else{
    echo $UPDATE->message();
    return NULL;
  }

}
