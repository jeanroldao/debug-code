<?php

class Test {
	
	private $nome = 'no name';
	
	public function setNome($nome) { 
		$this->nome = $nome;
	}
	public function getNome() { 
		return $this->nome;
	}
	
	public function __call($method, $args) {
		if (property_exists($this, $method)) {
			return call_user_func_array($this->$method->bindTo($this), $args);
		} else {
			$args = func_get_args();
			print_r($args);
		}
	}
}

$a = new Test();
//$a->nome = 'jean';
$a->falar = function($nome = null) {
	if ($nome !== null) {
		//$this->nome = $nome;
		$this->setNome($nome);
		//print_r($this);
	}
	return $this->getNome() . ': oi';
};
$a->falar('jean');
echo $a->falar();