<?php
class ErrorManager{
	Public $debugging = False;
	Protected $status = True;
	Protected $message = "";
	Public $exitExecution = False;

	public function __construct(){
		$this->matchGlobalDebuggingStatus();
	}

	private function matchGlobalDebuggingStatus(){
		if( isset($GLOBALS["debugging"]) ){
			$this->debugging = $GLOBALS["debugging"];
		}
	}
	public function weAreDebugging(){
		return $this->debugging;
	}
	public function handleError($message, $e=NULL, $exitExecution = NULL ){
		$this->errorMessage = $message;
		$this->status = False;

		if( $this->weAreDebugging() ){
			$this->errorMessage .= (is_null($e)) ? "" : " ".$e->getMessage();
			$this->alertErrorMessage();
		}

		$this->manageExecutionEnd( $exitExecution );
	}

	protected function manageExecutionEnd( $exitExecution ){
		# If $exitExecution is NULL, use current object state for the exit; decision.
		if( is_null($exitExecution) ){
			$exitExecution = $this->exitExecution;
		}
		if( $exitExecution ){
			exit;
		}
		return $exitExecution;
	}

	public function showErrorMessage(){
		echo $this->errorMessage;
	}
	public function alertErrorMessage(){
		echo printf("<script>alert('%s');</script>", $this->errorMessage);
	}
	//public function alertErrorMessage(){}

	public function getStatus(){
		return $this->status;
	}
	public function getMessage(){
		return $this->message;
	}

}
