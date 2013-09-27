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

$applications = [
	'WebApplication1' => [
		'NewServlet' => new jeanroldao\web\NewServlet()
	]
];
$http->on('request', function ($request, $response) use ($applications) {
	$headers = array('Content-Type' => 'text/plain');
	
	$path = explode('/', $request->getPath());
	if (empty($path[1])) {
		$response->writeHead(200, array('Content-Type' => 'text/html'));
		ob_start();
		?>
			<h1>List of applications</h1>
			<ul>
				<?php
				foreach ($applications as $appName => $servlets) {
					?>
					<li><a href="/<?=$appName?>/"><?=$appName?></a> (servlets: <?=count($servlets)?>)</li>
					<?php
				}
				?>
			</ul>
		<?php
		$response->end(ob_get_contents());
		ob_end_clean();
	} else if (isset($applications[$path[1]]) && empty($path[2])) {
		$response->writeHead(200, array('Content-Type' => 'text/html'));
		ob_start();
		?>
			<h1>List of servlets in <?=$path[1]?></h1>
			<ul>
				<?php
				foreach ($applications[$path[1]] as $servletName => $servlet) {
					?>
					<li><a href="/<?=$path[1]?>/<?=$servletName?>"><?=$servletName?></a> (<?=$servlet->getServletInfo()?>)</li>
					<?php
				}
				?>
			</ul>
		<?php
		$response->end(ob_get_contents());
		ob_end_clean();
	} else if (isset($applications[$path[1]]) && isset($applications[$path[1]][$path[2]])) {
		$servlet = $applications[$path[1]][$path[2]];
		
		$response->writeHead(200, array('Content-Type' => 'text/html'));
		
		$javaRequest  = new javax\servlet\http\HttpServletRequest($request);
		$javaResponse = new javax\servlet\http\HttpServletResponse($response);
		
		$servlet->doGet($javaRequest, $javaResponse);
		
		$response->end();
	} else if (file_exists('.'.$request->getPath())) {
		$file = '.'.$request->getPath();
		
		if (substr($file, -3) == 'php') {
			$response->writeHead(200, array('Content-Type' => 'text/html'));
			foreach ($_GET as $iG => $vG) {
				unset($_GET[$iG]);
			}
			$_GET += $request->getQuery();
			ob_start();
			include $file;
			$response->end(ob_get_contents());
			ob_end_clean();
		} else {
			$response->writeHead(200, array('Content-Type' => 'image'));
			var_dump($file);
			$response->end(file_get_contents($file));
		}
	} else {
		$response->writeHead(200, $headers);
		$response->end("Not found: ".$request->getPath()."\n");
	}
});

$socket->listen(1337);

echo "server started on http://localhost:1337/\n";
$loop->run();