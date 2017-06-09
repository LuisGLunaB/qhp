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
    //basename()
    $this->LoadedFrom = "Request";

    $this->name = $fileArray["name"];
    $this->type = $fileArray["type"];
    $this->error = $fileArray["error"];
    $this->size = $fileArray["size"];

    $this->tmp_name = $fileArray["tmp_name"];
    $this->path = $this->tmp_name;

    $this->message = "";
  }
  public function LoadFromSystem($path){
    return NULL;
  }

  public function SaveTo($path="./",$overWrite=True){
    if( $this->isFromRequest() ){
      $success = $this->SaveFromRequestTo($path,$overWrite);
    }else{
      $success = $this->CopyTo($path,$overWrite);
    }

    if( $success ){ $this->LoadFromSystem($path); }
    return $success;
  }
  public function CopyTo($path="./", $overWrite=True){
    // Duplicate file to destination
  }
  public function CutTo($path="./",$overWrite=True){
    // Delete Me, Open destination copy
  }
  public function DeleteMe(){
    return NULL;
  }
  public static function Delete($path="./"){
    return NULL;
  }

  protected function SaveFromRequestTo($path="./",$overWrite=True){
    $name_and_extension = $this->getName();
    echo $name_and_extension;
    $fullpath = $path.$name_and_extension;
    if( (!$overWrite) and self::Exists($fullpath) ){
        $this->message = "* Error: File alredy exists (Overwriting was not enabled).";
        return False;
    }else{
        if( move_uploaded_file($this->getTemporalName(), $fullpath) ){
          return True;
        }else{
          $this->message = "* Error: Uploading file from client failed. Please try again.";
          return False;
        }
    }
  }

  public static function Exists($path){
    return file_exists($path);
  }

  public function getExtension(){
    if( $this->isFromRequest() ){
      return $this->getExtensionFromName();
    }else{
      return pathinfo( $this->getPath(), PATHINFO_EXTENSION);
    }
  }
  public function getExtensionFromName(){
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

  protected function getTemporalName(){
    return $this->tmp_name;
  }
  public function isFromRequest(){
    return ( $this->LoadedFrom == "Request" );
  }

  public function status(){
    return ( $this->message == "" );
  }
  public function message(){
    return $this->message;
  }
}
