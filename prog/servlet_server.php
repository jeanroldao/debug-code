<?php
require 'vendor/autoload.php';
include 'php_javaClass.php';
//include 'ServletMock.php';

//\java\lang\Thread::currentThread()->getContextClassLoader()->addClasspath(
$cl = \java\lang\ClassLoader::getSystemClassLoader();
$cl->addClasspath('./rt.jar');
$cl->addClasspath('sample.war');
//'C:\Users\asus\Documents\NetBeansProjects\WebApplication1\build\web\WEB-INF\classes'
//'C:\Users\jean_roldao\Documents\NetBeansProjects\JavaPhpApplication\dist\JavaPhpApplication.war'
//'C:\Users\jean_roldao\Documents\NetBeansProjects\JavaPhpApplication\build\generated\classes'

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

$zip = new ZipArchive();
$zip->open('sample.war');
$zip->extractTo("temp/$servlet_name");
$zip->close();
chmod("temp/$servlet_name", 0777);

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
		if (file_exists('./temp/'.$request->getPath())) {
			$app_path = './temp/'.$request->getPath();
			?>
			<div>index <?=$path[1]?></div>
			<?php
			foreach (scandir($app_path) as $name) {
				if (in_array($name, ['.', '..'])) { continue; }
				?>
				<div>
					<a href="/<?=$path[1].'/'.$name?>"><?=$name?></a>
				</div>
				<?php
			}
		} else {
			?>
			<div>List of servlets in <?=$path[1]?></div>
			<?php
			foreach ($applications[$path[1]] as $name => $servlet) {
				?>
				<div>
					<a href="/<?=$path[1].'/'.$name?>/"><?=$name?></a>
				</div>
				<?php
			}
		}
		$response->end(ob_get_contents());
		ob_end_clean();
	} else if (!empty($applications[$path[1]]) && !empty($applications[$path[1]][$path[2]])) {
		$response->writeHead(200, $headers);
		
		$javaRequest = new javax\servlet\http\HttpServletRequest($request);
		$javaResponse = new javax\servlet\http\HttpServletResponse($response);
		
		$servlet = $applications[$path[1]][$path[2]];
		
		$servlet->doGet($javaRequest, $javaResponse);
		
		$response->end();
	} else if (file_exists('.'.$request->getPath()) || file_exists('./temp/'.$request->getPath())) {
		$file = '.'.$request->getPath();
		if (!file_exists($file)) {
			$file = './temp/'.$request->getPath();
		}
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
		} else if (substr($file, -3) == 'jsp') {
			$type = 'text/html';
			$response->writeHead(200, array('Content-Type' => $type));
			$contents = file_get_contents($file);
			/*
			$contents = str_replace(['<%=', '%>'], ['<script>document.write(', ');</script>'], $contents);
			//*/
			$response->end($contents);
		} else {
			//$type = 'image/gif';
			$type = 'text/html';
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