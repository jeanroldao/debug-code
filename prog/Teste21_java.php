<?php
include 'php_javaClass.php';

class Pessoa extends java\lang\Object {
	private $nome;
	
	public function __construct($nome) {
		$this->nome = $nome;
	}
	
	public function getNome() {
		return $this->nome;
	}
}
/*
$p = new \java\util\HashMap();

for ($i = 0; $i < 12345; $i++) {
	//System.out.println(i);
	println($i);
	$p->put(jstring("id_".$i), new Pessoa("(".$i.")"));
}
file_put_contents('Teste21_java_php.ser', serialize($p));
*/

$p = unserialize(file_get_contents('Teste21_java_php.ser'));
println("searching...");
//var_dump($p->values()->getClass().'');
//var_dump($p->entrySet().'');
println("done: " . $p->get(jstring("id_12344"))->getNome());
//readline();
