<?php

class A {

	public function falar($outro) {
		echo $this->getMsg() . ' + '. $outro->getMsg();
	}
	
	public function getMsg() {
		return "from A";
	}
}

class B extends A {

	public function getMsg() {
		return "from B";
	}
}

$a = new B();

$a->falar(new A());