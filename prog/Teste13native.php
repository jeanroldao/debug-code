<?php

function Java_Teste13_falar($thisObj) {
	var_dump($thisObj->getFrase() . $thisObj->nome);
}

function Java_Teste13_getFrase($thisObj) {
	return "meu nome eh: ";
}