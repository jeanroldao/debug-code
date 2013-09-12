<?php
use com\jeanroldao\webminer\WebMiner;

function mb_detect_encoding() {return false;}

include 'php_javaClass.php';
include 'JsoupMock.php';
//var_dump(gettype(file_get_html('http://www.google.com/')));

\java\lang\Thread::currentThread()->getContextClassLoader()->addClassPath(
	'C:\java-web\WebMiner\bin'
);

//$m = new MyClass("Jean");
//$m->falar("Oi");
//C:\java-web\WebMiner\bin>java -classpath .;../lib/jsoup-1.7.2.jar com.jeanroldao.webminer.WebMiner > horarios.js

WebMiner::main([]);