<?php

//javax.servlet.http.HttpServlet
namespace javax\servlet\http;
class HttpServlet extends \java\lang\Object {}

class HttpServletRequest extends \java\lang\Object {
	
	private $contextPath;
	private $request;
	
	public function __construct($reactRequest = null) {
		$contextPath = '{contextPath}';
		if ($reactRequest !== null) {
			$this->request = $reactRequest->getQuery();
			$contextPath = dirname($reactRequest->getPath());
		} else {
			$this->request = $_REQUEST;
			$contextPath = $_SERVER['REQUEST_URI'];
		}
		$this->contextPath = new \java\lang\String($contextPath);
	}
	
	public function getParameter($name) {
		$name = "$name";
		if (isset($this->request[$name])) {
			return new \java\lang\String($this->request[$name]);
		} else {
			return null;
		}
	}
	public function getContextPath() {
		return $this->contextPath;
	}
}

class HttpServletResponse extends \java\lang\Object {
	
	private $responseWriter;
	
	public function __construct($reactResponse = null) {
		if ($reactResponse !== null) {
			$this->responseWriter = new \java\io\PrintStream($reactResponse);
		} else {
			$this->responseWriter = \java\lang\System::$out;
		}
	}
	
	public function setContentType($contentType) {}
	
	public function getWriter() {
		return $this->responseWriter;
	}
}