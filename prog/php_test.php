<?php
namespace jeanroldao\web;

include 'php_javaClass.php';

//use jeanroldao\web\Position;
use jeanroldao\web\Player;
use java\lang\Integer;
use java\lang\String;
use java\lang\Thread;
use java\util\HashMap;


//$clazz = 'java\lang\Class';
//var_dump(new $clazz('java\lang\String'));

//JavaLoader::addJarClasspath(realpath('jsgame.jar'));
Thread::currentThread()->getContextClassLoader()->addClasspath(
'C:\Users\jean_roldao\Documents\NetBeansProjects\JsGame\build\web\WEB-INF\classes'
);

class Falador_php extends \java\lang\Object {
	private $player;
	
	
	public function construct(Player $p) {
		$this->player = $p;
	}
	
	public function falar() {
		return new String($this->player->getNome().': oi!');
	}
	
	public static function _eval($code) {
		return eval($code);
	}
}


$p = new Player();
$p->setNome(new String("JEAN"));
$p->setImgsrc(new String("p.jpg"));
//$p->getPos()->setX(1);
//$p->getPos()->setY(2);

$i1 = new Integer(157);
$i2 = new Integer(98);
//var_dump($i1 + $i2);
$p->getPos()->setX($i1);
$p->getPos()->setY($i2);
//$p->setX(10);
///$p->setY(45);
//var_dump(json_decode($p->toJson()));
//println($p->toJson());
//var_dump($p);
//echo "oi";

//$p->falar();exit;

function newPlayer($nome, $img, $x, $y) {
	$p = new Player();
	
	$p->setNome($nome);
	$p->setImgsrc($img);
	$p->getPos()->setX($x);
	$p->getPos()->setY($y);
	
	return $p;
}

$p = newPlayer('JEAN', 'none.jpg', 15, 20);

$map = new HashMap();
$map->put($p->getNome(), $p);

$p2 = newPlayer('de', 'none.jpg', 15, 20);
$map->put($p2->getNome(), $p2);

//println(Player::mapToJson($map));
//$map = json_decode(Player::mapToJson($map));
//$map = (object)(array) $map;

$ser = serialize($map);
echo strlen($ser) . PHP_EOL;
echo $ser;
file_put_contents('serial.txt', $ser);

//println($map->get("JEAN")->toJson());
