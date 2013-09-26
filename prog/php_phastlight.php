<?php
//Assuming this is server/server.php and the composer vendor directory is ../vendor
require_once 'vendor/autoload.php';

$system = new \Phastlight\System();

$console = $system->import("console");
$http = $system->import("http");

$http->createServer(function($req, $res){
  $res->writeHead(200, array('Content-Type' => 'text/plain'));
  $res->end($req->getURL());
})->listen(1337, '127.0.0.1');
$console->log('Server running at http://127.0.0.1:1337/');