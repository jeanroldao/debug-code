<?php
$lazyEval_classes = [];
function lazyEval($class, $code) {
	global $lazyEval_classes;
	$lazyEval_classes[$class] = $code;
}

spl_autoload_register(function($class){
	global $lazyEval_classes, $afterClassLoaded_events;
	static $i = 0;
	
	$i++;
	if (isset($lazyEval_classes[$class])) {
		$afterClassLoaded_events[$class] = '';
		eval($lazyEval_classes[$class]);
	}
	$i--;
	if ($i == 0) {
		foreach ($afterClassLoaded_events as $class => $code) {
			eval($code);
			unset($afterClassLoaded_events[$class]);
		}
	}
});

$GLOBALS['afterClassLoaded_events'] = [];
function afterClassLoaded($class, $code) {
	global $afterClassLoaded_events;
	
	if (isset($afterClassLoaded_events[$class])) {
		$afterClassLoaded_events[$class] = $code;
	} else {
		eval($code);
	}
}

lazyEval('ClassA', <<<'CODE'
class ClassA {
	private static $impl;
	public static function fazA() {
		var_dump('ClassA::fazA');
	}
	
	public static function init() {
		self::$impl = new ClassB();
	}
}
afterClassLoaded('ClassA', 'ClassA::init();');
//ClassA::init();
var_dump('ClassA loaded');
CODE
);

lazyEval('ClassB', <<<'CODE'
class ClassB extends ClassA {
	public static function fazB() {
		var_dump('ClassB::fazB');
	}
}
var_dump('ClassB loaded');
CODE
);

ClassB::fazB();
ClassA::fazA();
?>