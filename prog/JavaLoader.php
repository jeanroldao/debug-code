<?php
namespace sun\misc;

eval(<<<'CODE'
namespace java\lang;

class ClassLoader extends Object {

	private static $systemClassLoader;
	
	public static function getSystemClassLoader() {
		if (self::$systemClassLoader === null) {
			self::$systemClassLoader = \sun\misc\Launcher::getLauncher()->getClassLoader();
		}
		return self::$systemClassLoader;
	}
}
CODE
) !== false or exit;
class Launcher extends \java\lang\Object {

	private static $launcher;
	
	private $loader;
	
	public function __construct() {
		$this->loader = new Launcher_S_AppClassLoader();
		\java\lang\Thread::currentThread()->setContextClassLoader($this->loader);
	}
	
	public static function getLauncher() {
		if (self::$launcher === null) {
			self::$launcher = new self();
		}
		return self::$launcher;
	}
	
	public function getClassLoader() {
		return $this->loader;
	}
}
class Launcher_S_AppClassLoader extends \java\lang\ClassLoader {
	
	private $classpath = [];
	
	private $debug_log = false;
	
	public function __construct() {
		$this->addClasspath(dirname(__FILE__));
		//$this->addJarClasspath('./rt.jar');
		$this->addJarClasspath('C:/Program Files/Java/jre7/lib/rt.jar');
	}
	
	public function setDefaultAssertionStatus($debug) {
		$this->debug_log = (bool) $debug;
	}
	
	public function getDefaultAssertionStatus() {
		return $this->debug_log;
	}
	
	//java.net.URL
	public function getResource($name) {
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
				return /*new \java\net\URL*/(jstring(str_replace(['\\', '#', ' '], ['/', '!/', '%20'], $filename)));
			}
		}
		return null;
	}
	
	public function addClasspath($path) {
		if (!$path || !realpath($path)) {
			throw new \Exception('Path not found');
		}
		if (is_file($path) && substr($path, -4) == '.jar') {
			$this->addJarClassPath(realpath($path));
		} else {
			$this->classpath[] = realpath($path).'/';
		}
	}
	
	public function addJarClasspath($jarPath) {
		if (!$jarPath || !realpath($jarPath)) {
			throw new \Exception('Jar not found');
		}
		$this->classpath[] = 'zip://'.realpath($jarPath).'#';
	}
	
	public function compileJavaFile($filename) {
		$filename = realpath($filename);
		//println($filename);
		//println('tentei...');
		`javac $filename`;
	}
	
	public function readClassFile($filename) {
		@$fp = fopen($filename, 'rb');
		if ($fp) {
			$javaClass = new \php_javaClass();
			$javaClass->readClass($fp);
			//fclose($fp);
			//echo $javaClass->getPhpClassName();
			return true;
		}
		return false;
	}

	public function loadClass($className) {
		$className = "$className";
		//var_dump(['autoload', $className]);
		if (class_exists(str_replace('.', '\\', str_replace('$', '_S_', $className)), false)) {
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
			//if ($this->debug_log) { echo "<loading $className ...>".PHP_EOL; }
			if ($this->readClassFile($filename)) {
				if ($this->debug_log) { echo "<$className ok>".PHP_EOL; }
				$classNamePhp = str_replace('.', '\\', str_replace('$', '_S_', $className));
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
			//if ($this->debug_log) { echo "<$className not found>".PHP_EOL; }
		}
	}
}

class_alias('sun\misc\Launcher_S_AppClassLoader', 'sun\misc\Launcher$AppClassLoader');
