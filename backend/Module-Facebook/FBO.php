<?php
class Facebook{

  ## PLATFORM METHODS ##
  # facebook connection methods
  public function __construct($Token = NULL, $FacebookConnection = NULL){
    # Start Framework and Session if needed
    if (session_status() == PHP_SESSION_NONE) {session_start();}
    define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ . '/facebook-sdk-v5/');
    require_once __DIR__ . '/facebook-sdk-v5/autoload.php';

    # Connect to Facebook is $FB is not given
    if( is_null($FacebookConnection) ){
      $this->Connect2Facebook();
    }

    # Manage Token: Catch or load Token if Token is not given
    if( is_null($Token) ){
      if( isset($_GET['code']) ){
        $this->catchToken();
        $this->refreshToken();
        $this->saveToken();
      }
    $this->loadToken();
    }

  }
  public function processError($errorMessage, $errorObject = NULL, $exitExecution = True){
    # Every error in this Object leads here.

    # Concatenate message and set status to False
    $parsedMessage = ( is_null($errorObject) ) ? "" : $errorObject->getMessage();
    $this->status = False;
    $this->message = $errorMessage . " " . $parsedMessage;
    echo $this->message;

    if($exitExecution){exit;}
  }
  public function Connect2Facebook(){
    # Connect to FB with the credentials
    try{
      $this->FacebookConnection = new Facebook\Facebook([
        'app_id' => $this->app_id,
        'app_secret' => $this->app_secret,
        'default_graph_version' => 'v2.8']);
    }catch(Exception $e){
      $this->processError("Error en CONNECT.",$e);
    }
  }

  # token handling methods
  public function catchToken(){
  	$loginHelper = $this->FacebookConnection->getRedirectLoginHelper();
    $newToken = NULL;

    # Try to get a new Token
  	try {
  	  $newToken = $loginHelper->getAccessToken();
  	} catch(Facebook\Exceptions\FacebookResponseException $e) {
      $this->processError("Error al recuperar el Token en catchToken.", $e);
  	} catch(Facebook\Exceptions\FacebookSDKException $e) {
      $this->processError("Error del SDK en catchToken.", $e);
  	}

    # If you fail, set Token to NULL
    $this->Token = ( is_null($newToken) ) ? NULL : $newToken;

  	return $this->Token;
  }
  public function refreshToken(){
    $oldToken = $this->Token;
    $newToken = NULL;

    # Try to enlongate Token
    if( !is_null($oldToken) ){
        try{
        	$oAuth2Client = $this->FacebookConnection->getOAuth2Client();
        	$newToken = $oAuth2Client->getLongLivedAccessToken($oldToken);
        }catch(Exception $e){
          $this->processError("Error en refreshToken.",$e);
        }
    }else{
        $this->processError("No hay Token previo en refreshToken.");
    }

    $this->Token = $newToken;
  	return $this->Token;
  }
  public function saveToken( $saveTokenWith = NULL ){
    # Save Token depending on the method
    $Token = $this->Token;

    $saveTokenWith = ( is_null($saveTokenWith) ) ? $this->saveTokenWith : $saveTokenWith;

    # If saving with a COOKIE then:
    if($saveTokenWith=="COOKIE"){
      $_COOKIE["FBTOKEN"] = $Token;
      setcookie("FBTOKEN", $Token, time() + 3600*24*30 ); //1 month
    }

  }
  public function loadToken( $saveTokenWith = NULL){
    # Load Token depending on the method
    // Get saving method
    $saveTokenWith = ( is_null($saveTokenWith) ) ? $this->saveTokenWith : $saveTokenWith;

    # Loading from a COOKIE
    if($saveTokenWith=="COOKIE"){
      $this->Token = ( isset($_COOKIE["FBTOKEN"]) ) ? $_COOKIE["FBTOKEN"] : NULL;
    }
  }
  public function hasToken(){
    # Return if Object has a Token
    return ( !is_null($this->Token) );
  }

  public function getLogin($callbackURL = "", $isRelativePath = True, $userPermissions =
              ['public_profile','email','user_likes','user_friends',
              'pages_show_list','read_page_mailboxes','read_insights',
              'manage_pages','publish_pages','pages_messaging'] ){
    # Get Login URL for the user to grant access to Facebook

    $loginURL = "";
    $callbackURL = ($isRelativePath) ? "http://$_SERVER[HTTP_HOST]/$callbackURL" : "$callbackURL";

    # Try to request Login URL
    try{
    	$loginHelper = $this->FacebookConnection->getRedirectLoginHelper();
    	$loginURL = $loginHelper->getLoginUrl($callbackURL, $userPermissions);
    }catch(Exception $e){
      $this->processError("Error en getLogin.",$e);
    }

  	return htmlspecialchars($loginURL);
  }

  ## DATA RETRIEVING METHODS ##
  public function GET($get, $Token = NULL){
  	$fb = &$this->FacebookConnection;
    $Token = (is_null($Token)) ? $this->Token : $Token;

    try{
      $fb->setDefaultAccessToken($Token);
    }catch(Exception $e){
      $this->processError("Error al dar Token en GET.",$e);
    }

  	try {
  	  $response = $fb->get($get);
  	} catch(Facebook\Exceptions\FacebookResponseException $e) {
      $this->processError("Error de Respuesta en GET.",$e);
  	} catch(Facebook\Exceptions\FacebookSDKException $e) {
      $this->processError("Error al SDK en GET.",$e);
  	}

    try{
      $datos = $response->getDecodedBody();
    }catch(Exception $e){
      $this->processError("Error al crear array en GET.",$e);
    }

  	return $datos;
  }
  public function getPAGE($page){
    $identification = ["id","username","name","link"];
    $image = ["cover","picture"];
    $info = ["founded","category","category_list","description","products","about"];
    $contact = ["emails","phone","website"];
    $location = ["location","place_type","can_checkin"];
    $insights = ["fan_count","were_here_count","checkins",
    "rating_count","talking_about_count","overall_star_rating"];

    $fields = array_merge($identification,$image,$info,$contact,$location,$insights);
    $fields = implode(",",$fields);

    $query = "$page?fields=$fields";

    try{
      $this->GET($query);
      $this->datos = $this->parsePAGE( $this->datos );
    }catch(Exception $e){
      $this->processError("Error en PAGE.",$e);
    }
    return $this->datos;
  }
  public function getPAGETOKEN($page){
    $query = "$page?fields=access_token";

    try{
      $this->GET($query);
    }catch(Exception $e){
      $this->processError("Error en PAGETOKEN.",$e);
    }

    return $this->datos;
  }

  public function getCONVERSATIONS($pageIdentifier , $parseMessages = False){
    $limit = 1000;
    $conversationFields = ["id","link","message_count","participants",
      "unread_count","updated_time","can_reply","is_subscribed"];
    $conversationFields = implode( "," , $conversationFields);

    if($parseMessages){
      $messagesFields = ["message","attachments","id","from","created_time"];
      $messagesFields = implode( "," , $messagesFields);
      $messagesFields = ",messages.limit($limit){ $messagesFields}";
    }else{
      $messagesFields = "";
    }

    $query = "$pageIdentifier/conversations?fields=$conversationFields$messagesFields&limit=$limit";

    $pageToken = $this->getPageAccessTokenOf( $pageIdentifier );
    try{
      $this->Conversations = $this->GET( $query , $pageToken );
      $this->Conversations = $this->parseCONVERSATIONS( $this->Conversations , $parseMessages );
    }catch(Exception $e){
      $this->processError("Error en CONVERSATIONS.",$e);
    }
  }
  public function getAllCONVERSATIONS(){
    $allConversations = array();
    $this->Accounts = $this->getACCOUNTS( True );
    foreach($this->Accounts as $Account){
      $this->getCONVERSATIONS( $Account["id"] );
      $allConversations["id_fanpage_$Account[id]"] = $this->Conversations;
    }

    return $allConversations;
  }
  public function getACCOUNTS( $getAccessTokens = True, $minimumAdminLevel = 3 ){
    $fields = ["id","name","perms","username","instagram_accounts","picture","cover"];
    if($getAccessTokens){ $fields[] = "access_token"; }
    $fields = implode(",",$fields);

    $query = "me/accounts?fields=$fields";

    try{
      $this->Accounts = $this->GET( $query );
      $this->Accounts = $this->parseACCOUNTS( $this->Accounts , $minimumAdminLevel );
    }catch(Exception $e){
      $this->processError("Error en ACCOUNTS.",$e);
    }

    return $this->Accounts;
  }
  public function getPageAccessTokenOf( $pageIdentifier ){
    $pageToken = NULL;
    $this->Accounts = ( sizeof($this->Accounts)==0 ) ? $this->getACCOUNTS( True ) : $this->Accounts;

    foreach($this->Accounts as $Account){
      if( ($Account["id"] == $pageIdentifier) or ($Account["username"] == $pageIdentifier) ){
        $pageToken = $Account["access_token"];
        break;
      }
    }
    return $pageToken;
  }


  ## DATA HANDLING METHODS ##
  # main parsing methods
  public static function parsePAGE( $rawPage ){
    $parsedPage = array();

    $parseByNullKeys = ["id","username","name","link","founded","category","products",
    "about","phone","website","place_type","fan_count","were_here_count",
    "checkins","rating_count","talking_about_count","overall_star_rating"];
    foreach($parseByNullKeys as $key){self::parseNULL($key, $parsedPage, $rawPage);}

    $parseByBooleanKeys = ["can_checkin"];
    foreach($parseByBooleanKeys as $key){self::parseBoolean($key, $parsedPage, $parsedPage);}

    $parseBy2Keys =[ ["cover","source"], ["location","city"],
    ["location","country"], ["location","latitude"], ["location","longitude"],
    ["location","street"], ["location","zip"] ];
    foreach($parseBy2Keys as $keys){
      list($key1, $key2) = $keys;
      self::parse2Keys($key1, $key2, $parsedPage, $rawPage);
    }

    # Specific Parsings
    $parsedPage["emails"] = isset($rawPage["emails"]) ? implode(";", $rawPage["emails"]) : NULL;
    $parsedPage["picture_data_url"] = isset($rawPage["picture"]["data"]["url"]) ? $rawPage["picture"]["data"]["url"] : NULL;

    # Parse all possible Page categories as a concatenated string with : and ;
    if( isset($rawPage["category_list"]) ){
      $parsedCategoriesList = [];
      foreach( $rawPage["category_list"] as $rawCategory ){
        $parsedCategoriesList[] = "$rawCategory[id]:$rawCategory[name]";
        // $parsedCategoriesList[] = "468684511518871:Category Long Name;";
      }
      $parsedPage["category_list"] = implode(";",$parsedCategoriesList);
    }else{
      $parsedPage["category_list"] = NULL;
    }

    return $parsedPage;
  }
  public static function parseCONVERSATIONS ( $rawConversations , $parseMessages = False ){
    $oneShotParsingFields = ["id","link","updated_time","message_count","unread_count",
      "can_reply","is_subscribed"];
    $parsedConversations = array();

    $rowIndex = 0;
    foreach($rawConversations["data"] as $singleConversation){

      $parsedConversations[$rowIndex]["id_fanpage"] = $singleConversation["participants"]["data"][1]["id"];
      $parsedConversations[$rowIndex]["from_id"] = $singleConversation["participants"]["data"][0]["id"];
      $parsedConversations[$rowIndex]["from_email"] = $singleConversation["participants"]["data"][0]["email"];
      $parsedConversations[$rowIndex]["from_name"] = $singleConversation["participants"]["data"][0]["name"];

      foreach($oneShotParsingFields as $field){
        if( array_key_exists($field, $singleConversation) ){
          $parsedConversations[$rowIndex][$field] = $singleConversation[$field];
        }else{
          $parsedConversations[$rowIndex][$field] = NULL;
        }
      }//foreach fields
      $parsedConversations[$rowIndex]["unread_count"] =
        ( is_null($parsedConversations[$rowIndex]["unread_count"]) )
         ? 0 : $parsedConversations[$rowIndex]["unread_count"];
      $rowIndex++;
    }//foeach conversations

    return $parsedConversations;
  }
  public static function parseACCOUNTS( $rawAccounts , $minimumAdminLevel = 3 ){
    $parsedAccounts = array();
    $rowIndex = 0;
    foreach($rawAccounts["data"] as $singleAccount){
      # Check if account is worth parsing
      $adminLevel = sizeof($singleAccount["perms"]);
      if($adminLevel < $minimumAdminLevel){continue;}

      $parsedAccounts[$rowIndex]["admin_level"] = $adminLevel;

      $parseByNullKeys = ["id","name","access_token","username"];
      foreach($parseByNullKeys as $key){
        $parsedAccounts[$rowIndex][$key] = self::issetKey($singleAccount, $key);
      }

      $parsedAccounts[$rowIndex]["picture"] = ( isset($singleAccount["picture"]["data"]["url"]) ) ?
      $singleAccount["picture"]["data"]["url"] : NULL;

      $parsedAccounts[$rowIndex]["cover"] = ( isset($singleAccount["cover"]["source"]) ) ?
      $singleAccount["cover"]["source"] : NULL;

      # Manage possible multiple Intagrams accounts for a single Facebook page.
      $parsedAccounts[$rowIndex]["instagram_accounts"] = NULL;
      if( isset($singleAccount["instagram_accounts"]) ){
        $parsedInstagramAccountsList = [];
        foreach($singleAccount["instagram_accounts"]["data"] as $rawInstagramAccount){
          $parsedInstagramAccountsList[] = (int) $rawInstagramAccount["id"];
        }
        $parsedAccounts[$rowIndex]["instagram_accounts"] = implode(";", $parsedInstagramAccountsList);
      }

      $rowIndex ++;
    }//foreach account
    return $parsedAccounts;
  }

  # secondary parsing methods
  public static function parseNULL($key,&$referencedData,$rawData){
    # Check if a key exists: return its Value or NULL
    # Add the key back to $parsed (by reference)
    $referencedData[$key] = ( isset($rawData[$key]) ) ? $rawData[$key] : NULL;
  }
  public static function parseBoolean($key,&$referencedData,$rawData){
    # Check if key exists: return NULL or Boolean to $parsed (by reference)
    $referencedData[$key] = ( isset($rawData[$key]) ) ? $rawData[$key] : NULL;
  }
  public static function parse2Keys($key1,$key2,&$referencedData,$rawData){
    # Check if array with 2 given keys exists: return its Value or NULL (by reference)
    $jointKey = $key1."_".$key2;
    $referencedData[$jointKey] = ( isset($rawData[$key1][$key2]) ) ? $rawData[$key1][$key2] : NULL;
  }
  public static function issetKey($array,$key){
    # Check if an associative array has a key declared and return its Value.
    $keyValue = ( isset($array[$key]) ) ? $array[$key] : NULL;
    return $keyValue;
  }


  ## DATA DISPLAYING METHODS ##
  public function json( $data = NULL, $echoJSON = True, $setJSONheader = True){
    # Turns current state data into an API-friendly JSON representation.
		$datos['status'] = $this->status;
		$datos['message'] = $this->message;
		$datos["data"] = ( is_null($data) ) ? $this->datos : $data;

		if($setJSONheader){ header('Content-Type: application/json'); }

    $JSON = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		if($echoJSON){echo $JSON;}

		return $JSON;
	}
  public function flush(){
    # Echoes current state as Raw data.
    $statusAsString = ($this->status) ? "True" : "False";
    echo "status: ". $statusAsString."</br>";
    echo "message: ". $this->message."</br>";
    print_r($this->datos);
  }


  ## OBJECT VARIABLES ##
  protected $app_secret = 'c478fbfb19e9f22f688b5db5144086c7';
  protected $app_id = '632416283438924';

  # default connection values
  public $FacebookConnection = NULL;
  public $saveTokenWith = "COOKIE";
  public $Token = NULL;

  # default state values
  public $status = True;
  public $message = "";

  public $datos = array();
  public $Accounts = array();
  public $Conversations = array();
}
?>
