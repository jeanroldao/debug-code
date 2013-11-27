<?php

//Java_PHP_exec
function Java_PHP_exec($func, $args) {
	//var_dump($args[0] . '');
	java\lang\System::$out->println($args[0]);
	//call_user_func_array("$func", $args->toArray());
}