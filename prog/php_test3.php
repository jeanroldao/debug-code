<?php

class A {

	public function falar() {
		echo $this->getMsg();
	}
	
	private function getMsg() {
		return "from A";
	}
}

class B extends A {

	public function getMsg() {
		return "from B";
	}
}

$a = new B;

$a->falar();