<?php
require 'vendor/autoload.php';
include 'php_javaClass.php';
include 'ServletMock.php';

\java\lang\Thread::currentThread()->getContextClassLoader()->addClasspath(
'C:\Users\asus\Documents\NetBeansProjects\WebApplication1\build\web\WEB-INF\classes'
);

$i = 0;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

$servlet = new jeanroldao\web\NewServlet();
$http->on('request', function ($request, $response) use (&$i, $servlet) {
	$headers = array('Content-Type' => 'text/plain');
	
	if (substr($request->getPath(), 0, 27) == '/WebApplication1/NewServlet') {
		$response->writeHead(200, array('Content-Type' => 'text/html'));
		
		$javaRequest = new javax\servlet\http\HttpServletRequest($request);
		$javaResponse = new javax\servlet\http\HttpServletResponse($response);
		
		$servlet->doGet($javaRequest, $javaResponse);
		
		//$response->write('test');
		$response->end();
	} else if (substr($request->getPath(), 0, 12) == '/test/') {
		$i++;

		echo $text = "This is request number $i.\n";

		$response->writeHead(200, $headers);
		$response->end($text);
	} else {
		$response->writeHead(404, $headers);
		$response->end("Not found: ".$request->getPath()."\n");
	}
});

$socket->listen(1337);

echo "server started on http://localhost:1337/\n";
$loop->run();