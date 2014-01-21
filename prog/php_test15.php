<?php
namespace jean;

class Nada extends \Exception {
	private $n;
	
	public function __construct($n) {
		$this->n = $n;
	}
}

$n = new Nada(1);
var_dump($n);
$n = unserialize('O:9:"jean\Nada":0:{}');
var_dump($n);

///*
$reflector = new \ReflectionObject($n);  
$attribute = $reflector->getProperty('n'); 
$attribute->setAccessible(true); 
$attribute->setValue($n, 12); 
//*/
var_dump($n);
