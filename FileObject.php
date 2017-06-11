<?php
class FileObject{
  public function __construct($fileOrPath){
    if( is_array($fileOrPath) ){
      $this->LoadFromRequest($fileOrPath);
    }else{
      $this->LoadFromSystem($fileOrPath);
    }
  }
  public function getInstance(){
    $Clone = new self( $this->getPath() );
    return $Clone;
  }
  public function LoadFromRequest($fileArray){
    $this->LoadedFrom = "Request";

    $this->path = $fileArray["tmp_name"];

    $this->name = $fileArray["name"];
    $this->extension = $fileArray["type"];
    $this->size = $fileArray["size"];

    if( $fileArray["error"] == 0 ){
      $this->message = "";
    }else{
      $this->message = $this->CatchUploadError($fileArray["error"]);
    }
    return $this->status();
  }
  public function LoadFromSystem($path=NULL){
    $path = $this->getPathIfNULL($path);
    $this->LoadedFrom = "System";

    $this->path = $path;
    $this->message = "";
    try {
      $this->name = basename($path);
      $this->extension = $this->getExtension();
      $this->size = filesize($path);
    } catch (Exception $e) {
      $this->message = " * Error when loading file from system:" . $e->getMessage();
    }

    return $this->status();
  }
  public function getPathIfNULL($path=NULL){
    return (is_null($path)) ? $this->getPath() : $path;
  }
  protected function CatchUploadError($code){
    switch ($code) {
     case UPLOAD_ERR_INI_SIZE:
         $message = "* The uploaded file exceeds the upload_max_filesize directive in php.ini.";break;
     case UPLOAD_ERR_FORM_SIZE:
         $message = "* The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";break;
     case UPLOAD_ERR_PARTIAL:
         $message = "* The uploaded file was only partially uploaded.";break;
     case UPLOAD_ERR_NO_FILE:
         $message = "* No file was uploaded.";break;
     case UPLOAD_ERR_NO_TMP_DIR:
         $message = "* Missing a temporary folder.";break;
     case UPLOAD_ERR_CANT_WRITE:
         $message = "* Failed to write file to disk.";break;
     case UPLOAD_ERR_EXTENSION:
         $message = "* File upload stopped by extension.";break;
     default:
         $message = "* Unknown upload error.";break;
   }
   return $message;
  }

  public function SaveTo($path="./",$name=NULL,$overWrite=True){
    $name = $this->getNameIfNULL($name);
    $fullpath = ( $path . $name );

    if( $this->CopyTo($path,$name,$overWrite) ){
      return $this->LoadFromSystem( $fullpath );
    }else{
      return False;
    }
  }
  public function MoveTo($path="./",$name=NULL,$overWrite=True){
    $name = $this->getNameIfNULL($name);
    $fullpath = ( $path . $name );

    if( $this->CopyTo($path,$name,$overWrite) ){
      if( $this->DeleteMe() ){
        return $this->LoadFromSystem( $fullpath );
      }else{
        return False;
      }
    }else{
      return False;
    }
  }
  public function CopyTo($path="./",$name=NULL, $overWrite=True){
    $name = $this->getNameIfNULL($name);
    $fullpath = ( $path . $name );

    if( (!$overWrite) and file_exists($fullpath) ){
        $this->message = "* Error: File alredy exists (Overwriting is not enabled).";
        return False;
    }else{
        // move_uploaded_file
        if( copy( $this->getPath(), $fullpath ) ){
          return True;
        }else{
          $this->message = "* Error: Copying file to server failed. Please try again.";
          return False;
        }
    }
  }

  public function DeleteMe(){
    return True;
  }
  public function DestroyMe(){
    return True;
  }

  public function getExtension(){
    if( $this->isRequestFile() ){
      $extension = $this->getExtensionFromName();
    }else{
      $extension = pathinfo( $this->getPath(), PATHINFO_EXTENSION);
    }
    return strtolower($extension);
  }
  protected function getExtensionFromName(){
    $name = $this->name;
    $exploded = explode(".",$name);
    $n = sizeof($exploded);
    return $exploded[$n-1];
  }

  public function isExtension($ExtensionsArray){
    $ExtensionsArray = (is_array($ExtensionsArray)) ? $ExtensionsArray : [$ExtensionsArray];
    foreach($ExtensionsArray as $key => $value){
      $ExtensionsArray[$key] = strtolower($value);
    }
    $extension = $this->getExtension();
    return ( in_array($extension,$ExtensionsArray) );
  }
  public function isImage(){
    $ImageExtensionsArray = ["jpg","jpeg","png","gif"];
    return ( $this->isExtension($ImageExtensionsArray) );
  }
  public function isBiggerThan($bytes){
    return ( $this->getSize() > $bytes );
  }
  public function isBiggerThanMB($mb){
    return ( $this->getSizeInMB() > $mb );
  }
  protected function isRequestFile(){
    return ( $this->LoadedFrom == "Request" );
  }

  public function GetData(){
    $Array["LoadedFrom"] = $this->getLoadedFrom();
    $Array["path"] = $this->getPath();
    $Array["name"] = $this->getName();
    $Array["extension"] = $this->getExtension();
    $Array["size"] = $this->getSizeInMB();
    $Array["status"] = $this->status();
    $Array["message"] = $this->message();

    return $Array;
  }
  public function getSize(){
    return $this->size;
  }
  public function getSizeInMB(){
    return ( $this->size / 1000000 );
  }
  public function getPath(){
    return $this->path;
  }
  public function getName(){
    return $this->name;
  }
  public function getNameAndExtension(){
    return $this->name;
  }
  protected function getNameIfNULL($name=NULL){
    return (is_null($name)) ? $this->getNameAndExtension() : $name;
  }
  protected function getLoadedFrom(){
    return $this->LoadedFrom;
  }

  public function status(){
    return ( $this->message == "" );
  }
  public function message(){
    return $this->message;
  }
}

define("IMAGE_COMPRESSION_QUALITY",75);
define("IMAGE_DEFAULT_FIT_SIZE",1280);

class ImageObject extends FileObject{
  public function __construct($fileOrPath){
    parent::__construct( $fileOrPath );
  }

  public function Resize($resized_width,$resized_height){
    $resized_image = imagecreatetruecolor($resized_width, $resized_height);

    // Then copy original image and Paste into the new (resized) image.
    imagecopyresized($resized_image, $this->CreateMyImage(),
      0,0,0,0,
      $resized_width  , $resized_height,
      $this->getWidth() , $this->getHeight() );

    $this->SaveImage($resized_image);

    return $this->LoadFromSystem();
  }
  public function getImageSize(){
    return getimagesize( $this->getPath() );
  }
  public function getWidth(){
    list($width, $height) = $this->getImageSize();
    return $width;
  }
  public function getHeight(){
    list($width, $height) = $this->getImageSize();
    return $height;
  }
  public function getAspectRatio(){
    list($width, $height) = $this->getImageSize();
    return ( $width / $height );
  }

  public function CreateMyImage(){
    if( $this->isJPEG() ){
      return imagecreatefromjpeg( $this->getPath() );
    }
    if( $this->isPNG() ){
      return imagecreatefrompng( $this->getPath() );
    }
    if( $this->isGIF() ){
      return imagecreatefromgif( $this->getPath() );
    }
  }
  public function SaveImage($image){
    if( $this->isJPEG() ){
      imagejpeg($image, $this->getPath() );
    }
    if( $this->isPNG() ){
      imagepng($image, $this->getPath() );
    }
    if( $this->isGIF() ){
      imagegif($image, $this->getPath() );
    }
    return $this->LoadFromSystem();
  }

  public function Compress($quality=IMAGE_COMPRESSION_QUALITY){
    try {
      if( $this->isJPEG() ){ $this->CompressJPEG($quality); }
      if( $this->isPNG() ){ $this->CompressPNG($quality); }
      if( $this->isGIF() ){ $this->CompressGIF($quality); }
      return $this->LoadFromSystem();
    } catch (Exception $e) {
      $this->message = " * Error when compressing the image:" . $e->getMessage();
      return False;
    }
  }
  protected function CompressJPEG($quality=IMAGE_COMPRESSION_QUALITY){
    $image = imagecreatefromjpeg( $this->getPath() );
    unlink( $this->getPath() );
    imagejpeg($image, $this->getPath() ,$quality);
  }
  protected function CompressPNG($quality=IMAGE_COMPRESSION_QUALITY){
    $quality = (int) floor( max(min($quality,99),10) / 10 );

    $image = imagecreatefrompng( $this->getPath() );
    unlink( $this->getPath() );
    imagepng($image, $this->getPath() , $quality, PNG_ALL_FILTERS );
  }
  protected function CompressGIF($quality=IMAGE_COMPRESSION_QUALITY){
    return NULL;
  }

  public function isJPEG(){
      return ( $this->isExtension( ["jpg","jpeg"] ) );
  }
  public function isPNG(){
      return ( $this->isExtension( "png" ) );
  }
  public function isGIF(){
      return ( $this->isExtension( "gif") );
  }
}
