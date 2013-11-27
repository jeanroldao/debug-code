<?php

function Java_Teste23_S_PhpArray_init() {
	$this->_list = [];
	$this->size = 0;
}

function Java_Teste23_S_PhpArray_add($o) {
	$this->_list[] = $o;
	$this->size++;
}

function Java_Teste23_S_PhpArray_get($i) {
	return $this->_list[$i];
}
