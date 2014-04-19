<?php
namespace sun\misc;

evalLazy('java/lang/ClassLoader', <<<'CODE'
namespace java\lang;

class ClassLoader extends Object {

	private static $systemClassLoader;
	
	private $parent;
	
	public function __construct(ClassLoader $parent = null) {
		$this->parent = $parent;
	}
	
	public static function getSystemClassLoader() {
		return self::$systemClassLoader ?: 
			   self::$systemClassLoader = \sun\misc\Launcher::getLauncher()->getClassLoader();	
	}
	
	public final function defineClass($name, $b, $off, $len, $protectionDomain = null) {
		//var_dump($name, $b, $off, $len, $protectionDomain);
		$string = base64_encode(new \java\lang\String($b, $off, $len));
		$fp = fopen("data://text/plain;base64,$string",'r');
		//var_dump(strlen(stream_get_contents($fp)));
		$phpName = \php_javaClass::convertNameJavaToPhp("$name");
		
		if (class_exists($phpName, false)) {
			//var_dump($phpName, 'class_exists', class_exists($phpName, false));exit;
			$javaClass = new \php_javaClass();
			$javaClass->readClassNoEvaluate($fp);
			$javaClass->setClassLoader($this);

			$cls = \java\lang\Clazz::forName($name);
			$cls->getRefClass()
				->getProperty('javaClass')
				->setValue($javaClass);
				
			try {
				$phpName::__callstatic('<clinit>', []);
			} catch (\JavaMethodNotFoundException $e) {}
			return $cls;
		} else {
			$javaClass = new \php_javaClass();
			$javaClass->readClass($fp);
			$javaClass->setClassLoader($this);
			
			return \java\lang\Clazz::forName($name);
		}
	}
	
	public function loadClass($name) {
		if ($this->parent != null) {
			$this->parent->loadClass($name);
		} else {
			return null;
		}
	}
	
	public static function getSystemResource($name) {
		return self::getSystemClassLoader()->getResource($name);
	}
	
	public static function getSystemResourceAsStream($name) {
		return self::getSystemClassLoader()->getResourceAsStream($name);
	}
	
	public function getResource($name) {
		if ($this->parent != null) {
			return $this->parent->getResource($name);
		} else {
			return null;
		}
	}
	
	public function getResourceAsStream($name) {
		$url = $this->getResource($name);
		try {
			return $url != null ? $url->openStream() : null;
		} catch (IOException $e) {
			return null;
		}
	}
	
	public function getParent() {
		return $this->parent;
	}
}
CODE
) !== false or exit;
class Launcher extends \java\lang\Object {

	private static $launcher;
	
	private $loader;
	
	public function __construct() {
		$this->loader = new Launcher_S_AppClassLoader($this);
	}
	
	public static function getLauncher() {
		if (self::$launcher === null) {
			return self::$launcher = new Launcher();
		}
		return self::$launcher;
	}
	
	public function getClassLoader() {
		return $this->loader;
	}
}

class Launcher_S_AppClassLoader extends \java\lang\ClassLoader {
	
	private $this0;
	
	private $classpath = [];
	
	private $debug_log = false;
	
	public function __construct(Launcher $this0) {
		parent::__construct(null);
		$this->this0 = $this0;
		$this->addClasspath(dirname(__FILE__));
		$this->addJarClasspath('./rt.jar');
		$this->addJarClasspath('./resources.jar');
		//$this->addJarClasspath('./smallsql.jar');
		//$this->addJarClasspath('C:/Program Files/Java/jre7/lib/rt.jar');
	}
	
	public function setDefaultAssertionStatus($debug) {
		$this->debug_log = (bool) $debug;
	}
	
	public function getDefaultAssertionStatus() {
		return $this->debug_log;
	}
	
	//java.net.URL
	public function getResource($name) {
		//var_dump("getResource: $name");readline();
		foreach ($this->classpath as $path) {
			$filename = $path.$name;
			//var_dump($filename);
			@$fp = fopen($filename, 'rb');
			if ($fp) {
				fclose($fp);
				if (substr($filename, 0, 6) == 'zip://') {
					$filename = 'jar:file:/'.substr($filename, 6);
				} else {
					$filename = 'file:/'.$filename;
				}
				$url = new \java\net\URL(jstring(str_replace(['\\', '#', ' '], ['/', '!/', ' '], $filename)));
				//var_dump("getResource ok: $name");
				return $url;
			}
		}
		//var_dump($name);
		//var_dump("getResource fail");
		//exit;
		return null;
	}
	
	//InputStream
	public function getResourceAsStream($name) {
		$url = $this->getResource($name);
		try {
			return $url != null ? $url->openStream() : null;
		} catch (IOException $e) {
			return null;
		}
	}
	
	public function getResources($name) {
		
		$vector = new \java\util\Vector();
		$resource = $this->getResource($name);
		if ($resource !== null) {
			$vector->add($resource);
		}
		
		$enum = new \JavaArray(1, 'java.util.Enumeration');
		$enum[0] = $vector->elements();
		
		$compEnum = new CompoundEnumeration($enum);
		return $compEnum;
	}
	
	public function addClasspath($path) {
		if (substr($path, -1) == '*') {
			$path = substr($path, 0, -1);
			foreach (scandir($path) as $file) {
				if (substr($file, -4) == '.jar') {
					$this->addClasspath($path . $file);
				}
			}
			return;
		}
		if (!$path || !realpath($path)) {
			throw new \Exception('Path not found');
		}
		if (is_file($path) && in_array(substr($path, -4), ['.jar', '.zip'])) {
			$this->addJarClassPath(realpath($path));
		} else if (is_file($path) && in_array(substr($path, -4), ['.war', '.ear'])) {
			$this->classpath[] = 'zip://'.realpath($path).'#WEB-INF/classes/';
		} else {
			$this->classpath[] = realpath($path).'/';
		}
	}
	
	public function addJarClasspath($jarPath) {
		if (!$jarPath || !realpath($jarPath)) {
			throw new \Exception("Jar not found ($jarPath)");
		}
		$path = 'zip://'.realpath($jarPath).'#';
		$this->classpath[] = $path;
		//var_dump($path.'META-INF/services/java.sql.Driver');
		@$fp = fopen($path.'META-INF/services/java.sql.Driver', 'rb');
		//var_dump($fp);
		if ($fp) {
			afterClassLoad('java\lang\System', function() use ($fp) {
				$jdbcClass = trim(stream_get_contents($fp));
				$drivers = \java\lang\System::getProperty("jdbc.drivers");
				$newDrivers = jstring("$drivers:$jdbcClass");
				\java\lang\System::setProperty("jdbc.drivers", $newDrivers);
			});
		}
	}
	
	public function compileJavaFile($filename) {
		$filename = realpath($filename);
		//println($filename);
		//println('tentei...');
		`javac $filename`;
	}
	
	public function readClassFile($filename) {
		//if ($this->debug_log) { echo "<read \n$filename\n...>\n"; }
		@$fp = fopen($filename, 'rb');
		if ($fp) {
			$javaClass = new \php_javaClass();
			//if ($this->debug_log) { echo "<read done \n$filename\n...>\n"; }
			$javaClass->readClass($fp);
			//if ($this->debug_log) { echo "<parse done \n$filename\n...>\n"; var_dump(end(get_declared_classes()));}
			//fclose($fp);
			//println($javaClass->getPhpClassName());
			return true;
		} else {
			//if ($this->debug_log) { echo "<read error \n$filename\n...>\n"; }
			return false;
		}
	}

	public function loadClass($className) {
		$className = "$className";
		//var_dump(['autoload', $className, class_exists(\php_javaClass::convertNameJavaToPhp($className), false), interface_exists(\php_javaClass::convertNameJavaToPhp($className), false)]);
		if (class_exists(\php_javaClass::convertNameJavaToPhp($className), false) 
			|| interface_exists(\php_javaClass::convertNameJavaToPhp($className), false)) {
			return \java\lang\Clazz::forName($className);
		}
		
		//gambiarra temporaria, ou nao :P 
		//(serve para registrar e ativar os eventos do autoload, talvez deveria ser movido para dentro do classloader)
		if (!isset($GLOBALS['afterClassLoad_events'][\php_javaClass::convertNameJavaToPhp($className)])) {
			return \java\lang\Clazz::forName($className);
		}
		
		//debug_print_backtrace();
		
		//var_dump(self::$classpath);exit;
		foreach ($this->classpath as $path) {
			/*
			$filenameSrc = ($path.str_replace('\\', '/', $className).'.java');
			if (file_exists($filenameSrc)) {
				//$this->compileJavaFile($filenameSrc);
			}*/
			
			$filename = ($path.str_replace('.', '/', $className).'.class');
			//if ($this->debug_log) { echo "<loading \n$filename\n ...>".PHP_EOL; }
			if ($this->readClassFile($filename)) {
				if ($this->debug_log) { echo "<$className ok>".PHP_EOL; }
				$classNamePhp = \php_javaClass::convertNameJavaToPhp($className);
				if (!class_exists($classNamePhp) && !interface_exists($classNamePhp)) {
					echo 'class loader error! (class not loaded? "'.$classNamePhp.'")'.PHP_EOL;
					exit;
				}
				//var_dump($classNamePhp, $className);
				if (interface_exists($classNamePhp)) {
					$classNamePhp .= '_interface';
				}
				$classNamePhp::$javaClass->setClassLoader($this);
				//\java\lang\Clazz::setClassLoader($className, $this);
				return \java\lang\Clazz::forName($className);
			}
			if ($this->debug_log) { echo "<$className not found>".PHP_EOL; }
		}
		return null;
	}
}

class_alias('sun\misc\Launcher_S_AppClassLoader', 'sun\misc\Launcher$AppClassLoader');
