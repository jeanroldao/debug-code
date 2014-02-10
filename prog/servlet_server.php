<?php
require 'vendor/autoload.php';
include 'php_javaClass.php';
//include 'ServletMock.php';

//\java\lang\Thread::currentThread()->getContextClassLoader()->addClasspath(
\java\lang\ClassLoader::getSystemClassLoader()->addClasspath('./rt.jar');
\java\lang\ClassLoader::getSystemClassLoader()->addClasspath(
//'C:\Users\asus\Documents\NetBeansProjects\WebApplication1\build\web\WEB-INF\classes'
//'C:\Users\jean_roldao\Documents\NetBeansProjects\JavaPhpApplication\dist\JavaPhpApplication.war'
//'C:\Users\jean_roldao\Documents\NetBeansProjects\JavaPhpApplication\build\generated\classes'
'sample.war'
);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);

//$servlet = new jeanroldao\web\NewServlet();

//$fp = fopen('zip://C:\Users\jean_roldao\Documents\NetBeansProjects\JavaPhpApplication\dist\JavaPhpApplication.war#WEB-INF/classes/IndexHelper.class', 'rb');
//var_dump($fp);

//$h = new IndexHelper();
//echo ($h);
//exit;
$applications = [
	'WebApplication1' => [
		'NewServlet' => new org\apache\jsp\index_jsp()
	]
];

$servlets = [];

$fp = fopen('zip://'.realpath('sample.war').'#WEB-INF/web.xml', 'rb');
$xml = simplexml_load_string(stream_get_contents($fp));
fclose($fp);

$servlet_name = trim($xml->servlet->{'servlet-name'});
$servlet_class = trim($xml->servlet->{'servlet-class'});
$servlets[$servlet_name] = \java\lang\Clazz::forName($servlet_class)->newInstance();

//var_dump($servlets);
$servlet_name = trim($xml->{'servlet-mapping'}->{'servlet-name'});
$servlet_url = substr(trim($xml->{'servlet-mapping'}->{'url-pattern'}), 1);
$applications[$servlet_name] = [$servlet_url => $servlets[$servlet_name]];

$http->on('request', function ($request, $response) use ($applications) {
	$headers = array('Content-Type' => 'text/html');
	$path = explode('/', $request->getPath());
	//var_dump($path);
	if (empty($path[1])) {
		$response->writeHead(200, $headers);
		ob_start();
		?>
		<div>List of applications</div>
		<?php
		foreach ($applications as $name => $servlets) {
			?>
			<div><a href="/<?=$name?>/"><?=$name?></a></div>
			<?php
		}
		?>
		<?php
		$response->end(ob_get_contents());
		ob_end_clean();
	} else if (!empty($applications[$path[1]]) && empty($path[2])) {
		$response->writeHead(200, $headers);
		ob_start();
		?>
		<div>List of servlets in <?=$path[1]?></div>
		<?php
		foreach ($applications[$path[1]] as $name => $servlet) {
			?>
			<div>
				<a href="/<?=$path[1].'/'.$name?>/"><?=$name?></a>
				(<?=$servlet->getServletInfo()?>)
			</div>
			<?php
		}
		?>
		<?php
		$response->end(ob_get_contents());
		ob_end_clean();
	} else if (!empty($applications[$path[1]]) && !empty($applications[$path[1]][$path[2]])) {
		$response->writeHead(200, $headers);
		
		$javaRequest = new javax\servlet\http\HttpServletRequest($request);
		$javaResponse = new javax\servlet\http\HttpServletResponse($response);
		
		$servlet = $applications[$path[1]][$path[2]];
		
		$servlet->doGet($javaRequest, $javaResponse);
		
		$response->end();
	} else if (file_exists('.'.$request->getPath())) {
		$file = '.'.$request->getPath();
		if (substr($file, -3) == 'php') {
			$response->writeHead(200, $headers);
			foreach($_GET as $iG => $vG) {
				unset($_GET[$iG]);
			}
			$_GET += $request->getQuery();
			ob_start();
			include $file;
			$response->end(ob_get_contents());
			ob_end_clean();
		} else {
			$type = 'image/gif';
			$response->writeHead(200, array('Content-Type' => $type));
			$response->end(file_get_contents($file));
		}
	} else {
		$response->writeHead(200, $headers);
		$response->end('page not found: '.$request->getPath());
	}
});

$socket->listen(1337);

echo "server started on http://localhost:1337/\n";
$loop->run();