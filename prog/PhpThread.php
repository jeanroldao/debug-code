<?php
class PhpThread extends Thread {
	/**
	* Provide a passthrough to call_user_func_array
	**/
	private $method;
	private $params;
	private $result;
	private $joined;
	private $javaThread;
	
	public function __construct($method, $params, $javaThread){
		$this->method = $method;
		$this->params = $params;
		$this->javaThread = $javaThread;
		$this->result = null;
		$this->joined = false;
	}

	/**
	* The smallest thread in the world
	**/
	public function run(){
		if (($this->result=call_user_func_array($this->method, $this->params))) {
			return true;
		} else return false;
	}

	/**
	* Static method to create your threads from functions ...
	**/
	public static function call($method, $params){
		$thread = new Async($method, $params);
		if($thread->start()){
			return $thread;
		} /** else throw Nastyness **/
	}

	/**
	* Do whatever, result stored in $this->result, don't try to join twice
	**/
	public function __toString(){
		if(!$this->joined) {
			$this->joined = true;
			$this->join();
		}

		return $this->result;
	}
}
