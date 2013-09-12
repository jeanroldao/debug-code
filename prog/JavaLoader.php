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
		$this->addJarClasspath('./rt.jar');
	}
	
	public function setDefaultAssertionStatus($debug) {
		$this->debug_log = (bool) $debug;
	}
	
	public function getDefaultAssertionStatus() {
		return $this->debug_log;
	}
	
	public function addClasspath($path) {
		if (!$path || !realpath($path)) {
			throw new \Exception('Path not found');
		}
		$this->classpath[] = realpath($path).'/';
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
		if (class_exists($className, false)) {
			return \java\lang\Clazz::forName($className);
		}
		//debug_print_backtrace();
		
		//var_dump(self::$classpath);exit;
		foreach ($this->classpath as $path) {
			$filenameSrc = ($path.str_replace('\\', '/', $className).'.java');
			if (file_exists($filenameSrc)) {
				//$this->compileJavaFile($filenameSrc);
			}
			
			$filename = ($path.str_replace('\\', '/', $className).'.class');
			if ($this->readClassFile($filename)) {
				if ($this->debug_log) {
					echo "<loading $className>".PHP_EOL;
				}
				if (!class_exists($className)) {
					echo 'class loader error!';
					exit;
				}
				$className::$javaClass->setClassLoader($this);
				//\java\lang\Clazz::setClassLoader($className, $this);
				return \java\lang\Clazz::forName($className);
			}
		}
		return;
		if (file_exists('rt.jar')) {
			/*
			$zip = new ZipArchive; 
			$zip->open('rt.jar', ZIPARCHIVE::CHECKCONS); 
			$file = $zip->getFromName(str_replace('\\', '/', $className.'.class'));
			var_dump(strlen($file));
			*/
			try {
				$filename = 'zip://' . dirname(__FILE__) . '/rt.jar#'.str_replace('\\', '/', str_replace('_S_', '$', $className).'.class');
				if (file_exists($filename)) {
					$fp = fopen($filename, 'rb');
					echo "<loading rt.jar $className>".PHP_EOL;readline();
					$javaClass = new php_javaClass();
					$javaClass->readClass($fp);
					return;
				}
			} catch (\Exception $e) {
				var_dump($className);
				var_dump($e->getMessage());
				echo($e->getTraceAsString());
				//debug_print_backtrace();
			}
			//var_dump(strlen(stream_get_contents($fp)));
			//var_dump(class_exists($className));
			//exit;
		}
	}
}

class_alias('\sun\misc\Launcher_S_AppClassLoader', '\sun\misc\Launcher$AppClassLoader');
