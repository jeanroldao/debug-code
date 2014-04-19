<?php
include 'php_javaClass.php';

if ($_SERVER['argv'][1]) {
	$self = $_SERVER['argv'][0];
	$w = $_SERVER['argv'][1] - 1;
	//var_dump
	(`start /MIN php $self $w`);
	//exit;
}

print_r(php_test18_Menu::getMenu());