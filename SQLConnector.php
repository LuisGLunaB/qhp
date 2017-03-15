<?php

class SQLConnector{
	Protected $Connection = NULL;

	Protected $DatabaseHost = "";
	Protected $DatabaseName = "";
	Protected $Username = "";
	Protected $Password = "";

	Protected $ErrorManager;

	public function __construct($DatabaseHost=NULL,$DatabaseName=NULL,$Username=NULL,$Password=NULL){
		$this->ErrorManager = new ErrorManager();
		$this->setConnectionVariables($DatabaseHost,$DatabaseName,$Username,$Password);
		if( $this->isConnectionDataComplete() ){
			$this->createNewConnection();
		}
	}
	public function setConnectionVariables($DatabaseHost,$DatabaseName,$Username,$Password){
		$this->DatabaseHost = $DatabaseHost;
		$this->DatabaseName = $DatabaseName;
		$this->Username = $Username;
		$this->Password = $Password;
	}

	public function getConnector(){
		$this->assertOpenConnection();
		return $this->Connection;
	}
	Protected function isConnectionDataComplete(){
		return (
			($this->DatabaseHost !="" ) and
			($this->DatabaseName !="" ) and
			($this->Username !="")
		);
	}

	public function createNewConnection(){
		$this->assertConnectionData();

		$this->createConnectionHandler();
		$this->setPDOErrorMode();

    return $this->getConnector();
	}
	Protected function createConnectionHandler(){
		try{
			$this->Connection['handler'] =
				new PDO(
					"mysql:host=$this->DatabaseHost;
					dbname=$this->DatabaseName;",
					$this->Username,
					$this->Password,
					array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
			);
		}catch(PDOException $e){
			$this->ErrorManager->handleError("Error when creating Connection Handler.", $e );
		}
	}
	Protected function setPDOErrorMode(){
		try {
			if( $this->ErrorManager->weAreDebugging() ){
				$this->Connection['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			}else{
				$this->Connection['handler']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			}
		} catch (PDOException $e){
			$this->ErrorManager->handleError("Error when setting PDO ERRMODE.", $e );
		}
	}

	public function assertConnectionData(){
		try{
			if( !$this->isConnectionDataComplete() ){
				throw new Exception("#Custom exception: Missing database fields.");
			}
		} catch (Exception $e){
			$this->ErrorManager->handleError("Connection data is incomplete.", $e, $exitExecution=False );
		}
	}
	public function assertOpenConnection(){
		try{
			if( is_null($this->Connection) or !$this->status() ){
				throw new Exception("#Custom exception: NULL Connection or False status detected.");
			}
		} catch (Exception $e){
			$this->ErrorManager->handleError("Connection is not open.", $e, $exitExecution=False );
		}
	}

  public function status(){
    return $this->ErrorManager->getStatus();
  }
  public function message(){
    return $this->ErrorManager->getMessage();
  }

}
