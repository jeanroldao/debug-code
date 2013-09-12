<pre><?php

function getDocData($docText) {
	return $docText;
}

function ManagedBean($bean) {
	//var_dump($bean);
	return null;
}

/**
 * @ManagedBean(name="pessoaBean")
 */
class PessoaBean {
	
	private $nome;
	
	public function setNome($nome) {
		$this->nome = $nome;
	}
	
	public function getNome() {
		return $this->nome;
	}
}

class Carro {
	
	/**
	 * [bean(name="pessoaBean")]
	 */
	private $dono;
	
	public function setDono($dono) {
		$this->dono = $dono;
	}
	
	public function getDono() {
		return $this->dono;
	}
}

$reflector = new ReflectionClass('Carro');
$properties = $reflector->getProperties();
//var_dump($properties);

foreach ($properties as $property) {
	$doc = getDocData($property->getDocComment());
	var_dump($doc);
}
?>