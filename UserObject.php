<?php
// Refactor
class UserObject extends SQLBasicSelector{
  public $con = NULL;
  protected $IdentificationCookie = "_xa";
  protected $DeviceCookie = "_ja";
  public $TableName = "users";

  public $user_id = NULL;
  public $username = "";
  public $password = "";
  public $email = "";
  public $type = "";
  public $level = NULL;
  public $is_verified = NULL;
  public $is_active = NULL;

  public $UserData = array();
  protected $is_logged = False;

  protected $CredentialsFields = ['user_id','username','email',"password"];
  protected $IdentificationFields = ['user_id','username','email'];
  public $lastId = NULL;

  public function __construct($con=NULL){
    parent::__construct($this->TableName, NULL, $con);
  }

  # New User Methods
  public function NewUser(array $UserData,$LoginAfterInsert=True,$is_verified=1){
    $success = False;
    if( $this->hasEnoughIdentificationFields($UserData) ){
        if( ! $this->checkIfUserExists($UserData) ){
            // Grant basic access
            $maskedUserData = self::maskAssocArray($UserData,$this->getTableFields());
            $this->grantBaseAccess($maskedUserData);

            // Set Password and Verification
            $regularPassword = $maskedUserData["password"];
            $obscuredPassword = self::ObscurePassword( $maskedUserData["password"] );
            $maskedUserData["password"] = $obscuredPassword;
            $maskedUserData["is_verified"] = (int) $is_verified;

            $this->lastId = $this->INSERTUSER($maskedUserData);
            if( ! is_null($this->lastId) ){
                if( $LoginAfterInsert ){
                  $UsernameOrEmail = ( key_exists("username",$maskedUserData) )
                    ? $maskedUserData["username"] : $maskedUserData["email"];
                  if( $this->Login($UsernameOrEmail,$regularPassword) ){
                    $success = True;
                    # Bravo! You created a New User AND you logged in with it.
                  }else{
                    $this->ErrorManager->handleError("Error al entrar a la Cuenta recien creada.");
                  } //Check if Login was succesful
                }else{
                  $success = True;
                  # Bravo! You created a New User (but you didn't Log in)
                }
            }else{
                $this->ErrorManager->handleError("Error al Agregar Usuario a la base de datos." );
            } // Check If Insertion was succesful If
        }else{
          $this->ErrorManager->handleError("Este usuario ya existe." );
          $this->Logout();
        } // Check if user alredy Exists If
    }else{
      $this->ErrorManager->handleError("Datos insuficientes para crear Usuario nuevo." );
    } // Not enough credentials If

    return $success;
  }
  public function checkIfUserExists(array $UserData){
    $MaskedUserData = self::maskAssocArray($UserData,$this->IdentificationFields);
    $Exists = $this->EXISTS($MaskedUserData);
    return $Exists;
  }
  public function grantBaseAccess(&$NewUserData){
    $NewUserData["level"] = 0;
    $NewUserData["type"] = "Regular";
    $NewUserData["is_active"] = 1;
  }
  public function INSERTUSER($NewUserData){
    $this->lastId = NULL;
    $maskedUserData = self::maskAssocArray($NewUserData,$this->getTableFields());

    if( $this->hasEnoughIdentificationFields($maskedUserData) ){
      $INSERT = new SQLInsert( $this->TableName, NULL, $this->con );
    	$INSERT->INSERT( $maskedUserData );
    	if( ! $INSERT->execute() ){
        $this->ErrorManager->handleError("Error al crear nuevo usuario.", $INSERT->ErrorManager->getErrorObject() );
      }
    }else{
      $this->ErrorManager->handleError("Datos insuficientes para crear Usuario nuevo.");
    }

    return $INSERT->lastId;
  }
  public function hasEnoughIdentificationFields($NewUserData){
    return (
      ( key_exists("username",$NewUserData)
        or key_exists("email",$NewUserData)
        or key_exists("user_id",$NewUserData) )
      and key_exists("password",$NewUserData)
    );
  }
  public static function ObscurePassword($password){
    $sal = "viveriveniversumvivusvici";
    $encrypted_password = sha1($sal.$password);
    return $encrypted_password;
  }

  # Login Methods
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
        $this->ErrorManager->handleError( $this->message() );
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
  # Autentication and Login methods
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
            $this->is_logged = $success;
            $this->ErrorManager->handleError("Error al extraer información del usuario.", $e, $exitExecution=True);
          } // Try to evaluate attribute Assigments
      } // Foreach Loop

    }else{
      $success = False;
      $this->ErrorManager->handleError("El Registro de éste usuario está vacío.");
    } // Empty Data If

    return $success;
  }
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

    $value = "$RandomString:$IntegerNoise1$RealInteger$IntegerNoise2:$RandomInteger:$RealString";
    $OneDay = 86400;
    $_COOKIE[$this->IdentificationCookie] = $value;
    setcookie($this->IdentificationCookie, $value, time() + ($OneDay*100), "/",$_SERVER['SERVER_NAME']);
  }
  public function setDeviceCookie(){
    $OneDay = 86400;
    $value = self::getDeviceCookie();
    $_COOKIE[$this->DeviceCookie] = $value;
    setcookie($this->DeviceCookie, $value, time() + ($OneDay*100), "/",$_SERVER['SERVER_NAME']);
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
    $user_id = NULL;
    $obscuredPassword = NULL;
    if( $this->isIdentificationCookieSet() ){
      list($rubish1,$noisy_id,$rubish2,$obscuredPassword) = explode(":",$_COOKIE[$this->IdentificationCookie]);

      $noisy_id = strval($noisy_id);
      $user_id = (int) substr($noisy_id,2,-2);

    }
    return [$user_id,$obscuredPassword];
  }

  # Logout and Cleaning Methods
  public function Logout(){
    $this->clearUserData();
    $this->deleteCredentials();
  }
  protected function clearUserData(){
    $this->user_id = NULL;
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
    $OneYear = 86400 * 365;
    if( $this->isIdentificationCookieSet() ){
      setcookie($this->IdentificationCookie, "deleted", time() - $OneYear, "/", $_SERVER['SERVER_NAME'] );
      if(isset($_COOKIE[$this->IdentificationCookie] )){
        unset( $_COOKIE[$this->IdentificationCookie] );
      }
    }

    if( $this->isDeviceCookieSet() ){
      setcookie($this->DeviceCookie, "deleted", time() - $OneYear, "/", $_SERVER['SERVER_NAME']);
      if(isset($_COOKIE[$this->DeviceCookie] )){
        unset( $_COOKIE[$this->DeviceCookie] );
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
