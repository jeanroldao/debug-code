<?php
include 'php_javaClass.php';

class Pessoa extends \java\lang\Object {
	private $nome;
	
	public function __construct($nome) {
		$this->nome = $nome;
	}
	
	public function getNome() {
		return $this->nome;
	}
	
	public function toString() {
		return jstring("{nome=".$this->nome."}");
	}
}

$list = new \java\util\ArrayList();

for ($i = 0; $i < 1; $i++) {
	//$list->put(jstring("p$i"), new Pessoa(jstring("p$i")));
	$list->add(new Pessoa(jstring("p$i")));
}

//echo "<?php\n";
//var_dump($list);
echo $list->get(0)->getNome();