<?php
class ErrorManager{
	Public $debugging = False;
	Protected $status = True;
	Protected $e = NULL;
	Public $exitExecution = False;

	Protected $errorMessage = ""; //String
	Protected $BackTraceString = ""; //String
	Protected $BackTraceData = ""; //JSON
	Protected $State = ""; //JSON

	// Protected $DateTime = "";
	// Sense User and Catch It

	public function __construct(){
		$this->matchGlobalDebuggingStatus();
	}

	private function matchGlobalDebuggingStatus(){
		if( array_key_exists( "debugging", $GLOBALS) ){
			$this->debugging = $GLOBALS["debugging"];
		}
	}
	public function weAreDebugging(){
		return $this->debugging;
	}
	public function handleError($errorMessage, $e=NULL, $exitExecution = NULL ){
		$this->status = False;
		$this->errorMessage = utf8_encode($errorMessage);
		$this->e = $e;

		$this->errorMessage .= ( is_null($e) ) ? "" : ( " | system: " . $e->getMessage() );
		$this->BackTraceString = self::debug_string_backtrace();
		$this->BackTraceData = json_encode( debug_backtrace() );
		$this->State = json_encode( $_REQUEST );
		// $this->$StateJSON = json_encode( $_REQUEST, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		alert_if_debugging( $errorMessage );
		$this->manageExecutionEnd( $exitExecution );
	}

	protected function manageExecutionEnd( $exitExecution ){
		# If $exitExecution is NULL, use current object state for the exit; decision.
		if( is_null($exitExecution) ){
			$exitExecution = $this->exitExecution;
		}
		if( $exitExecution ){ exit; }

		return $exitExecution;
	}

	protected static function debug_string_backtrace() {
			ob_start();
			debug_print_backtrace();
			$trace = ob_get_contents();
			ob_end_clean();

			// Remove first item from backtrace as it's this function which
			// is redundant.
			$trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

			// Renumber backtrace items.
			$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);

			return $trace;
	}

	public function getErrorObject(){
		return $this->e;
	}

	public static function showErrorMessage($errorMessage){
		echo $errorMessage;
	}
	public static function alertErrorMessage($errorMessage){
		printf("<script>alert('%s');</script>", $errorMessage);
	}

	public function getStatus(){
		return $this->status;
	}
	public function getMessage(){
		return $this->errorMessage;
	}

}

function alert_if_debugging($string){
	if( array_key_exists("debugging", $GLOBALS) ){
		$debugging = $GLOBALS["debugging"];
	}else{
		$debugging = False;
	}
	if($debugging){ alert($string); }
}

function alert($string){
	printf("<script>alert('%s');</script>", $string);
}
