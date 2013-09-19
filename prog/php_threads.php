<?php
class Async extends Thread {
	/**
	* Provide a passthrough to call_user_func_array
	**/
	public function __construct($method, $params){
		$this->method = $method;
		$this->params = $params;
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

//$future = Async::call("file_get_contents", array("http://www.php.net"));
function teste1($n) {
	//var_dump($GLOBALS);exit;
	for ($i = 0; $i < 100; $i++) {
		echo "$n-$i" . PHP_EOL;
		usleep(100000);
	}
}
//Async::call("teste1", [1]);
//Async::call("teste1", [2]);

$threads = [];
for ($i = 0; $i < 2; $i++) {
	$t = new Async("teste1", [$i]);
	$t->start();
	//$threads[] = $t;
	$t = null;
}

echo "start...", PHP_EOL;
?>