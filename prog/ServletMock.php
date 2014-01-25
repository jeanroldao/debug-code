<?php

//javax.servlet.http.HttpServlet
namespace javax\servlet\http;
class HttpServlet extends \java\lang\Object {
	public function getServletInfo() {
		return jstring('No info');
	}
}

//org.apache.jasper.runtime.HttpJspBase
eval2('org/apache/jasper/runtime/HttpJspBase', <<<'CODE'
namespace org\apache\jasper\runtime;

class HttpJspBase extends \javax\servlet\http\HttpServlet {
	public function doGet($request, $response) {
		$this->_jspService($request, $response);
	}
	
	public /* virtual */ function _jspService($request, $response) {}
}
CODE
);

//javax/servlet/jsp/JspFactory
eval2('javax/servlet/jsp/JspFactory', <<<'CODE'
namespace javax\servlet\jsp;

class JspFactory extends \java\lang\Object {
	private static $defaultFactory;
	private $pageContext;
	
	public static function getDefaultFactory() {
		return self::$defaultFactory ?: self::$defaultFactory = new JspFactory();
	}
	
	public function getPageContext($servlet, $request, $response, $arg1, $arg2, $arg3, $arg4) {
		return $this->pageContext ?: $this->pageContext = new PageContext($response);
	}
	
	public function releasePageContext() {
		$this->pageContext = null;
	}
}
CODE
);

//javax.servlet.jsp.PageContext
eval2('javax/servlet/jsp/PageContext', <<<'CODE'
namespace javax\servlet\jsp;

class PageContext extends \java\lang\Object {
	
	private $servletContext;
	private $response;
	private $out;

	public function __construct($response) {
		$this->response = $response;
	}
	
	public function getServletContext() {
		return $this->servletContext ?: $this->servletContext = new ServletContext();
	}
	
	public function getServletConfig() {
		return null;
	}
	
	public function getSession() {
		return null;
	}
	
	public function getOut() {
		return $this->out ?: $this->out = new JspWriter($this->response->getWriter());
	}
}

CODE
);

//javax.servlet.jsp.ServletContext
eval2('javax/servlet/jsp/ServletContext', <<<'CODE'
namespace javax\servlet\jsp;

class ServletContext extends \java\lang\Object {
	
	public function getAttribute() {
		return null;
	}
}
CODE
);

//javax.servlet.jsp.JspWriter
eval2('javax/servlet/jsp/JspWriter', <<<'CODE'
namespace javax\servlet\jsp;

class JspWriter extends \java\lang\Object {
	
	private $writer;
	
	public function __construct($writer) {
		$this->writer = $writer;
	}
	
	public function write($msg) {
		$this->writer->print($msg);
	}
	
	public function close() {
		$this->writer->close();
	}
}
CODE
);

//org.apache.jasper.runtime.JspSourceDependent
eval2('org/apache/jasper/runtime/JspSourceDependent', <<<'CODE'
namespace org\apache\jasper\runtime;

class JspSourceDependent_interface extends \java\lang\Object {}
interface JspSourceDependent {}

CODE
);

class HttpServletRequest extends \java\lang\Object {
	
	private $contextPath;
	private $request;
	
	public function __construct($reactRequest = null) {
		$contextPath = '{contextPath}';
		if ($reactRequest !== null) {
			$request = $reactRequest->getQuery();
			$contextPath = dirname($reactRequest->getPath());
		} else {
			$request = $_REQUEST;
			$contextPath = $_SERVER['REQUEST_URI'];
		}
		$this->request = $this->convertRequestStyle($request);
		$this->contextPath = jstring($contextPath);
	}
	
	private function convertRequestStyle($request) {
		$request = explode('&', urldecode(http_build_query($request)));
		$javaRequest = [];
		
		foreach ($request as $param) {
			if(!$param) {
				continue;
			}
			$param = explode('=', $param, 2);
			$javaRequest[$param[0]] = $param[1];
		}
		//var_dump($javaRequest);
		return $javaRequest;
	}
	
	public function getParameter($name) {
		$name = "$name";
		if (isset($this->request[$name])) {
			return jstring($this->request[$name]);
		} else {
			return null;
		}
	}
	
	public function getParameterNames() {
		
		$vector = new \java\util\Vector();
		foreach(array_keys($this->request) as $name) {
			//var_dump($name);
			$vector->add(jstring($name));
		}
		return $vector->elements();
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
	
	public function sendResponseHeaders($httpcode, $length) {
		
	}
	
	public function getResponseBody() {
		return new \javax\servlet\jsp\JspWriter($this->getWriter());
	}
	
	public function getWriter() {
		return $this->responseWriter;
	}
}