<?php
$fb_app_secret = 'c478fbfb19e9f22f688b5db5144086c7';
$fb_app_id = '632416283438924';
include_once( ROOT . "/backend/Loaders/SET_MODULE_ROUTE.php");

$fb_connection = FacebookConnection::Set($fb_app_secret,$fb_app_id);
$Yo = "EAAIZCLePsr0wBABDGkkVCZAgmT5MBsOz9BJ1ksTmb3u4MUmW4idjFxZChYTF2q4wdLZAdoSuEb1YYpqNYZCZC3yBRRVr6KBUhbrV3d7uHH5WZAUowfZAJiTpljnxPCTHd3DOZAN9MZANeKaAjKTddoXgy1IjIhV89RKXAZD";
$MKTi = "EAAIZCLePsr0wBAKgV8yRXlG26FZBalOrKpOfJOfLHgxZA7qmMiA2BB2bUlERNPIK8Ha1hWBbuMQUdphXeHmhp30TqDZCz6yrxa55AufoBZCx9DQV3RHLRbbhewYleikCIuaDhQlRVAZADrmch9Ur6A1QHShHF7HsTnZAJbopHXmlAZDZD";

class FacebookConnection{
  protected $app_secret = NULL;
  protected $app_id = NULL;
  protected $version = "v2.9";
  public $con = NULL;

  public function __construct($app_secret=NULL,$app_id=NULL){
    $this->ErrorManager = new ErrorManager();
    $this->loadSDK();
    $this->setConnection($app_secret,$app_id);
  }
  public static function Set($fb_app_secret,$fb_app_id){
    $FacebookConnectionObject1 = new FacebookConnection($fb_app_secret,$fb_app_id);
    return $FacebookConnectionObject1->con;
  }
  protected function loadSDK(){
    if (session_status() == PHP_SESSION_NONE) {session_start();}
    define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ . '/facebook-sdk-v5/');
    require_once __DIR__ . '/facebook-sdk-v5/autoload.php';
  }
  # Connection methods
  public function setConnection($app_secret=NULL,$app_id=NULL){
    if( is_null($app_secret) or is_null($app_id) ){
      list($this->app_secret,$this->app_id) = $this->getCredentialsFromEnviroment();
    }else{
      $this->app_secret = $app_secret;
      $this->app_id = $app_id;
    }
    $this->connect();
  }
  private function getCredentialsFromEnviroment(){
		if( isset($GLOBALS["fb_app_secret"]) and isset($GLOBALS["fb_app_id"]) ){
      return [$GLOBALS["fb_app_secret"], $GLOBALS["fb_app_id"]];
		}else{
      $this->ErrorManager->handleError("No Global Facebook Connection detected." );
      return [NULL,NULL];
    }
	}
  protected function connect(){
    try{
      $this->con = new Facebook\Facebook([
        'app_id' => $this->app_id,
        'app_secret' => $this->app_secret,
        'default_graph_version' => $this->version]);
    }catch(Exception $e){
      $this->ErrorManager->handleError("Error when connecting to Facebook", $e );
    }
  }
  public function status(){
    return $this->ErrorManager->getStatus();
  }
  public function message(){
    return $this->ErrorManager->getMessage();
  }
}

class FacebookObject{
  public $con = NULL;
  public $Token = NULL;
  public $Query = NULL;

  public $LastFunction = NULL;

  public $limit = 100;
  public $size = NULL; // Integer

  public $since = NULL;
  public $until = NULL;

  public $UntilOrSince = "";

  protected $BasicUserPermissions = ['public_profile','user_friends','email'];
  protected $MidUserPermissions = ['public_profile','email','user_likes',];
  protected $UserPermissions = ['public_profile','email','pages_show_list', 'read_page_mailboxes',
  'read_insights','manage_pages','publish_pages','pages_messaging'];

  protected $AccountsFields = ["access_token","name","id","perms",
  "username","instagram_accounts","picture","cover"];

  public $reponse = NULL;
  public $data = array();
  public $accounts = NULL;

  protected $ErrorManager;

  public function __construct($Token=NULL,$con=NULL){
    $this->ErrorManager = new ErrorManager();

    $this->SetOrSenseConnection($con);
    $this->SetOrLoadToken($Token);
  }
  protected function SetOrSenseConnection($con){
    if( is_null($con) ){
      $this->getConnectionFromEnviroment();
    }else{
      $this->con = $con;
    }
  }
  protected function SetOrLoadToken($Token){
    if( ! is_null($Token) ){
      $this->setToken($Token);
    }else{
      $this->loadTokenIfPossible();
    }
  }
  protected function getConnectionFromEnviroment(){
    if( isset($GLOBALS["fb_connection"]) ){
      $this->con = $GLOBALS["fb_connection"];
		}else{
      $this->con = NULL;
    }
  }
  public function hasConnection(){
    return is_null($this->con);
  }
  # Login and User methods
  public function getLoginURL($callback=NULL,$UserPermissions=NULL){
    // Set Callback
    $thisFile = basename($_SERVER['PHP_SELF']);
    $callback = is_null($callback) ? (HTTPS."://$_SERVER[HTTP_HOST]/$thisFile") : $callback;
    // Set Permissions
    $UserPermissions = (is_null($UserPermissions)) ? $this->UserPermissions : $UserPermissions;

    try{
    	$loginHelper = $this->con->getRedirectLoginHelper();
    	$loginURL = $loginHelper->getLoginUrl($callback, $UserPermissions);
    }catch (Exception $e){
      $this->ErrorManager->handleError("Error when generating Facebook Login URL.", $e );
    }

  	return htmlspecialchars($loginURL);
  }

  # Token methods
  public function isNewTokenAvailable(){
    return array_key_exists("code",$_GET);
  }
  public function catchToken(){
    // $code =  $_GET["code"];
    $loginHelper = $this->con->getRedirectLoginHelper();
  	try {
  	  $this->Token = $loginHelper->getAccessToken();
      $this->setDefaultAccessToken();
  	} catch(Facebook\Exceptions\FacebookResponseException $e) {
      $this->ErrorManager->handleError("Facebook RESPONSE Exception in catchToken.", $e );
  	} catch(Facebook\Exceptions\FacebookSDKException $e) {
      $this->ErrorManager->handleError("Facebook SDK Exception in catchToken.", $e );
  	}

  	return $this->Token;
  }
  public function extendToken(){
    if( $this->hasToken() ){
        try{
        	$oAuth2Client = $this->con->getOAuth2Client();
        	$this->Token = $oAuth2Client->getLongLivedAccessToken($this->Token);
          $this->setDefaultAccessToken();
        }catch(Exception $e){
          $this->processError("Error at extendToken.", $e);
        }
    }else{
      $this->ErrorManager->handleError( "No Token Available for Extension." );
    }
  	return $this->Token;
  }
  public function saveToken(){
    setcookie("fbt", $this->Token, time() + 3600*24*30*6 ); //6 months
    $_COOKIE["fbt"] = $this->Token;
  }
  public function loadToken(){
    $this->Token = ( array_key_exists("fbt",$_COOKIE) ) ? $_COOKIE["fbt"] : NULL;
    $this->setDefaultAccessToken();
    return $this->Token;
  }

  public function loadTokenIfPossible(){
    if( $this->isLoadTokenPossible() ){
      $this->loadToken();
    }
  }
  public function isLoadTokenPossible(){
    return array_key_exists("fbt",$_COOKIE);
  }
  public function hasToken(){
    return ( !is_null( $this->Token ) );
  }
  public function setToken($Token){
    $this->Token = $Token;
    $this->setDefaultAccessToken();
  }
  protected function setDefaultAccessToken(){
    if( $this->hasToken() ){
      try{
        $this->con->setDefaultAccessToken( $this->Token );
      }catch(Exception $e){
        $this->ErrorManager->handleError( "Error when setting Default Facebook Token.", $e);
      }
    }
  }

  # API basic methods
  public function me( $fields=["id","name","email"] ){
    $fields = self::commaSeparated($fields);
    return $this->GET("me?fields=$fields");
  }
  public function email(){
    $UserData = $this->me();
    return $UserData["email"];
  }

  public function GET($Query=NULL,$until=NULL,$since=NULL){
    $this->Query =  (is_null($Query)) ? $this->Query : $Query;

    $this->UntilOrSince = "";
    if( ! is_null($since) ){
      $this->UntilOrSince .= "&since=$since";
    }
    if( ! is_null($until) ){
      $this->UntilOrSince .= "&until=$until";
    }

    $this->LastFunction = debug_backtrace()[1]['function'];
    // echo $Query.$this->UntilOrSince;
    if( $this->TryToGetResponse($Query.$this->UntilOrSince) ){
      try{
        $this->data = $this->response->getDecodedBody();
      }catch(Exception $e){
        $this->data = array();
        $this->ErrorManager->handleError("Error when Decoding Facebook Query Response.", $e );
      }
    }else{
      //Log Error with $this->message() on the Enviroment.
    }
  	return $this->data;
  }
  protected function TryToGetResponse($Query){
    try {
      $this->response = $this->con->get($Query);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      $this->ErrorManager->handleError("Facebook Query Response Exception.", $e );
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      $this->ErrorManager->handleError("Facebook Query SDK Exception.", $e );
    }
    return $this->status();
  }
  public static function parse($function, $data){
    $parsed = array();
    foreach($data as $row){
      $parsed[] = call_user_func_array( $function, [$row] );
    }
    return $parsed;
  }

  # Pagination methods
  public function PaginateAndCount(){
    $this->Paginate();
    $this->Count();
  }
  public function Paginate(){
    list($this->since,$this->until) = self::Pagination($this->data);
  }
  public static function Pagination($data){
    $since = NULL;$until = NULL;
    if( array_key_exists("paging",$data) ){
      $since = self::getSince( $data["paging"]["previous"] );
      $until = self::getUntil( $data["paging"]["next"] );
      $since = (int) $since;
      $until = (int) $until;
    }
    return [$since, $until];
  }
  public static function getSince($string){
    return self::getSinceOrUntil($string,"since");
  }
  public static function getUntil($string){
    return self::getSinceOrUntil($string,"until");
  }
  public static function getSinceOrUntil($string,$type){
    $exploded = explode( "$type=" ,$string);
    $value = explode( "&" , $exploded[1] );
    return $value[0];
  }

  # Query Size methods
  public function Count(){
    $this->size = self::DataSize($this->data);
  }
  public static function DataSize($data){
    if( array_key_exists("data",$data) ){
      return sizeof( $data["data"] );
    }else{
      return sizeof( $data );
    }
  }
  public function isPageFull(){
    $this->Count();
    return ( $this->size == $this->limit );
  }
  public function isPageEmpty(){
    $this->Count();
    return ( ($this->size == 0) or is_null($this->size) );
  }
  public function isLastPage(){
    $this->Count();
    return ( ($this->size < $this->limit) and ($this->size > 0)  );
  }

  public function next($until=NULL){
    $until = ( is_null($until) ) ? $this->until : $until;
    return call_user_func_array( array( $this, "$this->LastFunction" ), [NULL,$until,NULL] );
  }
  public function previous($since=NULL){
    $since = ( is_null($since) ) ? $this->since : $since;
    return call_user_func_array( array( $this, "$this->LastFunction" ), [NULL,NULL,$since] );
  }

  # API accounts methods
  public function ACCOUNTS($reload=False,$fields=NULL){
    if( $reload or ! $this->hasAccounts() ){
      $fields = (is_null($fields)) ? $this->AccountsFields : $fields;
      $fields = self::commaSeparated($fields);
      $this->accounts = $this->GET("me/accounts?fields=$fields");
      //PaginateAndCount()?
      $this->accounts = $this->accounts["data"];
      $this->accounts = self::parse_accounts($this->accounts);
      if( !$this->status() ){
        $this->ErrorManager->handleError( "Error when getting Accounts." );
      }
    }
    return $this->accounts;
  }
  public function get_account_token($id_or_username,$level=1){
    $search_field = ( is_string($id_or_username) ) ? "page_username" : "page_id";
    $PageToken = NULL;
    $this->ACCOUNTS();
    foreach($this->accounts as $row){
      if( $row[$search_field] == $id_or_username ){
        $PageToken = $row["page_token"];
        break;
      }
    }
    return $PageToken;
  }
  public static function parse_accounts($data){
    $parsed = self::parse("self::parse_accounts_row", $data);
    $parsed = array_sort($parsed, 'page_name', SORT_ASC);
    return $parsed;
  }
  public static function parse_accounts_row($r){
    // $p for "parsed"
    // $r for "raw"
    $p = array();
    $p["page_token"] = $r["access_token"];
    $p["page_perms"] = self::getPagePermsLevel($r);
    $p["page_instagram"] = self::getInstagram($r);

    $p["page_id"] = $r["id"];
    $p["page_name"] = $r["name"];
    $p["page_username"] = self::getPageUsername($r);
    $p["page_picture"] = self::getPagePicture($r);
    $p["page_cover"] = self::getPageCover($r);

    return $p;
  }
  public static function getPagePermsLevel($r){
    return sizeof($r["perms"])-1;
  }
  public static function getInstagram($r){
    if( array_key_exists("instagram_accounts",$r) ){
      return $r["instagram_accounts"]["data"][0]["id"];
    }else{
      return NULL;
    }
  }
  public static function getPagePicture($r){
    return $r["picture"]["data"]["url"];
  }
  public static function getPageCover($r){
    if( array_key_exists("cover",$r) ){
      return $r["cover"]["source"];
    }else{
      return NULL;
    }
  }
  public static function getPageUsername($r){
    if( array_key_exists("username",$r) ){
      return $r["username"];
    }else{
      return NULL;
    }
  }
  public function hasAccounts(){
    return ( !is_null($this->accounts) );
  }

  # Formatting methods
  public static function parseFacebookDatetime($datetime){
    return $datetime;
  }
  public static function commaSeparated($data){
    $commaSeparatedData = "";
    if( is_array($data) ){
      $commaSeparatedData = implode(",",$data);
    }else{
      if( is_string($data) ){
        $commaSeparatedData = $data;
      }
    }
    return $commaSeparatedData;
  }

  # Status and error handling methods
  public function status(){
    return $this->ErrorManager->getStatus();
  }
  public function message(){
    return $this->ErrorManager->getMessage();
  }
}

class FacebookPageManager extends FacebookObject{
  public $limit = 100;
  public $conversation_fields = ["id","link","updated_time","participants",
      "message_count","unread_count","snippet","can_reply","is_subscribed"];

  public function me( $fields=["id","name"] ){
    return parent::me( $fields );
  }

  public function setPage($id_or_username){
    $PageToken = $this->get_account_token($id_or_username);
    $this->setToken($PageToken);
  }

  public function CONVERSATIONS($fields=NULL,$until=NULL,$since=NULL){
    $fields = (is_null($fields)) ? $this->conversation_fields : $fields;
    $fields = self::commaSeparated($fields);
    $this->GET("me/conversations?fields=$fields&limit=$this->limit", $until,$since);
    $this->PaginateAndCount();
    return $this->data;
  }

}

class FacebookPageInsights extends FacebookPageManager{
  public $limit = 100;

  public function Fans($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_fan_adds_by_paid_non_paid_unique&limit=$this->limit", $until,$since);
    $this->data = self::parse_Fans($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_Fans($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_Fans_row", $data);
    return $parsed;
  }
  public static function parse_Fans_row($r){
    // $p for "parsed"
    // $r for "raw"
    $p = array();
    $p["facebookfans_daytime"] = self::parseFacebookDatetime( $r["end_time"] );
    $p["facebookfans_unpaid"] = $r["value"]["unpaid"];
    $p["facebookfans_paid"] = $r["value"]["paid"];
    $p["facebookfans_total"] = $r["value"]["total"];

    return $p;
  }
  public function CurrentFans(){
    $this->GET("me/insights?metric=page_fans");
    $this->data = end($this->data["data"][0]["values"])["value"]; //Parsing
    return $this->data;
  }
  public function FansHistory($since=NULL,$until=NULL){
    $CurrentFans = $this->CurrentFans();
    $Fans = $this->Fans($since,$until);
    $FansSum = self::columns_sum_bykey( $Fans, "facebookfans_total");

    $RollingFans = $CurrentFans - $FansSum;

    $FansHistory = array();
    foreach($Fans as $row){
      $RollingFans += $row["facebookfans_total"];
      $FansHistory[] =
        array(
          "facebookfans_daytime" => $row["facebookfans_daytime"],
          "facebookfans_history" => $RollingFans
        );
    }
    return $FansHistory;
  }

  public function FanSources($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_fans_by_like_source&limit=$this->limit", $until,$since);
    $this->data = self::parse_FanSources($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_FanSources($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_FanSources_row", $data);
    return $parsed;
  }
  public static function parse_FanSources_row($r){
    $p = array();
    $p["facebookfansource_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    $p["facebookfansource_reactivateduser"] = self::ZeroIfNotfound($r,"pagelike_adder_for_reactivated_users");
    $p["facebookfansource_oldinvite"] = self::ZeroIfNotfound($r,"reminder_box_invite");
    $p["facebookfansource_invite"] = self::ZeroIfNotfound($r,"page_browser_invite");
    $p["facebookfansource_mobilebrowser"] = self::ZeroIfNotfound($r,"mobile_page_browser");
    $p["facebookfansource_story"] = self::ZeroIfNotfound($r,"feed_story");
    $p["facebookfansource_profile"] = self::ZeroIfNotfound($r,"page_profile");
    $p["facebookfansource_timeline"] = self::ZeroIfNotfound($r,"page_timeline");
    $p["facebookfansource_promotedpost"] = self::ZeroIfNotfound($r,"sponsored_story");
    $p["facebookfansource_hovercard"] = self::ZeroIfNotfound($r,"hovercard");
    $p["facebookfansource_api"] = self::ZeroIfNotfound($r,"api");
    $p["facebookfansource_search"] = self::ZeroIfNotfound($r,"search");
    $p["facebookfansource_mobile"] = self::ZeroIfNotfound($r,"mobile");
    $p["facebookfansource_mobile_ads"] = self::ZeroIfNotfound($r,"mobile_ads");
    $p["facebookfansource_banhammer"] = self::ZeroIfNotfound($r,"banhammer");
    $p["facebookfansource_ads"] = self::ZeroIfNotfound($r,"ads");
    $p["facebookfansource_friendinvite"] = self::ZeroIfNotfound($r,"page_suggestion");

    $p["facebookfansource_total"] =
        $p["facebookfansource_reactivateduser"] +
        $p["facebookfansource_oldinvite"] +
        $p["facebookfansource_invite"] +
        $p["facebookfansource_mobilebrowser"] +
        $p["facebookfansource_story"] +
        $p["facebookfansource_profile"] +
        $p["facebookfansource_timeline"] +
        $p["facebookfansource_promotedpost"] +
        $p["facebookfansource_hovercard"] +
        $p["facebookfansource_api"] +
        $p["facebookfansource_search"] +
        $p["facebookfansource_mobile"] +
        $p["facebookfansource_mobile_ads"] +
        $p["facebookfansource_banhammer"] +
        $p["facebookfansource_ads"] +
        $p["facebookfansource_friendinvite"] ;
    return $p;
  }

  public static function ZeroIfNotfound($array,$key){
    if( array_key_exists("value",$array) ){
      $array = $array["value"];
      $x = ( array_key_exists($key,$array) ) ? $array[$key] : 0;
      return $x;
    }else {
      return 0;
    }
  }

  public function Consumption($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_consumptions_by_consumption_type&limit=$this->limit", $until,$since);
    $this->data = self::parse_Consumption($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_Consumption($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_Consumption_row", $data);
    return $parsed;
  }
  public static function parse_Consumption_row($r){
    $p = array();
    $p["facebookconsumption_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    $p["facebookconsumption_otherclicks"] = $r["value"]["other clicks"];
    $p["facebookconsumption_videoplay"] = $r["value"]["video play"];
    $p["facebookconsumption_linkclick"] = $r["value"]["link clicks"];
    $p["facebookconsumption_photoview"] = $r["value"]["photo view"];

    $p["facebookconsumption_total"] =
        $p["facebookconsumption_otherclicks"] +
        $p["facebookconsumption_videoplay"] +
        $p["facebookconsumption_linkclick"] +
        $p["facebookconsumption_photoview"] ;

    return $p;
  }

  public function Stories($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_stories_by_story_type&limit=$this->limit", $until,$since);
    $this->data = self::parse_Story($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_Story($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_Story_row", $data);
    return $parsed;
  }
  public static function parse_Story_row($r){
    $p = array();
    $p["facebookstory_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    $p["facebookstory_fan"] = $r["value"]["fan"];
    $p["facebookstory_other"] = $r["value"]["other"];
    $p["facebookstory_pagepost"] = $r["value"]["page post"];
    $p["facebookstory_userpost"] = $r["value"]["user post"];
    $p["facebookstory_checkin"] = $r["value"]["checkin"];
    $p["facebookstory_question"] = $r["value"]["question"];
    $p["facebookstory_coupon"] = $r["value"]["coupon"];
    $p["facebookstory_event"] = $r["value"]["event"];
    $p["facebookstory_mention"] = $r["value"]["mention"];

    $p["facebookstory_total"] =
        $p["facebookstory_fan"] +
        $p["facebookstory_other"] +
        $p["facebookstory_pagepost"] +
        $p["facebookstory_userpost"] +
        $p["facebookstory_checkin"] +
        $p["facebookstory_question"] +
        $p["facebookstory_coupon"] +
        $p["facebookstory_event"] +
        $p["facebookstory_mention"] ;

    return $p;
  }

  public function Impressions($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_impressions_by_paid_non_paid&limit=$this->limit", $until,$since);
    $this->data = self::parse_Impressions($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_Impressions($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_Impressions_row", $data);
    return $parsed;
  }
  public static function parse_Impressions_row($r){
    $p = array();
    $p["facebookimpressions_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    $p["facebookimpressions_paid"] = $r["value"]["paid"];
    $p["facebookimpressions_unpaid"] = $r["value"]["unpaid"];
    $p["facebookimpressions_total"] = $r["value"]["total"];

    return $p;
  }

  public function Reactions($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_actions_post_reactions_total&limit=$this->limit", $until,$since);
    $this->data = self::parse_Reactions($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_Reactions($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_Reactions_row", $data);
    return $parsed;
  }
  public static function parse_Reactions_row($r){
    $p = array();
    $p["facebookreactions_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    $p["facebookreactions_like"] = $r["value"]["like"];
    $p["facebookreactions_love"] = $r["value"]["love"];
    $p["facebookreactions_wow"] = $r["value"]["wow"];
    $p["facebookreactions_haha"] = $r["value"]["haha"];
    $p["facebookreactions_sorry"] = $r["value"]["sorry"];
    $p["facebookreactions_anger"] = $r["value"]["anger"];

    $p["facebookreactions_total"] =
        $p["facebookreactions_like"] +
        $p["facebookreactions_love"] +
        $p["facebookreactions_wow"] +
        $p["facebookreactions_haha"] +
        $p["facebookreactions_sorry"] +
        $p["facebookreactions_anger"] ;

    return $p;
  }

  public function OnlineFans($since=NULL,$until=NULL){
    $this->GET("me/insights?period=day&metric=page_fans_online&limit=$this->limit", $until,$since);
    $this->data = self::parse_OnlineFans($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_OnlineFans($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_OnlineFans_row", $data);
    return $parsed;
  }
  public static function parse_OnlineFans_row($r){
    $p = array();
    $p["facebookonlinefans_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    $p["facebookonlinefans_total"] = 0;
    for ($h = 0; $h <= 23; $h++) {
      $p["facebookonlinefans_$h"] = self::ZeroIfNotfound($r,"$h");
      $p["facebookonlinefans_total"] += $p["facebookonlinefans_$h"];
    }

    return $p;
  }

  public function CurrentFansByCountry($page="me"){
    $data = $this->FansByCountry($page);
    $i = (sizeof($data)-1);
    $this->data = $data[$i];
    return $this->data;
  }
  public function FansByCountry($page="me",$since=NULL,$until=NULL){
    $this->GET("$page/insights?metric=page_fans_country&limit=$this->limit", $until,$since);
    $this->data = self::parse_FansByCountry($this->data);
    $this->PaginateAndCount();
    return $this->data;
  }
  public static function parse_FansByCountry($data){
    $data = $data["data"][0]["values"];
    $parsed = self::parse("FacebookPageInsights::parse_FansByCountry_row", $data);
    return $parsed;
  }
  public static function parse_FansByCountry_row($r){
    $p = array();
    $p["facebookpagefans_daytime"] = self::parseFacebookDatetime( $r["end_time"] );

    if( sizeof($r["value"])==0 ){
      $p["facebookpagefans_country"] = "";
      $p["facebookpagefans_country_total"] = 0;
      $p["facebookpagefans_country_rate"] = 0;
      $p["facebookpagefans_total"] = 0;
    }else{
      $sum = 0;
      $max = -1;
      $topcountry = "";
      foreach( $r["value"] as $country => $fans){
        $sum += $fans;
        if($max<$fans){
          $max = $fans;
          $topcountry = $country;
        }
      }
      $p["facebookpagefans_country"] = $topcountry;
      $p["facebookpagefans_country_total"] = $max;
      $p["facebookpagefans_country_rate"] = round($max/$sum,2);
      $p["facebookpagefans_total"] = $sum;
    }

    return $p;
  }

  public static function columns_sum($table){
    $keys = array_keys($table[0]);
    foreach($keys as $k){
      $keysums[$k] = 0.0;
    }
    foreach($table as $row){
      foreach($keys as $k){
        $keysums[$k] += floatval( $row[$k] );
      }
    }
    return $keysums;
  }
  public static function columns_sum_bykey($table,$key){
    $sum = 0.0;
    foreach($table as $row){
      $sum += floatval( $row[$key] );
    }
    return $sum;
  }
  public static function columns_avg($table){
    $keysums = self::columns_sum($table);
    $N = sizeof($table);

    $keys = array_keys($table[0]);
    foreach($keys as $k){
      $keyaverages[$k] = ( $keysums[$k] / $N );
    }
    return $keyaverages;
  }
  public static function columns_std($table,$keyaverages=NULL){
    $variancekeys = self::columns_var($table,$keyaverages);

    $keys = array_keys($table[0]);
    $stdkeys = array();
    foreach( $keys as $k){
      $stdkeys[$k] = sqrt( $variancekeys[$k] );
    }

    return $stdkeys;
  }
  public static function columns_var($table,$keyaverages=NULL){
    $keyaverages = ( is_null($keyaverages) )? self::columns_avg($table) : $keyaverages;
    $variancetable = self::table_var($table,$keyaverages);
    $variancekeys = self::columns_avg($variancetable);
    return $variancekeys;
  }
  public static function columns_avgstd($table,$keyaverages=NULL){
    $keyaverages = ( is_null($keyaverages) )? self::columns_avg($table) : $keyaverages;
    $stdkeys = self::columns_std($table,$keyaverages);
    return [$keyaverages,$stdkeys];
  }
  // public static function columns_max($table)
  // public static function columns_min($table)
  public static function percentile($z){
    $zvalues = self::zvaluestable();
    $limit = sizeof($zvalues)-1;
    $is_positive = ($z>=0);

    $zindex = abs($z*10);
    $index1 = min( floor($zindex), $limit );
    $index2 = ($index1==$limit) ? $limit : ($index1+1);

    $weight2 = ($zindex-$index1);
    $weight1 = (1-$weight2);

    $percentile = ( $zvalues[$index1] * $weight1 ) + ( $zvalues[$index2] * $weight2 );
    if($is_positive){
      $percentile = (1-$percentile);
    }
    return round($percentile,4);
  }
  public static function zvaluestable(){
    $zvalues[0] = 0.5000;
    $zvalues[1] = 0.4602;
    $zvalues[2] = 0.4207;
    $zvalues[3] = 0.3821;
    $zvalues[4] = 0.3446;
    $zvalues[5] = 0.3085;
    $zvalues[6] = 0.2743;
    $zvalues[7] = 0.2420;
    $zvalues[8] = 0.2119;
    $zvalues[9] = 0.1841;
    $zvalues[10] = 0.1587;
    $zvalues[11] = 0.1357;
    $zvalues[12] = 0.1151;
    $zvalues[13] = 0.0968;
    $zvalues[14] = 0.0808;
    $zvalues[15] = 0.0668;
    $zvalues[16] = 0.0548;
    $zvalues[17] = 0.0446;
    $zvalues[18] = 0.0359;
    $zvalues[19] = 0.0287;
    $zvalues[20] = 0.0228;
    $zvalues[21] = 0.0179;
    $zvalues[22] = 0.0139;
    $zvalues[23] = 0.0107;
    $zvalues[24] = 0.0082;
    $zvalues[25] = 0.0062;
    $zvalues[26] = 0.0047;
    $zvalues[27] = 0.0035;
    $zvalues[28] = 0.0026;
    $zvalues[29] = 0.0019;
    $zvalues[30] = 0.0013;
    $zvalues[31] = 0.0010;
    $zvalues[32] = 0.0007;
    $zvalues[33] = 0.0005;
    $zvalues[34] = 0.0003;
    $zvalues[35] = 0.0002;

    return $zvalues;
  }
  public static function table_var($table,$keyaverages=NULL){
    $keyaverages = ( is_null($keyaverages) )? self::columns_avg($table) : $keyaverages;

    $i = 0;
    $variancetable = array();
    $keys = array_keys($table[0]);

    foreach($table as $row){
      foreach($keys as $k){
        $x = $table[$i][$k];
        $m = $keyaverages[$k];
        $variancetable[$i][$k] = pow( $x - $m , 2 );
      }
      $i++;
    }

    return $variancetable;
  }
  public static function table_normalization($table,$keyaverages=NULL,$stdkeys=NULL){
    if( is_null($stdkeys) ){
      list($avg,$std) = self::columns_avgstd($table,$keyaverages);
    }else{
      $avg = ( is_null($keyaverages) ) ? self::columns_avg($table) : $keyaverages;
      $std = $stdkeys;
    }
    $i = 0;
    $keys = array_keys($table[0]);
    $normalizedtable = array();
    foreach($table as $row){
      foreach($keys as $j){
        $m = $avg[$j];
        $s = $std[$j];
        $x = $table[$i][$j];
        if($s!=0){
          $normalizedtable[$i][$j] = (($x - $m) / $s);
        }else{
          $normalizedtable[$i][$j] = 0;
        }

      }
      $i++;
    }

    return $normalizedtable;
  }
  public static function table_percentiles($table,$normalizedtable=NULL){
    $normalizedtable = (is_null($normalizedtable)) ? self::table_normalization($table) : $normalizedtable;
    $percentiletable = self::table_function($normalizedtable,"FacebookPageInsights::percentile");
    return $percentiletable;
  }
  public static function table_function($table, $function, $args = [] ){
    $return = array();

    $i = 0;
    $keys = array_keys($table[0]);
    foreach($table as $row){
      foreach($keys as $j){

        if( sizeof($args)>0 ){
          $args = array_merge( [ $table[$i][$j] ] , $args );
        }else{
          $args = [ $table[$i][$j] ];
        }

        $return[$i][$j] =  call_user_func_array( $function, $args );
      }
      $i++;
    }
    return $return;
  }
}

class FacebookAds extends FacebookPageManager{
    public $AdAccountsFields = ["account_id","account_status","amount_spent","balance","currency","name","spend_cap","is_notifications_enabled"];
    public function AdAccounts($fields=NULL){
      $fields = ( is_null($fields) ) ? $this->AdAccountsFields : $fields;
      $fields = self::commaSeparated($fields);
      $this->GET("me/adaccounts?fields=$fields&limit=$this->limit");
      $this->data = self::parse("FacebookAds::parse_AdAccounts_row", $this->data["data"]);
      $this->data  = array_sort($this->data, 'fbadsaccount_name', SORT_ASC);
      return $this->data;
    }
    public static function parse_AdAccounts_row($r){
      $p = array();
      $p["fbadsaccount_id"] = $r["account_id"];
      $p["fbadsaccount_name"] = $r["name"];
      $p["fbadsaccount_currency"] = $r["currency"];
      $p["fbadsaccount_spent"] = $r["amount_spent"]/100;
      $p["fbadsaccount_balance"] = $r["balance"]/100;
      $p["fbadsaccount_cap"] = $r["spend_cap"]/100;
      $p["fbadsaccount_status"] = $r["account_status"];
      $p["fbadsaccount_notifications"] = $r["is_notifications_enabled"];

      return $p;
    }
}
