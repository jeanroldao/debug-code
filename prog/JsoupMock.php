<?php

//org/jsoup/Jsoup
//eval(<<<'CODE '
namespace org\jsoup;

include './simplehtmldom/simple_html_dom.php';

class Jsoup extends \java\lang\Object {
	
	public static function connect($url) {
		return HttpConnection::connect($url);
	}
}

class HttpConnection extends \java\lang\Object {
	
	private $url;

	public static function connect($url) {
		$connection = new HttpConnection();
		$connection->url($url);
		return $connection;
	}
	
	public function url($url) {
		$this->url = "$url";
		return $this;
	}
	
	public function get() {
		return new \org\jsoup\nodes\Document(file_get_html($this->url));
	}
}

//org/jsoup/nodes/Element
eval(<<<'CODE'

namespace org\jsoup\nodes;

class Document extends \java\lang\Object {
	private $dom;
	
	public function __construct($dom) {
		$this->dom = $dom;
	}
	
	public function select($selector) {
		return new \org\jsoup\select\Elements($this->dom->find("$selector"));
	}
}

//org/jsoup/nodes/Element
class Element extends \java\lang\Object {
	private $domElement;
	
	public function __construct($domElement) {
		$this->domElement = $domElement;
	}
	
	public function select($selector) {
		return new \org\jsoup\select\Elements($this->domElement->find("$selector"));
	}
	
	public function text() {
		return new \java\lang\String($this->domElement->text());
	}
	
	public function val() {
		return new \java\lang\String($this->domElement->value);
	}
}
CODE
) !== false or exit;

//org/jsoup/select/Elements
eval(<<<'CODE'

namespace org\jsoup\select;

class Elements extends \java\util\ArrayList {

	public function __construct($elements) {
		$array = [];
		foreach ($elements as $el) {
			$array[] = new \org\jsoup\nodes\Element($el);
		}
		parent::__construct($array);
	}
	
	public function text() {
		if ($this->get(0) !== null) {
			return $this->get(0)->text();
		} else {
			return new \java\lang\String("");
		}
	}
	
	public function val() {
		if ($this->get(0) !== null) {
			return $this->get(0)->val();
		} else {
			return new \java\lang\String("");
		}
	}
	
	public function select($selector) {
		return $this->get(0)->select($selector);
	}
}
CODE
) !== false or exit;

