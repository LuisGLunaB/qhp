<?php
class FileObject{
  public function __construct($fileOrPath){
    if( is_array($fileOrPath) ){
      $this->LoadFromRequest($fileOrPath);
    }else{
      $this->LoadFromSystem($fileOrPath);
    }
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
  public function LoadFromSystem($path){
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

  public function SaveTo($path="./",$overWrite=True){
    $fullpath = ( $path . $this->getNameAndExtension() );

    if( $this->isRequestFile() ){
      $success = $this->SaveFromRequestTo($fullpath,$overWrite);
    }else{
      $success = $this->CopyTo($fullpath,$overWrite);
    }

    if( $success ){ $this->LoadFromSystem($fullpath); }
    return $success;
  }
  protected function SaveFromRequestTo($path,$overWrite=True){
    if( (!$overWrite) and file_exists($path) ){
        $this->message = "* Error: File alredy exists (Overwriting was not enabled).";
        return False;
    }else{
        if( move_uploaded_file($this->getPath(), $path) ){
          return True;
        }else{
          $this->message = "* Error: Uploading file from client failed. Please try again.";
          return False;
        }
    }
  }

  public function CopyTo($path="./", $overWrite=True){
    // Duplicate file to destination
  }
  public function MoveTo($path="./",$overWrite=True){
    // Delete Me, Open destination copy
  }
  public function DeleteMe(){
    return NULL;
  }

  public function getExtension(){
    if( $this->isRequestFile() ){
      return $this->getExtensionFromName();
    }else{
      return pathinfo( $this->getPath(), PATHINFO_EXTENSION);
    }
  }
  protected function getExtensionFromName(){
    $name = $this->name;
    list($basename,$extension) = explode(".",$name,2);
    return $extension;
  }

  public function isImage(){
    $extension = $this->getExtension();
    $ImageExtensionsArray =
      ["jpg","jpeg","png","gif",
       "JPG","JPEG","PNG","GIF",
       "Jpg","Jpeg","Png","Gif"];

    return ( in_array($extension,$ImageExtensionsArray) );
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
