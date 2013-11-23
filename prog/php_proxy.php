<?php
require 'vendor/autoload.php';
include 'php_javaClass.php';
include 'ServletMock.php';
include 'IE.php';
/*
\java\lang\Thread::currentThread()->getContextClassLoader()->addClasspath(
'C:\Users\asus\Documents\NetBeansProjects\WebApplication1\build\web\WEB-INF\classes'
);*/

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$http->on('request', function ($request, $response) {
	$headers = array('Content-Type' => 'text/html');
	$response->writeHead(200, $headers);
	echo $request->getPath(), PHP_EOL;
	$dados = ie_get_contents('http://www.confecsul.com.br'.$request->getPath());
	$response->end($dados);
});

$socket->listen(1337);

echo "server started on http://localhost:1337/ \n";
$loop->run();