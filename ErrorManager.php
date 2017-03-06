<?php
class ErrorManager{
	Public $status = True;
	Public $debugging = False;
	Public $message = "";

	public function __construct(){
		$this->matchGlobalDebuggingStatus();
	}

	public function matchGlobalDebuggingStatus(){
		if( isset($GLOBALS["debugging"]) ){
			$this->debugging = $GLOBALS["debugging"];
		}
	}
	public function weAreDebugging(){
		return $this->debugging;
	}
	public function handleError($message, $e, $exitExecution = True){
		$this->errorMessage = $message;
		$this->status = False;

		if( $this->weAreDebugging() ){
			$this->errorMessage .= " ".$e->getMessage();
			$this->showErrorMessage();
		}

		if($exitExecution){
			exit;
		}
	}
	public function showErrorMessage(){
		echo $this->errorMessage;
	}
}
