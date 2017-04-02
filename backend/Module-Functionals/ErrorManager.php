<?php
class ErrorManager{
	Public $debugging = False;
	Protected $status = True;
	Protected $errorMessage = "";
	Public $exitExecution = False;
	Protected $e = NULL;

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
	public function handleError($errorMessage, $e=NULL, $exitExecution = NULL ){
		$this->errorMessage = utf8_encode($errorMessage);
		$this->e = $e;
		$this->status = False;

		if( $this->weAreDebugging() ){
			$this->errorMessage .= (is_null($e)) ? "" : " ".$e->getMessage();
			self::alertErrorMessage($errorMessage);
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

	public function getErrorObject(){
		return $this->e;
	}

	public static function showErrorMessage($errorMessage){
		echo $errorMessage;
	}
	public static function alertErrorMessage($errorMessage){
		echo printf("<script>alert('%s');</script>", $errorMessage);
	}

	public function getStatus(){
		return $this->status;
	}
	public function getMessage(){
		return $this->errorMessage;
	}

}
