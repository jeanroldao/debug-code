<?php
define('JAVA_RT_DIR', __DIR__);

function jstring($str) {
	return (new \java\lang\String($str))->intern();
}

function fixPhpClassName($className) {
	static $replace_ar = [
		'Class' => 'Clazz',
		'Array' => '__Array',
		'List' => '__List',
	];
	
	$className_ns = explode('\\', $className);
	
	foreach ($className_ns as $i => $name) {
		foreach ($replace_ar as $oldName => $newName) {
			if ($name == $oldName) {
				//return $newName;
				$className_ns[$i] = $newName;
			}
		}
	}
	//var_dump($className_ns);readline();
	
	return implode('\\', $className_ns);
	//return str_replace(array_keys($replace_ar), array_values($replace_ar), $className);
}

function fixPhpFuncName($method) {
	static $replace_ar = [
		'<init>' => '__construct',
		'clone' => '_clone',
		'empty' => '__empty',
		'echo' => '__echo',
		'print' => '__print',
		'function' => '__function',
		'exit' => '__exit',
		'list' => '__list',
		'array' => '__array',
		'and' => '__and',
		'or' => '__or',
		'xor' => '__xor',
		'xand' => '__xand',
	];
	foreach ($replace_ar as $oldName => $newName) {
		if ($method == $oldName) {
			return $newName;
		}
	}
	return str_replace('$', '_S_', $method);
	/*
	if ($method == '<init>') {
		return '__construct';
	} else if ($method == 'clone') {
		return '_clone';
	} else if ($method == 'empty') {
		return '__empty';
	} else if ($method == 'echo') {
		return '__echo';
	} else if ($method == 'print') {
		return '__print';
	} else if ($method == 'exit') {
		return '__exit';
	} else if ($method == 'list') {
		return '__list';
	} else {
		return $method;
	}*/
}


function eval2($thisObj, $php) {
	static $dir = null;
	if ($dir === null) {
		$dir = __DIR__ . '/temp/';
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		foreach (scandir($dir) as $file) {
			if (!in_array($file, ['.', '..'])) {
				unlink($dir.$file);
			}
		}
	}
	
	$className = is_object($thisObj) ? $thisObj->getPhpClassName() : $thisObj;
	$file = $dir.str_replace(['\\', '/'], ['_', '_'], $className).'.php';
	file_put_contents($file, "<?php $php");
	include($file);
}

$GLOBALS['evalLazy_classes'] = [];
function evalLazy($className, $code) {
	//var_dump($className);
	if (is_array($className)) {
		foreach ($className as $class) {
			$GLOBALS['evalLazy_classes'][$class] = $code;
		}
	} else {
		$GLOBALS['evalLazy_classes'][$className] = $code;
	}
}

function javaClassDefinition($className) {
	static $cache = [];
	$path = 'zip://'.realpath('./rt.jar').'#';
	$className = str_replace('.', '/', "$className");
	if (isset($cache[$className])) {
		return $cache[$className];
	}
	
	$javaClass = new \php_javaClass();
	$fp = fopen("$path$className.class", 'rb');
	if (!$fp) { 
		exit;
	}
	$javaClass->readClassNoEvaluate($fp);
	//var_dump($javaClass);
	return $cache[$className] = $javaClass;
	//exit;
}

//java/lang/Object
evalLazy('java/lang/Object', <<<'CODE'

namespace java\lang;
	
trait ObjectTrait {
	public function __construct() {}
	
	public function toString() {
		$str = new String($this->getClass()->getName() . '@'.\dechex(System::identityHashCode($this)));
		return $str;
	}
	
	public function hashCode() {
		return System::identityHashCode($this);
	}
	
	public function getClass() {
		return Clazz::forName(str_replace('\\', '.', str_replace('_S_', '$', get_class($this))));
	}
	
	public function __call($method, $args) {
		$fname = fixPhpFuncName($method);
		//var_dump(method_exists($this, $fname));
		if (method_exists($this, $fname)) {
			return call_user_func_array([$this, $fname], $args);
		} else {
			println(get_called_class() . '::' . $method.' method does not exists');
			exit;
			//throw new \Exception(get_called_class() . '::' . $method.' method does not exists');
		}
	}

	public static function __callstatic($method, $args) {
		$fname = fixPhpFuncName($method);
		if (method_exists(get_called_class(), $fname)) {
			return call_user_func_array([get_called_class(), $fname], $args);
		} else {
			println($method.' static method for '.get_called_class().' do not exists');
			exit;
			//throw new \Exception($method.' static method for '.get_called_class().' do not exists');
		}
	}
	
	public function __toString() {
		return $this->toString().'';
	}
	
	public function _clone() {
		$c = clone $this;
		return $c;
	}
	
	public function equals($o) {
		return $this == $o;
	}
	
	public function wait() {
		//var_dump('wait'); readline();
	}
	
}
class Object {use ObjectTrait;}

CODE
) !== false or exit;

//java/lang/Class
evalLazy(['java/lang/Clazz', 'java/lang/Class'], <<<'CODE'

namespace java\lang;
	
class Clazz extends Object {
	
	private $name;
	private $refClass;
	
	private $annotations;
	private $declaredAnnotations;
	
	private static $loadedClasses = [];
	
	public function __construct($name) {
		$name = "$name";
		if (substr($name, 0, 1) == 'L') {
			$name = str_replace('/', '.', substr($name, 1, -1));
		}
		$this->name = $name;
		
		//return;
		
		///*
		//ob_end_flush();
		//var_dump("$name", isset(self::$loadedClasses["$name"]), class_exists("$name", false));readline();
		
		//teste para tipos primitivos?
		if (!$this->isPrimitive() && !isset(self::$loadedClasses["$name"])) {
			self::$loadedClasses["$name"] = true;
			$this->getRefClass();
		}
		//*/
	}
	
	private function getRefClass() {
		//Thread::currentThread()->getContextClassLoader()->loadClass($this->name);
		//var_dump($this->name);
		//var_dump($this->refClass);
		try {
			if ($this->refClass === null) {
				$name = \php_javaClass::convertNameJavaToPhp($this->name);
				if ($this->isArray()) {
					$this->refClass = new \ReflectionClass('JavaArray');
				} else if ($this->isInterface()) {
					$this->refClass = new \ReflectionClass("{$name}_interface");
				} else {
					$this->refClass = new \ReflectionClass($name);
					/*
					try {
					} catch (Exception $er) {
						//println($this->name);
						//var_dump(file_exists($this->name.'.class'));
						//exit;
					}*/
				}
			}
			return $this->refClass;
		} catch (\ReflectionException $e) {
			throw new ClassNotFoundException($this->name);
		}
	}
	
	public function getName() {
		//var_dump($this->name);
		return jstring(str_replace(['\\', '/', '_S_'], ['.', '.', '$'], $this->name));
	}
	
	public static function forName($name) {
		$cls = new Clazz($name);
		return $cls->intern();
	}
	
	public function newInstance() {
		return $this->getRefClass()->newInstance();
	}
	
	private static $interned_classes = [];
	public function intern() {
		if (isset(self::$interned_classes[$this->name])) {
			return self::$interned_classes[$this->name];
		} else {
			return self::$interned_classes[$this->name] = $this;
		}
	}
	
	public function desiredAssertionStatus() {
		return false;
	}
	
	private $enumConstantDirectory = null;
	public function enumConstantDirectory() {
	
		if ($this->enumConstantDirectory === null) {
		
			if (!is_subclass_of($this->name, '\java\lang\Enum')) {
				throw new IllegalArgumentException($this->name . " is not an enum type");
			}
			
			$map = new \java\util\HashMap();
			$name = $this->name;
			foreach ($name::$_S_VALUES as $constant) {
				$map->put($constant->name(), $constant);
			}
			$this->enumConstantDirectory = $map;
		}
		return $this->enumConstantDirectory;
	}
	
	private static $primitive_types = [
		'Z' => 'boolean',
		'C' => 'char',
		'F' => 'float',
		'D' => 'double',
		'B' => 'byte',
		'S' => 'short',
		'I' => 'int',
		'J' => 'long',
		'V' => 'void'
	];
	
	private static function isPrimitiveType($type) {
		return isset(self::$primitive_types["$type"]);
	}
	
	public static function getPrimitiveClass($type) {
		
		$type = "$type";
		
		if (strlen($type) == 1 && self::isPrimitiveType($type)) {
			$type = self::$primitive_types[$type];
		}
		
		return Clazz::forName($type);
	}
	
	public function toString() {
		//$d = debug_backtrace()[1];
		//var_dump([$d['file'], $d['line']]);exit;
		//return new String("class " . $this->getName());
		$toString = 'class ';
		if ($this->isInterface()) {
			$toString = 'interface ';
		} else if ($this->isPrimitive()) {
			$toString = '';
		}
		return jstring($toString)->concat($this->getName());
	}
	
	public function isArray() {
		return $this->getName()->charAt(0) === ord('[');
	}
	
	public function isInterface() {
		return interface_exists(\php_javaClass::convertNameJavaToPhp($this->name));
	}
	
	public function isPrimitive() {
		return in_array($this->name, self::$primitive_types);
	}
	
	public function getModifiers() {
		$javaClass = $this->getRefClass()
						  ->getProperty('javaClass')
						  ->getValue();
		return $javaClass->class_attr['flags_num'];
	}
	
	public function getResource($name) {
		if ($this->getClassLoader() !== null) {
			return $this->getClassLoader()->getResource($name);
		} else {
			return null;
		}
	}
	
	public function getClassLoader() {
		try {
			
			//emulates native libs behavior
			$name = $this->getName();
			if ($name->startsWith(jstring('java'))
			 || $name->startsWith(jstring('sun'))) {
				return null;
			}
			//println($name);
			$cl = $this->getRefClass()
						->getProperty('javaClass')
						->getValue()
						//->getPhpClassName()
						->getClassLoader()
						;
			//var_dump($cl);
			return $cl;
		} catch(\ReflectionException $e) {
			//println($e->getMessage());exit;
			return null;
		}
	}
	
	private $declaredMethods;
	public function getDeclaredMethods() {
		if ($this->declaredMethods !== null) {
			return $this->declaredMethods;
		}
		//var_dump($this->getRefClass()->getMethods());
		try {
			$javaClass = $this->getRefClass()
							  ->getProperty('javaClass')
							  ->getValue();
		} catch (\ReflectionException $e) {
			//var_dump($this->getRefClass()->getMethods()[0].'');
			$javaClass = javaClassDefinition($this->getName());
		}
		
		$methods = $javaClass->class_attr['methods'];
		$javaMethods = [];
		foreach ($methods as $i => $method) {
			if (in_array($method['name'], ['<init>', '<clinit>'])) {
				// no reference for constructor
				continue;
			}
			$argsType = $javaClass->getArgsType($method['type']);
			$returnType = strlen($argsType['return']) == 1 ? self::getPrimitiveClass($argsType['return']): self::forName($argsType['return']);
			
			$parameterTypes = new \JavaArray(count($argsType['args']), 'java.lang.Class');
			foreach ($argsType['args'] as $iArg => $argType) {
				$parameterTypes[$iArg] = strlen($argType) == 1 ? self::getPrimitiveClass($argType): self::forName($argType);
			}
			$javaMethod = new \java\lang\reflect\Method(
				/*Class declaringClass*/        $this,
				/*String name*/                 jstring($method['name']),
				/*Class[] parameterTypes*/      $parameterTypes,
				/*Class returnType*/            $returnType,
				/*Class[] checkedExceptions*/   new \JavaArray(0, 'java.lang.reflect.Class'),
				/*int modifiers*/               $method['flags_num'],
				/*int slot*/                    0,
				/*String signature*/            jstring(''),
				/*byte[] annotations*/          new \JavaArray(0, 'B'),
				/*byte[] parameterAnnotations*/ new \JavaArray(0, 'B'),
				/*byte[] annotationDefault*/    new \JavaArray(0, 'B')
			);
			//var_dump($method);
			//var_dump($javaMethod->toString().'');
			//exit;
			$javaMethod->override = true;
			$javaMethod->_java_type = $method['type'];
			$javaMethods[] = $javaMethod;
		}
		
		return $this->declaredMethods = \JavaArray::fromArray($javaMethods, 'java.lang.reflect.Method');
	}
	
	public function getMethods() {
		$allMethods = [];
		$cls = $this;
		while ($cls !== null) {
			foreach ($cls->getDeclaredMethods() as $method) {
				if (strpos("$method", 'public ') !== false) {
					$allMethods[] = $method;
				}
			}
			$cls = $cls->getSuperClass();
		}
		return \JavaArray::fromArray($allMethods, 'java.lang.reflect.Method');
	}
	
	public function getMethod($name, $types) {
		//var_dump("$this", "$name", self::argumentTypesToString($types));
		
		$cls = $this;
		while ($cls !== null) {
			try {
				$method = $cls->getDeclaredMethod($name, $types);
				return $method;
			} catch (NoSuchMethodException $e) {
				//var_dump("$cls has no $name");
				$cls = $cls->getSuperClass();
			} catch (\Exception $e) {
				println($e);
				exit;
			}
		}
		println(jstring($this->getName().'.'.$name.self::argumentTypesToString($types)));
		exit;
		//throw new NoSuchMethodException(jstring($this->getName().'.'.$name.self::argumentTypesToString($types)));
	}
	
	public function getDeclaredMethod($name, $types) {
		$allMethods = $this->getDeclaredMethods();
		
		//println($name);
		//var_dump(count($allMethods));
		//exit;
		foreach ($allMethods as $method) {
			//print($method->getName());
			//println(self::argumentTypesToString($method->getParameterTypes()));
			if ($method->getName()->equals($name) 
				&& self::argumentTypesToString($method->getParameterTypes())->equals(self::argumentTypesToString($types))
				) {
				//var_dump(''.self::argumentTypesToString($types));
				//var_dump(''.self::argumentTypesToString($method->getParameterTypes()));
				//var_dump(self::argumentTypesToString($method->getParameterTypes())->equals(self::argumentTypesToString($types)));
				//var_dump('getDeclaredMethod');
				//exit;
				return $method;
			}
		}
		throw new NoSuchMethodException(jstring($this->getName().'.'.$name.self::argumentTypesToString($types)));
		//var_dump($this->getName());
		//var_dump("$name", "$types");
		//println(self::argumentTypesToString($types));
		//var_dump('not found');
		//exit;
	}
	
	private static function argumentTypesToString($argTypes) {
		$buf = '(';
        if ($argTypes != null) {
            for ($i = 0; $i < count($argTypes); $i++) {
                if ($i > 0) {
                    $buf .= ", ";
                }
                $c = $argTypes[$i];
                $buf .= ($c == null) ? "null" : $c->getName();
            }
        }
        $buf .= ")";
        return jstring($buf);
    }	
	public function getField($fieldName) {
		var_dump($fieldName);
		var_dump('getDeclaredField');
		exit;
	}
	
	public function getFields() {
		return $this->getAllFields();
	}
	
	private function getAllFields() {
		
		//var_dump($this->getRefClass());
		$javaClass = $this->getRefClass()
						  ->getProperty('javaClass')
						  ->getValue();
		$fields = $javaClass->class_attr['fields'];
		//var_dump($fields);
		foreach ($fields as $i => $field) {
			$type = strlen($field['type']) == 1 ? self::getPrimitiveClass($field['type']): self::forName($field['type']);
			$javaField = new \java\lang\reflect\Field(
				/*Class declaringClass*/ $this,
				/*String name*/          $field['name'],
				/*Class type*/           $type,
				/*int modifiers*/        $field['flags_num'],
				/*int slot*/             0,
				/*String signature*/     jstring(''),
				/*byte[] annotations*/   new \JavaArray(0, 'B')
			);
			$fields[$i] = $javaField;
		}
		return \JavaArray::fromArray($fields, 'java.lang.reflect.Field');
	}
	
	public function getDeclaredField($fieldName) {
		//var_dump(['looking', $fieldName]);
		$fields = $this->getAllFields();
		foreach ($fields as $f) {
			if ($f->getName()->equals($fieldName)) {
				//var_dump(['found', $fieldName]);
				return $f;
			}
		}
		throw new \java\lang\NoSuchFieldException($fieldName);
	}
	public function getDeclaredFields() {
		var_dump('getDeclaredFields');
		exit;
	}
	
	public function getEnclosingClass() {
		$enclosingClasses = explode('$', $this->name);
		array_pop($enclosingClasses);
		$enclosingClass = implode('$', $enclosingClasses);
		
		if ($enclosingClass) {
			return self::forName($enclosingClass);
		} else {
			return null;
		}
	}
	
	public function getClasses() {
		$members = new \java\util\ArrayList();
		$current = $this;
		while ($current != null) {
			foreach ($current->getDeclaredClasses() as $cls) {
				$members->add($cls);
			}
			$current = $current->getSuperclass();
		}
		
		$returnList = new \JavaArray($members->size(), 'java.lang.Class');
		return $members->toArray($returnList);
	}
	
	public function getDeclaredClasses() {
		//DESAFIO ACEITO!!!
		//$classLoader = $this->getClassLoader();
		//var_dump($classLoader);exit;
		//return new \JavaArray(0, 'java.lang.Class');
		$classes = [];
		/*
		foreach(get_declared_classes() as $cls) {
			$enclosingClass = $this->name.'_S_';
			if (substr($cls, 0, strlen($enclosingClass)) == $enclosingClass) {
				$classes[] = $cls;
			}
		}*/
		if (property_exists($this->name, 'javaClass')) {
			$class_name = $this->name;
			if (isset($class_name::$javaClass->class_attr['attr']['InnerClasses'])) {
				foreach ($class_name::$javaClass->class_attr['attr']['InnerClasses'] as $innerClass) {
					//var_dump($innerClass);exit;
					if ($innerClass['outer_class_name'] == $this->name) {
						$classes[] = self::forName($innerClass['inner_class_name']);
					}
				}
			}
		}
		$classes = \JavaArray::fromArray($classes, 'java.lang.Class');
		return $classes;
	}
	
	public function getSuperclass() {
		$superClass = get_parent_class($this->name);
		if ($superClass) {
			return self::forName($superClass);
		} else {
			return null;
		}
	}
	
	public function getComponentType() {
		if (substr($this->name, 0, 1) != '[') {
			return null;
		}
		$name = substr($this->name, 1);
		//var_dump(self::isPrimitiveType($name));
		if (substr($name, 0, 1) == 'L') {
			$name = substr($name, 1, -1);
		} else if (self::isPrimitiveType($name)) {
			return self::getPrimitiveClass($name);
		}
		return self::forName($name);
	}
	
	public function isAssignableFrom($cls) {
		$this_name = \php_javaClass::convertNameJavaToPhp($cls->getName());
		$other_name = \php_javaClass::convertNameJavaToPhp($this->getName());
		
		return is_a($this_name, $other_name, true);
	}
	
	public function getAnnotation($annotationClass) {
		/*
		if ($annotationClass === null) {
			throw new NullPointerException();
		}
		$this->initAnnotationsIfNecessary();
		return $this->annotations->get($annotationClass);
		*/
		return null;
	}
	
	private function initAnnotationsIfNecessary() {}
	
}
class_alias('java\lang\Clazz', 'java\lang\Class');
CODE
) !== false or exit;

//java/lang/System
evalLazy('java/lang/System', <<<'CODE'

namespace java\lang;
	
class System extends Object {
	public static $in;
	public static $out;
	public static $err;
	
	private static $props;
	
	public static function currentTimeMillis() {
		return intval(microtime(true) * 1000);
	}
	
	public static function identityHashCode($obj) {
		//var_dump($obj);
		//return spl_object_hash($obj);
		ob_start(); var_dump($obj); $dump = ob_get_contents(); ob_end_clean();
		if ($r = preg_match('/^object\(([a-z0-9_\\\\]+)\)\#(\d+)/i', $dump, $match)) {
			//var_dump($dump, $match);
			$hash = \md5($match[1] . $match[2]);
			return \base_convert(\substr($hash, 0, 6), 16, 10);
		}	
		return 0;
	}
	
	public static function arraycopy($src, $srcPos, $dest, $destPos, $length) {
		try {
			for ($i = 0; $i < $length; $i++) {
				$dest[$destPos + $i] = $src[$srcPos + $i];
			}
		} catch (\RuntimeException $e) {
			throw new ArrayIndexOutOfBoundsException($i);
		}
	}
	
	public static function gc() {
		Runtime::getRuntime()->gc();
	}
	
	public static function getSecurityManager() {
		return null;
	}
	
	public static function loadLibrary($lib) {
		
		//built-in libs
		if (in_array("$lib", ['net', 'nio', 'awt'])) {
			return;
		}
		
		$file = "$lib.php";
		if (file_exists($file)) {
			require_once $file;
		} else {
			var_dump("no $file in java.library.path");
			//throw new UnsatisfiedLinkError(jstring("no $file in java.library.path"));
		}
	}
	
	public static function getProperties() {
		if (self::$props === null) {
			self::$props = new \java\util\Properties();
			self::initProperties(self::$props);
		}
		return self::$props;
	}
	
	public static function getProperty($propName, $default = null) {
		$prop = self::getProperties()->get($propName);
		if ($prop !== null) {
			return $prop;
		} else {
			return $default;
		}
	}
	
	public static function setProperties(\java\util\Properties $props) {
		self::$props = $props;
	}
	
	public static function setProperty($propName, $value) {
		return self::getProperties()->setProperty($propName, $value);
	}
	
	private static function initProperties($props) {
		try {
			$ini_array = file("SystemProperties.ini");
			if (is_array($ini_array)) {
				foreach ($ini_array as $line) {
					$values = explode('=', trim($line), 2);
					if (count($values) < 2) {
						continue;
					}
					list($k, $v) = explode('=', trim($line), 2);
					//var_dump($v);
					$props->put(jstring($k), jstring(stripslashes($v)));
				}
			}
			$env_data = [
				'sun.boot.library.path' => JAVA_RT_DIR,
				'java.home' 			=> JAVA_RT_DIR,
				'line.separator'        => PHP_EOL,
				'user.dir' 				=> JAVA_RT_DIR,
				'user.home' 			=> getenv("HOME") ?: getenv("HOMEDRIVE").getenv("HOMEPATH"),
				'java.io.tmpdir' 		=> realpath(JAVA_RT_DIR.'/temp'),
				'sun.nio.ch.disableSystemWideOverlappingFileLockCheck' => 'true',
			];
			foreach ($env_data as $k => $v) {
				$props->put(jstring($k), jstring($v));
			}
		} catch (\Exception $e) {
			var_dump('init prop fail');
			println($e);
			exit;
		}
	}
	
	public static function __exit($code = 0) {
		exit($code);
	}
}

afterClassLoad('java\lang\System', function(){
	System::$out = new \java\io\PrintStream();
	System::$err = new \java\io\PrintStream();
	System::$in = new \java\io\BufferedInputStream(new \java\io\FileInputStream(STDIN));
	
	\sun\misc\SharedSecrets::setJavaLangAccess(new System_S_1());

	//\java\sql\DriverManager::setLogStream(System::$out);
});


CODE
) !== false or exit;

//java/lang/System$1
evalLazy(['java/lang/System$1', 'java/lang/System_S_1'], <<<'CODE'

namespace java\lang;

class System_S_1 extends Object implements \sun\misc\JavaLangAccess {

	public function getConstantPool($klass) {
		return $klass->getConstantPool();
	}
	public function setAnnotationType($klass, $annotationType) {
		$klass->setAnnotationType($annotationType);
	}
	public function getAnnotationType($klass) {
		return $klass->getAnnotationType();
	}
	public function getEnumConstantsShared($klass) {
		return $klass->getEnumConstantsShared();
	}
	public function blockedOn($thread, $interruptible) {
		$thread->blockedOn($interruptible);
	}
}

class_alias('java\lang\System_S_1', 'java\lang\System$1');
CODE
) !== false or exit;

//java/io/PrintStream
evalLazy('java/io/PrintStream', <<<'CODE'

namespace java\io;
	
class PrintStream extends \java\lang\Object {
	
	private $writer;
	
	public function __construct($writer = null) {
		if ($writer !== null) {
			$this->writer = $writer;
		}
	}
	
	public function __call($func, $args) {
		//var_dump(func_get_args());//readline();
		/*
		$func_args = func_get_args();
		if(count($func_args) > 2) {
			var_dump($func_args[2][2]);
			exit;
		}
		//*/
		
		if ($func == 'print') {
			//$this->__call('__print', $args);
			call_user_func_array([$this, '__print'], $args);
		} else {
			parent::__call($func, $args);
		}
	}
	
	public function __print($args) {
		$writer = $this->writer;
		$args = func_get_args();
		foreach ($args as $arg) {
			if ($arg === null) {
				$arg = 'null';
			}
			if ($writer !== null) {
				$writer->write((string)new \java\lang\String($arg));
			} else {
				echo new \java\lang\String($arg);
			}
		}
	}
	
	public function println($s = '') {
		//var_dump($s);
		$this->print($s, PHP_EOL);
	}
	
	public function close() {}
}

CODE
) !== false or exit;

//java/util/Scanner
evalLazy('java/util/Scanner', <<<'CODE'

namespace java\util;
	
class Scanner extends \java\lang\Object {
	
	private $stream;
	
	private $line;
	
	public function __construct($stream) {
		if ($stream === null) {throw new \Exception('null stream');}
		//var_dump($stream);exit;
		$this->stream = $stream;
	}
	
	public function nextLine() {
		if (!$this->hasNextLine()) {
			var_dump('Scanner->nextLine');
			var_dump('no more lines!');
			exit;
		}
		$line = $this->line;
		$this->line = null;
		return $line;
	}
	
	public function nextInt() {
		return intval((string) $this->nextLine());
	}
	
	public function hasNextLine() {
		//var_dump($this->line);exit;
		if ($this->line) {
			return true;
		} else {
			$line = $this->stream->readLine();
			//var_dump($line);exit;
			if ($line) {
				$this->line = $line;
				return true;
			}
		}
		//return $this->stream->hasNextLine();
		return false;
	}
}

CODE
) !== false or exit;


//java/io/File
evalLazy('java/io/File1', <<<'CODE'

namespace java\io;
	
class File1 extends \java\lang\Object {
	
	private $path;
	
	public function __construct($path) {
		$this->path = "$path";
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function exists() {
		return file_exists($this->path);
	}
	
	public function isFile() {
		return is_file($this->path);
	}
	
	public function length() {
		if ($this->isFile()) {
			return filesize($this->path);
		} else {
			return 0;
		}
	}
	
	public function createNewFile() {
		if (!$this->exists()) {
			file_put_contents($this->path, '');
		}
	}
	
	public function delete() {
		unlink($this->path);
	}
	
	public function getAbsoluteFile() {
		$realpath = realpath($this->path);
		if ($realpath === false) {
			$this->createNewFile();
			$realpath = realpath($this->path);
			$this->delete();
		}
		return new File($realpath);
	}
	
	public function toString() {
		return jstring($this->path);
	}
}

CODE
) !== false or exit;


//java/lang/Character
evalLazy('java/lang/Character', <<<'CODE'

namespace java\lang;
	
class Character extends \java\lang\Object {
	
	private $char;
	
	public static $TYPE;
	
	public function __construct($char) {
		$this->char = "$char";
	}
	
	public function charValue() {
		return $this->char;
	}
	
	public static function valueOf($char) {
		return new Character("$char");
	}
	
	public function toString() {
		return jstring(chr($this->char));
	}
	
	public static function isLetter($char) {
		return ctype_alpha(chr($char));
	}
	
	public static function isLetterOrDigit($char) {
		return ctype_alnum(chr($char));
	}
	
	public static function isWhiteSpace($char) {
		return ctype_space(chr($char));
	}
	
	public static function toUpperCase($c) {
		return ord(strtoupper(chr($c)));
	}
	
	public static function toLowerCase($c) {
		return ord(strtolower(chr($c)));
	}
}

afterClassLoad('java\lang\Character', function(){
	Character::$TYPE = Clazz::getPrimitiveClass('char');
});
CODE
) !== false or exit;


//java/lang/String
evalLazy('java/lang/String', <<<'CODE'

namespace java\lang;
	
class String extends Object implements \java\io\Serializable, Comparable, CharSequence {
	
	private $string;
	private $hash = 0;
	
	public function __construct($string = '', $offset = null, $length = null) {
		//String( sql, offset, length );
		if (self::isCharArray($string)) {
			$this->string = '';
			if ($offset === null) {
				$offset = 0;
			}
			if ($length === null) {
				$length = count($string);
			}
			$length += $offset;
			for ($i = $offset; $i < $length; $i++) {
				$this->string .= chr($string[$i]);
			}
			//var_dump($this->string);
		} else {
			$this->string = "$string";
		}
	}
	
	private static function isCharArray($array) {
		if (!$array instanceof \JavaArray) {
			return false;
		}
		$component_type = $array->getClass()
								->getComponentType()
								->getName();
		return in_array("$component_type", ['char', 'byte']);
	}
	
	public function indexOf($str, $fromIndex = 0) {
		if (is_int($str)) {
			$str = chr($str);
		}
		if ($fromIndex >= $this->length()) {
			return -1;
		}
		$pos =  strpos($this->string, "$str", +"$fromIndex");
		return $pos !== false ? $pos : -1;
	}
	
	public function lastIndexOf($str, $fromIndex = null) {
		if (is_int($str)) {
			$str = chr($str);
		}
		$len = $this->length();
		if ($fromIndex === null) {
			$fromIndex = $len;
		}
		$fromIndex = min($fromIndex, $this->length());
		//var_dump($fromIndex);
		$fromIndex = $len - $fromIndex;
		$thisString = strrev($this->string);
		//var_dump([$thisString, strrev("$str"), +"$fromIndex"]);readline();
		$pos =  strpos($thisString, strrev("$str"), +"$fromIndex");
		return $pos !== false ? $len - ($pos + 1) : -1;
	}
	
	public function split($delimiter, $limit = 0) {
		if ($limit > 0) {
			$ar = preg_split("/$delimiter/", $this->string, $limit);
		} else {
			$ar = preg_split("/$delimiter/", $this->string);
		}
		if ($limit == 0) {
			while (end($ar) === '') {
				array_pop($ar);
			}
		}
		foreach ($ar as $i => $item) {
			$ar[$i] = new String($ar[$i]);
		}
		return \JavaArray::fromArray($ar, 'java.lang.String');
	}
	
	public function substring($start, $end = null) {
		if ($end === null) {
			$end = strlen($this->string);
		}
		return new String(substr($this->string, $start, $end - $start));
	}
	
	public function replace($from, $to) {
		return new String(str_replace("$from", "$to", $this->string));
	}
	
	public function replaceAll($from, $to) {
		return new String(str_replace("$from", "$to", $this->string));
	}
	
	public function trim() {
		return new String(trim($this->string));
	}
	
	public function toLowerCase() {
		return new String(strtolower($this->string));
	}
	
	public function toUpperCase() {
		return new String(strtoupper($this->string));
	}
	
	public function length() {
		return strlen($this->string);
	}
	
	public function toCharArray() {
		//return str_split($this->string);
		$ar_chr = [];
		foreach (str_split($this->string) as $c) {
			$ar_chr[] = ord($c);
		}
		return \JavaArray::fromArray($ar_chr, 'C');
	}
	
	public function getChars($srcBegin, $srcEnd, $dst, $dstBegin) {
		if (is_object($srcBegin)) {
			var_dump('String.getChars() error');
			exit;
		}
        if ($srcBegin < 0) {
            throw new StringIndexOutOfBoundsException($srcBegin);
        }
        if ($srcEnd > $this->length()) {
            throw new StringIndexOutOfBoundsException($srcEnd);
        }
        if ($srcBegin > $srcEnd) {
            throw new StringIndexOutOfBoundsException($srcEnd - $srcBegin);
        }
        System::arraycopy($this->toCharArray(), $srcBegin, $dst, $dstBegin, $srcEnd - $srcBegin);
		//var_dump($dst);exit;
	}
	
	public function charAt($i) {
		if ($i < 0 || $i >= $this->length()) {
			//var_dump([$this->string, $i]);
			//debug_print_backtrace(0, 10);
			//exit;
			throw new StringIndexOutOfBoundsException($i);
		}
		return ord($this->string[$i]);
	}
	
	public function startsWith($str) {
		$str = "$str";
		return substr($this->string, 0, strlen($str)) == $str;
	}
	
	public function endsWith($str) {
		$str = "$str";
		return substr($this->string, -strlen($str)) == $str;
	}
	
	public function concat(String $o) {
		return new String($this->string . $o->string);
	}
	
	public function regionMatches($ignoreCase, $toffset, $otherString, $ooffset, $len) {
		$str1 = substr($this->string, $toffset, $len);
		$str2 = substr($otherString->string, $toffset, $len);
		if ($ignoreCase) {
			$str1 = strtolower($str1);
			$str2 = strtolower($str2);
		}
		return $str1 === $str2;
	}
	
	public function equals($o) {
		if ($o instanceof String) {
			//var_dump($this->string, $o->string, $this->string == $o->string);
			return $this->string == $o->string;
		} else {
			return false;
		}
	}
	
	public function equalsIgnoreCase($o) {
		if ($o instanceof String && $o !== null) {
			return $this->toLowerCase()->equals($o->toLowerCase());
		} else {
			return false;
		}
	}
	
	public function hashCode() {
		$h = $this->hash;
        $len = $this->length();
		if ($h == 0 && $len > 0) {
			$off = 0;
			$val = $this->string;

            for ($i = 0; $i < $len; $i++) {
                $h = 31*$h + ord($val[$off++]);
            }
            $this->hash = $h;
        }
        return (int)$h;
	}
	
	public static function format($str, $args) {
		$args = func_get_args();
		$args[0] = ''.$args[0];
		return new self(call_user_func_array('sprintf', $args));
	}
	
	public static function valueOf($o) {
		return jstring($o);
	}
	
	private static $interned_strings = [];
	public function intern() {
		if (isset(self::$interned_strings[$this->string])) {
			return self::$interned_strings[$this->string];
		} else {
			self::$interned_strings[$this->string] = $this;
		}
		return $this;
	}
	
	public function toString() {
		return $this;
	}
	public function __toString() {
		//var_dump($this->string);
		return (string) $this->string;
	}
}

CODE
) !== false or exit;

//java/lang/StringBuilder
evalLazy('java/lang/StringBuilder', <<<'CODE'

namespace java\lang;
	
class StringBuilder extends Object implements \java\io\Serializable, Comparable, CharSequence  {
	
	private $string;
	
	public function __construct($ini = null) {
		$this->string = '';
		if ($ini !== null && $ini instanceof CharSequence) {
			$this->string .= "$ini";
		}
	}
	
	public function __call($method, $args) {
		$func_args = func_get_args();
		if(count($func_args) > 2) {
			$opcode = $func_args[2];
		} else {
			$opcode = null;
		}
		if ($method == 'append') {
			if ($opcode[2]['args'][0] == 'C') {
				$args[0] = chr($args[0]);
			}
			//return $this->__call('_append', $args);
			return call_user_func_array([$this, '_append'], $args);
		} else {
			return parent::__call($method, $args, $opcode);
		}
		//var_dump($method, $args, $opcode);exit;
	}
	
	private function _append($s, $offset = null, $length = null) {
		//var_dump($s);
		
		if ($offset !== null && $length !== null) {
			for ($i = 0; $i < $length; $i++) {
				$this->string .= chr($s[$offset + $i]);
			}
			return $this;
		}
		
		if ($s === null) {
			$s = 'null';
		}
		$this->string .= jstring($s);
		return $this;
	}
	
	public function setLength($newLength) {
		 //var_dump($newLength);
		
		if ($newLength < 0) {
			throw new StringIndexOutOfBoundsException($newLength);
		}
		
		$length = $this->length();
		
		if ($newLength < $length) {
			$this->string = substr($this->string, 0, $newLength);
		}
		
		if ($newLength > $length) {
			$this->string .= str_repeat("\0", $newLength - $length);
		}
	}
	
	public function length() {
		return strlen($this->string);
	}
	
	public function charAt($i) {
		return ord(substr($this->string, $i, 1));
	}

	public function reverse() {
		$this->string = strrev($this->string);
		return $this;
	}
	
	public function toString() {
		//var_dump($this->string);
		return new String($this->string);
	}
}

CODE
) !== false or exit;

evalLazy('java/lang/StringBuffer', <<<'CODE'

namespace java\lang;

class StringBuffer extends StringBuilder {}

CODE
) !== false or exit;

//java/util/StringTokenizer
evalLazy('java/util/StringTokenizer', <<<'CODE'

namespace java\util;
	
class StringTokenizer extends \java\lang\Object {
	
	private $tokens;
	
	public function __construct($str, $delim = " \t\n\r\f", $returnDelims = false) {
		$this->tokens = explode("$delim", "$str");
	}
	
	public function hasMoreElements() {
		return $this->hasMoreTokens();
	}
	
	public function hasMoreTokens() {
		return !empty($this->tokens);
	}
	
	public function nextElement() {
		return $this->nextToken();
	}
	
	public function nextToken() {
		if (!$this->hasMoreTokens()) {
			throw new NoSuchElementException();
		}
		return new \java\lang\String(array_shift($this->tokens));
	}
}

CODE
) !== false or exit;


//java/util/ArrayList
evalLazy('java/util/ArrayList1', <<<'CODE'

namespace java\util;
	
class ArrayList1 extends \java\lang\Object {
	
	protected $array;
	
	public function __construct($array = []) {
		if (is_array($array)) {
			$this->array = $array;
		} else {
			$this->array = [];
		}
	}
	
	public function add($obj) {
		$this->array[] = $obj;
		return true;
	}
	
	public function get($i) {
		return $this->array["$i"];
	}
	
	public function set($i, $obj) {
		$this->array["$i"] = $obj;
	}
	
	public function clear() {
		$this->array = [];
	}
	
	public function contains($obj) {
		return in_array($obj, $this->array);
	}
	
	public function indexOf($obj) {
		$index = array_search($obj, $this->array);
		if ($index === false) {
			return -1;
		} else {
			return $index;
		}
	}
	
	public function size() {
		return count($this->array);
	}
	
	public function iterator() {
		return new HashIterator($this->array);
	}
	
	public function toString() {
		return '['.implode(', ', $this->array).']';
	}
}

class Arrays1 extends \java\lang\Object {
	public static function asList($array) {
		return new ArrayList($array->toArray());
	}
}

CODE
) !== false or exit;

//java/util/Vector
evalLazy('java/util/Vector1', <<<'CODE'

namespace java\util;
	
class Vector1 extends ArrayList1 {
}

CODE
) !== false or exit;

//java/lang/Random
evalLazy('java/lang/Random', <<<'CODE'

namespace java\util;
	
class Random extends \java\lang\Object {
	
	private $seed;
	
	public function __construct($seed = 0) {
		$this->seed = $seed;
	}

	public function nextDouble() {
		return \mt_rand(0, 1000) / 1000;
	}
}

CODE
) !== false or exit;

//java/util/concurrent/ConcurrentHashMap
evalLazy('java/util/concurrent/ConcurrentHashMap', <<<'CODE'

namespace java\util\concurrent;
class ConcurrentHashMap extends \java\util\HashMap {

	public function putIfAbsent($key, $value) {
		if ($value === null) {
			throw new \java\lang\NullPointerException();
		}
		$oldValue = $this->get($key);
		if ($oldValue === null) {
			$this->put($key, $value);
		}
		return $oldValue;
	}
}
CODE
) !== false or exit;

//java/util/HashMap
evalLazy('java/util/HashMap1', <<<'CODE'

namespace java\util;
	
class HashMap1 extends ArrayList1 {

	public function put($key, $value) {
		$key = "$key";
		$old = isset($this->array[$key]) ? $this->array[$key] : null;
		$this->array[$key] = $value;
		return $old;
	}
	
	public function keySet() {
		return new HashSet(array_keys($this->array));
	}
	
	public function toString() {
		$values = [];
		foreach ($this->array as $k => $v) {
			$values[] = "$k=$v";
		}
		return "{".implode(', ', $values)."}";
	}
}
CODE
) !== false or exit;

//java/util/HashSet
evalLazy('java/util/HashSet1', <<<'CODE'

namespace java\util;
	
class HashSet1 extends ArrayList1 {
	
	public function __construct($array = []) {
		parent::__construct($array);
		$this->array = array_unique($this->array);
	}
	
	public function add($value) {
		parent::add($value);
		$this->array = array_unique($this->array);
	}
}
CODE
) !== false or exit;


//java/util/HashIterator
evalLazy('java/util/HashIterator1', <<<'CODE'

namespace java\util;
	
class HashIterator1 extends \ArrayIterator {
	
	
	public function next() {
		$current = $this->current();
		parent::next();
		return $current;
	}
	public function hasNext() {
		$hasNext = $this->valid();
		return $hasNext;
	}
}
CODE
) !== false or exit;


//java/util/Hashtable
evalLazy('java/util/Hashtable1', <<<'CODE'

namespace java\util;
	
class Hashtable1 extends HashMap1 {
	
}
CODE
) !== false or exit;

//java/util/WeakHashMap
evalLazy('java/util/WeakHashMap1', <<<'CODE'

namespace java\util;
	
class WeakHashMap1 extends HashMap1 {
	
}
CODE
) !== false or exit;

//java/util/Properties
evalLazy('java/util/Properties1', <<<'CODE'

namespace java\util;
	
class Properties1 extends Hashtable1 {
	public function setProperty($key, $value) {
		return $this->put($key, $value);
	}
	
	public function getProperty($key, $defaultValue = null) {
		$value = $this->get($key);
		return $value !== null ? $value : $defaultValue;
	}
}
CODE
) !== false or exit;


//java/lang/StrictMath
evalLazy('java/lang/StrictMath', <<<'CODE'

namespace java\lang;
	
class StrictMath extends \java\lang\Object {
	public static function random() {
		return \mt_rand(0, 1000) / 1000;
	}
	
	public static function sqrt($n) {
		return sqrt($n);
	}
	
	public static function max() {
		return max(func_get_args());
	}

	public static function min() {
		return min(func_get_args());
	}
}

//class Math extends StrictMath {}

CODE
) !== false or exit;


//java/lang/Number
evalLazy('java/lang/Number', <<<'CODE'

namespace java\lang;
	
class Number extends Object {

	public static $TYPE;
	
	protected $v;
	
	public function __construct($v = null) {
		
		/*
		if ($v === null) {
			var_dump("Number parser error!");
			var_dump($this);
			exit;
		}
		//*/
		
		if ($v !== null) {
			if (!is_numeric("$v")) {
				throw new \java\lang\NumberFormatException("For input string: \"$v\"");
			}
			$this->v = "$v";
		}
	}
	
	public function intValue() {
		return (int) $this->v;
	}
	
	public function longValue() {
		return $this->v;
	}
	
	public function doubleValue() {
		return +$this->v;
	}
	
	public function floatValue() {
		return $this->doubleValue();
	}
	
	public static function toHexString($n) {
		return \jstring(\dechex($n));
	}
	
	public function toString($i = null) {
		if ($i !== null) {
			return new String($i);
		} else {
			return new String($this->v);
		}
	}
	
	public function equals($o) {
		var_dump("$this");
		var_dump("$o");
		return "$this" == "$o";
	}
}

CODE
) !== false or exit;

//java/lang/Integer
evalLazy('java/lang/Integer', <<<'CODE'

namespace java\lang;
	
class Integer extends Number {
	
	const MAX_VALUE = 0x7fffffff;
	
	public static $TYPE;

	public static function parseInt($in) {
		return intval($in.'');
	}

	public static function valueOf($v) {
		return new self($v);
	}
	
	public static function highestOneBit($i) {
		$i |= ($i >>  1);
		$i |= ($i >>  2);
		$i |= ($i >>  4);
		$i |= ($i >>  8);
		$i |= ($i >> 16);
		return $i - ($i >> 1);
    }
	
	public static function lowestOneBit($i) {
		return $i & -$i;
    }
	
	public static function bitCount($i) {
		$i = $i - (($i >> 1) & 0x55555555);
		$i = ($i & 0x33333333) + (($i >> 2) & 0x33333333);
		$i = ($i + ($i >> 4)) & 0x0f0f0f0f;
		$i = $i + ($i >> 8);
		$i = $i + ($i >> 16);
		return $i & 0x3f;
    }
}

afterClassLoad('java\lang\Integer', function(){
	Integer::$TYPE = Clazz::getPrimitiveClass('int');
});

CODE
) !== false or exit;

//java/util/concurrent/atomic/AtomicInteger
evalLazy('java/util/concurrent/atomic/AtomicInteger', <<<'CODE'

namespace java\util\concurrent\atomic;
	
class AtomicInteger extends \java\lang\Number {

	public function __construct($v = 0) {
		parent::__construct($v);
	}

	public function get() {
		return $this->v;
	}
	
	public function set($v) {
		if (!is_numeric("$v")) {
			throw new \java\lang\NumberFormatException("For input string: \"$v\"");
		}
		$this->v = +"$v";
	}
	
	public function incrementAndGet() {
		return ++$this->v;
	}

	public function decrementAndGet() {
		return --$this->v;
	}
	
	public function getAndIncrement() {
		return $this->v++;
	}
	
	public function getAndDecrement() {
		return $this->v--;
	}
	
	public function getAndAdd($delta) {
		$current = $this->v;
		$this->v += $delta;
		return $current;
	}
	
	public static function parseInt($in) {
		return intval($in.'');
	}

	public static function valueOf($v) {
		return new self($v);
	}
}

CODE
) !== false or exit;

//java/util/concurrent/atomic/AtomicLong
evalLazy('java/util/concurrent/atomic/AtomicLong', <<<'CODE'

namespace java\util\concurrent\atomic;
	
class AtomicLong extends \java\lang\Number {

	public function __construct($v = '0') {
		parent::__construct($v);
	}

	public function get() {
		return $this->v;
	}
	
	public function set($v) {
		if (!is_numeric("$v")) {
			throw new \java\lang\NumberFormatException("For input string: \"$v\"");
		}
		$this->v = $v;
	}
	
	public function incrementAndGet() {
		$v = $this->v;
		$this->v = bcadd($this->v, '1');
		return $v;
	}

	public function decrementAndGet() {
		$v = $this->v;
		$this->v = bcsub($this->v, '1');
		return $v;
	}
	
	public function getAndIncrement() {
		$this->v = bcadd($this->v, '1');
		return $this->v;
	}
	
	public function getAndDecrement() {
		$this->v = bcsub($this->v, '1');
		return $this->v;
	}
	
	public function getAndAdd($delta) {
		$current = $this->v;
		$this->v = bcadd($this->v, "$delta");
		return $current;
	}
	
	public static function parseInt($in) {
		return intval($in.'');
	}

	public static function valueOf($v) {
		return new AtomicLong($v);
	}
}

CODE
) !== false or exit;

//java/lang/Byte
evalLazy('java/lang/Byte', <<<'CODE'

namespace java\lang;
	
class Byte extends Number {
	
	public static $TYPE;

	public static function parseByte($in) {
		return intval("$in");
	}

	public static function valueOf($v) {
		return new self($v);
	}
}

afterClassLoad('java\lang\Byte', function(){
	Byte::$TYPE = Clazz::getPrimitiveClass('byte');
});

CODE
) !== false or exit;

//java/lang/Double
evalLazy('java/lang/Double', <<<'CODE'

namespace java\lang;
	
class Double extends Number {
	
	public static $TYPE;
	
	public static function doubleToLongBits($d) {
		return $d | 0;
	}
	
	public static function valueOf($v) {
		return new self($v);
	}
}

afterClassLoad('java\lang\Double', function(){
	Double::$TYPE = Clazz::getPrimitiveClass('double');
});

CODE
) !== false or exit;

//java/lang/Float
evalLazy('java/lang/Float', <<<'CODE'

namespace java\lang;
	
class Float extends Number {
	
	public static $TYPE;
	
	public static function floatToIntBits($f) {
		return $f | 0;
	}
	
	public static function valueOf($v) {
		return new self($v);
	}
	
	public static function isNaN($f) {
		//var_dump($f);
		return false;//WTF?
	}
}

afterClassLoad('java\lang\Float', function(){
	Float::$TYPE = Clazz::getPrimitiveClass('float');
});

CODE
) !== false or exit;

//java/lang/Short
evalLazy('java/lang/Short', <<<'CODE'

namespace java\lang;
	
class Short extends Number {
	
	public static $TYPE;
	
	public static $MAX_VALUE = '32767';
	public static $MIN_VALUE = '-32768';
	
	public static function valueOf($v) {
		return new self($v);
	}
}

afterClassLoad('java\lang\Short', function(){
	Short::$TYPE = Clazz::getPrimitiveClass('short');
});

CODE
) !== false or exit;

//java/lang/Long
evalLazy('java/lang/Long', <<<'CODE'

namespace java\lang;
	
class Long extends Number {
	
	public static $TYPE;
	
	public static $MAX_VALUE = '9223372036854775807';
	public static $MIN_VALUE = '-9223372036854775808';
	
	public static function valueOf($v) {
		return new self($v);
	}
}

afterClassLoad('java\lang\Long', function(){
	Long::$TYPE = Clazz::getPrimitiveClass('long');
});

CODE
) !== false or exit;

//java/math/BigInteger
evalLazy('java/math/BigInteger1', <<<'CODE'

namespace java\math;
	
class BigInteger1 extends \java\lang\Number {
	
	public static function valueOf($v) {
		return new self($v);
	}
	
	public function add($v) {
		return new self($this->v + "$v");
	}
}

CODE
) !== false or exit;

//java/lang/Boolean
evalLazy('java/lang/Boolean', <<<'CODE'

namespace java\lang;
	
class Boolean extends \java\lang\Object {
	
	private $v;
	
	public static $TYPE;
	
	public function __construct($v) {
		//var_dump($v);
		parent::__construct();
		if (   strtolower("$v") === "true"
		    || $v === true
			|| $v === 1
			|| $v === "1") {
			$this->v = true;
		} else {
			$this->v = false;
		}
	}
	
	public function booleanValue() {
		return (bool) $this->v;
	}
	
	public static function parseBoolean($s) {
		return (bool) "$s";
	}
	
	public static function valueOf($v) {
		return new self($v);
	}
	
	public static function getBoolean($name) {
		$result = System::getProperty($name);
		if ($result !== null && $result->equalsIgnoreCase(jstring("true"))) {
			return true;
		} else {
			return false;
		}
	}
	
	public function toString() {
		return new String($this->v ? "true" : "false");
	}
}

afterClassLoad('java\lang\Boolean', function(){
	Boolean::$TYPE = Clazz::getPrimitiveClass('boolean');
});
CODE
) !== false or exit;

//sun/reflect/misc/ReflectUtil
evalLazy('sun/reflect/misc/ReflectUtil', <<<'CODE'
	
namespace sun\reflect\misc;

class ReflectUtil extends \java\lang\Object {

	public function __construct() {
		
	}
	
	public static function checkPackageAccess($string) {}
	
	public static function ensureMemberAccess($currentClass, $memberClass, $target, $modifiers) {}
	
	public static function forName($className) {
		return \java\lang\Clazz::forName($className);
	}
	
	public static function isPackageAccessible($class) {
		return true;
	}
	
	private static function isSubclassOf($queryClass, $ofClass) {
		return is_a($queryClass->getName().'', $ofClass->getName().'', true);
	}
	
	public static function newInstance($class) {
		return $class->newInstance();
	}
}
CODE
) !== false or exit;

//sun/misc/Unsafe
evalLazy('sun/misc/Unsafe1', <<<'CODE'
namespace sun\misc;

class Unsafe1 extends \java\lang\Object {
	private static $theUnsafe;
	
	public static function getUnsafe() {
		if (self::$theUnsafe === null) {
			self::$theUnsafe = new Unsafe();
		}
		return self::$theUnsafe;
	}
}

CODE
) !== false or exit;

//sun/misc/SharedSecrets
evalLazy('sun/misc/SharedSecrets1', <<<'CODE'
namespace sun\misc;

class SharedSecrets1 extends \java\lang\Object {
	private static $javaLangAccess;
	private static $javaIOAccess;
	private static $javaNioAccess;
	private static $javaIOFileAccess;
	private static $javaUtilJarAccess;
	private static $javaAWTAccess;
	private static $javaSecurityAccess;
	
	public static function getJavaLangAccess() {
		return self::$javaLangAccess;
	}
	
	public static function setJavaLangAccess($javaLangAccess) {
		self::$javaLangAccess = $javaLangAccess;
	}
	
	public static function getJavaLangAccess() {
		return self::$javaLangAccess;
	}
	
	public static function setJavaIOAccess($jio) {
		self::$javaIOAccess = $jio;
	}
	
	public static function getJavaIOAccess() {
		return self::$javaIOAccess;
	}
	
	public static function setJavaNioAccess($jnio) {
		self::$javaNioAccess = $jnio;
	}
	
	public static function getJavaNioAccess() {
		return self::$javaNioAccess;
	}
	
	public static function setJavaIOFileAccess($iofile) {
		self::$javaIOFileAccess = $iofile;
	}
	
	public static function getJavaIOFileAccess() {
		return self::$javaIOFileAccess
	}
	
	public static function setJavaUtilJarAccess($jarAccess) {
		self::$javaUtilJarAccess = $jarAccess;
	}
	
	public static function getJavaUtilJarAccess() {
		return self::$javaUtilJarAccess;
	}
	
	public static function getJavaAWTAccess() {
		return self::$javaAWTAccess;
	}
	
	public static function setJavaSecurityAccess($javaSecurityAccess) {
		self::$javaSecurityAccess = $javaSecurityAccess;
	}
	
	public static funciton getJavaSecurityAccess() {
		return self::$javaSecurityAccess;
	}
}
CODE
) !== false or exit;

//java/lang/Throwable
evalLazy('java/lang/Throwable', <<<'CODE'

namespace java\lang;
	
//class Exception extends \Exception {}
class Throwable extends \Exception {
	use ObjectTrait;
	
	private $cause;
	
	public function __construct($message = '', $cause = null) {
		if ($message !== '__EXCEPTION_DONT_INIT__') {
			//var_dump([get_class($this), $message]);
			if ($message instanceof Throwable) {
				$cause = $message;
				$message = null;
			}
			parent::__construct($message, 0, $cause);
			$this->message = $message ? jstring($message) : null;
			if ($cause !== null) {
				$this->cause = $cause;
			} else {
				$this->cause = $this;
			}
		}
	}
	
	public function printStackTrace($outstream = null) {
		//var_dump(get_called_class(), $outstream);return;
		//var_dump("$this");
		//debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);exit;
		if ($outstream === null) {
			$outstream = System::$out;
		}
		
		$trace = $this->getTrace();
		//var_dump(count($trace));exit;
		foreach ($trace as $t) {
			if (	!empty($t['class']) 
				 && !in_array($t['class'], ['php_javaClass', 'ReflectionClass', 'ReflectionMethod'])) {
				if (in_array($t['function'], ['__call', '__callstatic'])) {
					$t['function'] = $t['args'][0];
				}
				$outstream->println($t['class'].$t['type'].$t['function']);
			}
		}
		
		//$outstream->println($str_trace);
	}
	
	public function getLocalizedMessage() {
		return $this->getMessage();
	}
	
	public function initCause(Throwable $cause) {
		if ($this->cause != $this) {
			throw new IllegalStateException("Can't overwrite cause");
		}
		if ($cause == $this) {
			throw new IllegalArgumentException("Self-causation not permitted");
		}
		$this->cause = $cause;
		return $this;
	}
	
	public function getCause() {
		return $this->cause === $this ? null : $this->cause;
	}
	
	public function getStackTrace() {
		return new \JavaArray(0, 'Ljava.lang.StackTraceElement;');
	}
	
	public function toString() {
		$s = $this->getClass()->getName();
		$message = $this->getLocalizedMessage();
		return $message != null ? jstring("$s: $message") : $s;
	}
}

CODE
) !== false or exit;

//java/lang/Thread
evalLazy('java/lang/Thread', <<<'CODE'

namespace java\lang;
	
class Thread extends Object {
	
	/* java\lang\Thread[] */
	private static $threads;
	
	/* long */
	public static $currentThreadId = 0;
	
	/* long */
	public $tid = 0;
	
	/* String */
	private $name;
	
	/* ThreadGroup */
	private $group;
	
	/* Interruptible */
	private $blocker;
	
	/* java\lang\ClassLoader */ 
	private $contextClassLoader;
	
	private $nativeThread;
	
	private $target;
	
	// boolean
	private $daemon;
	
	// int
	private $priority = 0;
	
	// int
	private $threadStatus = 0;
	
	// boolean
	private $interrupted = false;
	
	public $threadLocals;

	public function __construct($target = null, $name = null) {
	
		$this->tid = self::nextThreadID();
		self::$threads[$this->tid] = $this;
		
		$args = func_get_args();
		foreach ($args as $arg) {
			if (!$arg) {
				continue;
			} else if ($arg instanceof String) {
				$this->name = $arg;
			} else if ($arg instanceof Runnable) {
				$this->target = $arg;
			} else if ($arg instanceof ThreadGroup) {
				$this->group = $arg;
			}
		}
		
		if ($this->name === null) {
			$this->name = jstring('Thread-'. self::nextThreadNum());
		}
		
		$parent = self::currentThread();
		
		if ($parent !== null) {
			
			if ($this->group === null) {
				$this->group = $parent->getThreadGroup();
			}
			
			$this->group->addUnstarted();
			
			$this->daemon = $parent->isDaemon();
			$this->priority = $parent->getPriority();
			$this->contextClassLoader = $parent->getContextClassLoader();
		}
	}
	
	/* long */
	private static $threadSeqNumber = 0; 
	
	private static function nextThreadID() {
		return self::$threadSeqNumber++;
	}
	
	/* long */
	private static $threadInitNumber = 0; 
	
	private static function nextThreadNum() {
		return self::$threadInitNumber++;
	}
	
	public function start() {
		
		//disable threads 
		//return;
		if ($this->name->equals(jstring('Reference Handler'))) {
			return;
		}
		
		
		if ($this->threadStatus !== 0) {
			throw new IllegalThreadStateException();
		}

		$opcode = [2 => ['type' => '([Ljava/lang/Thread;)V']];
		$this->group->__call('add', [$this], $opcode);

		$this->run();
		
		//var_dump($this->target); exit;
		//$this->nativeThread = new \PhpThread([$this, 'run'], [], $this);		
		//$this->nativeThread->start();
	}
	
	public function interrupt() {
	
		$this->interrupted = true;
		
		if ($this->blocker !== null) {
			$this->blocker->interrupt();
		}
		//throw new InterruptedException();
		//exit;
	}
	
	public function isInterrupted($clear = false) {
		if ($clear) {
			return $this->interrupted = false;
		}
		return $this->interrupted;
	}
	
	public function run() {
		if ($this->target !== null) {
			$this->target->run();
		}
	}
	
	/* long */
	public function getId() {
		return $this->tid;
	}
	
	public function isDaemon() {
		return $this->daemon;
	}
	
	public function setDaemon($daemon) {
		$this->daemon = !!$daemon;
	}
	
	public function getPriority() {
		return $this->priority;
	}
	
	public function setPriority($priority) {
		$this->priority = $priority;
	}
	
	/* ThreadGroup */
	public function getThreadGroup() {
		return $this->group;
	}
	
	/* int */
	public static function activeCount() {
        return self::currentThread()->getThreadGroup()->activeCount();
    }
	
	/* long */
	public static function sleep($milisecs) {
		usleep($milisecs * 1000);
	}
	
	/* java\lang\Thread */
	public static function currentThread() {
		if (!isset(self::$threads[self::$currentThreadId])) {
			//$thread = new Thread();
			//self::$currentThreadId = $thread->tid;
			return null;
		}
		return self::$threads[self::$currentThreadId];
	}
	
	/* void */
	public function setContextClassLoader(\java\lang\ClassLoader $loader = null) {
		$this->contextClassLoader = $loader;
	}
	
	/* java\lang\ClassLoader */
	public function getContextClassLoader() {
		return $this->contextClassLoader;
	}
	
	public function blockedOn($blocker) {
		$this->blocker = $blocker;
	}
	
	private $alive = false;
	
	public function isAlive() {
		return $this->alive;
	}
}

CODE
) !== false or exit;

//java/lang/ThreadLocal
evalLazy('java/lang/ThreadLocal1', <<<'CODE'

namespace java\lang;
	
class ThreadLocal1 extends \java\lang\Object {
	private $value = [];
	
	public function get() {
		exit;
		if (isset($this->value[Thread::currentThread()->getId()])) {
			return $this->value[Thread::currentThread()->getId()];
		} else {
			return null;
		}
	}
	
	public function set($value) {
		$this->value[Thread::currentThread()->getId()] = $value;
	}
}

CODE
) !== false or exit;

//java/io/BufferedInputStream
evalLazy('java/io/BufferedInputStream', <<<'CODE'

namespace java\io;

class BufferedInputStream extends FilterInputStream {
	
	public function __construct($in) {
		parent::__construct($in);
	}
	
	public function readLine() {
		return $this->in->readLine();
	}
}
CODE
) !== false or exit;

//java/io/FileInputStream
evalLazy('java/io/FileInputStream', <<<'CODE'

namespace java\io;
	
class FileInputStream extends \java\lang\Object {
	
	private $file;
	private $stream;
	
	public function __construct($file) {
		//var_dump($file, is_resource($file), get_resource_type($file));
		if (is_resource($file)) {
			$this->file = $file;
		} else {
			@$this->file = fopen($file, 'rb');
			if ($this->file === false) {
				//println($file); exit;
				throw new FileNotFoundException($file);
			}
		}
		$this->stream = new \DataInputStream($this->file);
	}
	
	public function read($b = null, $off = null, $len = null) {
		if ($b === null) {
			return $this->readByte();
		} else {
			$this->readFully($b, $off, $len);
			return $len;
		}
	}
	
	public function readByte() {
		return $this->stream->readByte();
	}
	
	public function peek() {
		return $this->peekByte();
	}
	
	public function peekByte() {
		$b = $this->readByte();
		$this->stream->skipBytes(-1);
		return $b;
	}
	
	public function readShort() {
		//return $this->stream->readShort();
        $ch1 = $this->stream->readByte();
        $ch2 = $this->stream->readByte();
        if (($ch1 | $ch2) < 0) {
            throw new EOFException();
		}
        return (($ch1 << 8) + ($ch2 << 0));
	}
	
	public function readUnsignedShort() {
		//var_dump('readUnsignedShort');
		//var_dump(decbin($this->readShort()));
		//var_dump(decbin(27));
		//exit;
		return $this->readShort();
	}
	
	public function readUTF() {
		//return $this->stream->readUTF();
		//$len = $this->stream->readInt();
		$len = $this->readShort();
		if ($len > 100) {
			println("(e".($len & 0xFFFF).")");
			$len = $this->readShort();
		}
		println("($len)");
		if ($len == 0) {return '';}
		return utf8_decode($this->stream->readChar($len));
	}
	
	public function readLongUTF() {
		$len = $this->stream->readLong();
		if ($len == 0) {return '';}
		return utf8_decode($this->stream->readChar($len));
	}
	
	public function readLine() {
		$line = $this->stream->readLine();
		//var_dump('??', $line);exit;
		if ($line === false) {
			return null;
		} else {
			return jstring($line)->trim();
		}
	}
	
	public function readFully(\SplFixedArray $ar, $offset, $length) {
		for ($i = 0; $i < $length; $i++) {
			$ar[$offset + $i] = $this->readByte();
		}
	}
	
	public function close() {
		$this->file = null;
	}
}

CODE
) !== false or exit;

//java/io/DataInputStream
evalLazy('java/io/DataInputStream', <<<'CODE'

namespace java\io;
	
class DataInputStream extends \java\lang\Object {
	
	private $input;
	
	public function __construct($input) {
		$this->input = $input;
	}
	
	public function read() {
		return $this->input->read();
	}
	
	public function readUnsignedShort() {
		$ch1 = $this->input->read();
        $ch2 = $this->input->read();
        //var_dump("DataInputStream::readUnsignedShort");var_dump($ch1, $ch2);readline();
        if (($ch1 | $ch2) < 0) {
			//var_dump("?EOFException");
			//var_dump($ch1, $ch2);
			//var_dump("exit");
			//exit;
			throw new EOFException();
		}
        return ($ch1 << 8) + ($ch2 << 0);
	}
}

CODE
) !== false or exit;

//java/io/ObjectInputStream
evalLazy('java/io/ObjectInputStream1', <<<'CODE'

namespace java\io;
	
class ObjectInputStream1 extends \java\lang\Object {
	
	const STREAM_MAGIC = 0xACED;
	const STREAM_VERSION = 5;
	
	const TC_STRING = 0x74;
	const TC_LONGSTRING = 0x7c;
	const TC_OBJECT = 0x73;
	
	const TC_NULL = 0x70;
	const TC_REFERENCE = 0x71;
	const TC_PROXYCLASSDESC = 0x7d;
	const TC_CLASSDESC = 0x72;
	
	const NULL_HANDLE = -1;
	
	private $input;
	
	private $bin;
	
	public $defaultDataEnd = false;
	
	private $passHandle;
	
	const baseWireHandle = 0x7e0000;
	
	public function __construct($input) {
		$this->passHandle = self::NULL_HANDLE;
		$this->input = $input;
		$this->bin = new ObjectInputStream_S_BlockInputStream($this, $input);
		$this->readStreamHeader();
		$this->bin->setBlockDataMode(true);
	}
	
	private function readStreamHeader() {
		$s0 = $this->input->readShort();
		$s1 = $this->input->readShort();
		if ($s0 !== self::STREAM_MAGIC || $s1 !== self::STREAM_VERSION) {
			throw new StreamCorruptedException(
			\java\lang\String::format("invalid stream header: %04X%04X", $s0, $s1));
		}
	}
	
	public function readObject() {
		//return null;
		
		$tc = $this->input->peekByte();
		
		switch ($tc) {
			case self::TC_STRING:
			case self::TC_LONGSTRING:
				return $this->readString();
			case self::TC_OBJECT:
				return $this->readOrdinaryObject();
			default:
				throw new StreamCorruptedException(
				\java\lang\String::format("invalid type code: %02X", $tc));
		}
	}
	
	public function readOrdinaryObject() {
		if ($this->input->readByte() != self::TC_OBJECT) {
			throw new \java\lang\InternalError();
		}
		$desc = $this->readClassDesc();
		$desc->checkDeserialize();
	}
	
	private function readClassDesc() {
		$tc = $this->input->peekByte();
		switch ($tc) {
			case self::TC_NULL:
				return $this->readNull();

			case self::TC_REFERENCE:
				return $this->readHandle();

			case self::TC_PROXYCLASSDESC:
				return $this->readProxyDesc();

			case self::TC_CLASSDESC:
				return $this->readNonProxyDesc();
			
			default:
				throw new StreamCorruptedException(
					\java\lang\String::format("invalid type code: %02X", $tc));
		}
	}
	
	public function readNull() {
		if ($this->input->readByte() != self::TC_NULL) {
			throw new \java\lang\InternalError();
		}
		$this->passHandle = self::NULL_HANDLE;
		return null;
	}
	
	public function readHandle() {
		if ($this->input->readByte() != self::TC_REFERENCE) {
			throw new \java\lang\InternalError();
		}
		$this->passHandle = $this->input->readInt() - self::baseWireHandle;
		
		if ($this->passHandle < 0 || $this->passHandle >= $this->handles->size()) {
			throw new StreamCorruptedException(
				\java\lang\String::format("invalid handle value: %08X", $this->passHandle + 
				self::baseWireHandle));
		}
	}
	
	public function readUTF() {
		//return new \java\lang\String($this->input->readUTF());
		//return $this->readObject();
		//var_dump("ObjectInputStream::readUTF");readline();
		return $this->bin->readUTF();
	}
	
	private function readString() {
		$tc = $this->input->readByte();
		
		switch ($tc) {
			case self::TC_STRING:
				$str = $this->input->readUTF();
				break;
			
			case self::TC_LONGSTRING:
				$str = $this->input->readLongUTF();
				break;
			
			default:
				throw new StreamCorruptedException(
					\java\lang\String::format("invalid type code: %02X", $tc));
		}
		
		return new \java\lang\String($str);
	}
	
	public function close() {
		$this->input->close();
	}
}

CODE
) !== false or exit;

//java/io/ObjectInputStream$BlockInputStream
evalLazy('java/io/ObjectInputStream_S_BlockInputStream', <<<'CODE'

namespace java\io;
	
class ObjectInputStream_S_BlockInputStream extends \java\lang\Object {
	
	private $this0;
	
	const MAX_BLOCK_SIZE = 1024;
	const CHAR_BUF_SIZE = 256;
	const MAX_HEADER_SIZE = 5;
	
	const HEADER_BLOCKED = -2;
	
	const TC_BASE = 0x70;
	const TC_BLOCKDATA = 0x77;
	const TC_RESET = 0x79;
	const TC_MAX = 0x7E;
	const TC_BLOCKDATALONG = 0x7A;
	
	private $input;
	private $din;
	
	private $blockDataMode = false;
	
	private $pos = 0;
	private $end = 0;
	private $unread = 0;
	
	private $buf = [];
	private $hbuf = [];
	private $cbuf = [];
	
	public function __construct($this0, $input) {
		$this->this0 = $this0;
		
		$this->buf = new \SplFixedArray(self::MAX_BLOCK_SIZE);
		
		$this->hbuf = new \SplFixedArray(self::MAX_HEADER_SIZE);
		$this->cbuf = new \SplFixedArray(self::MAX_BLOCK_SIZE);
		
		$this->input = $input;
		$this->din = new DataInputStream($this);
	}
	
	public function setBlockDataMode($mode) {
		if ($this->blockDataMode == $mode) {
			return $mode;
		}
		
		if ($mode) {
			$this->pos = 0;
			$this->end = 0;
			$this->unread = 0;
		} else if ($this->pos < $this->end) {
			throw new \java\lang\IllegalStateException(jstring("unread block data"));
		}
		
		$this->blockDataMode = $mode;
		return !$mode;
	}
	
	public function getBlockDataMode() {
		return $this->blockDataMode;
	}
	
	public function read() {
		if ($this->blockDataMode) {
			if ($this->pos == $this->end) {
				$this->refill();
			}
			//var_dump('$this->end', $this->end, $this->pos, $this->buf[$this->pos++]);
			return ($this->end >= 0) ? ($this->buf[$this->pos++] & 0xFF) : -1;
		} else {
			return $this->input->read();
	    }
	}
	
	private function refill() {
		try {
			do {
				$this->pos = 0;
				if ($this->unread > 0) {
					$n = $this->input->read($this->buf, 0, \java\lang\Math::min($this->unread, self::MAX_BLOCK_SIZE));
					if ($n >= 0) {
						$this->end = $n;
						$this->unread -= $n;
					} else {
						throw new StreamCorruptedException(jstring("unexpected EOF in middle of data block"));
					}
				} else {
					$n = $this->readBlockHeader(true);
					if ($n >= 0) {
						$this->end = 0;
						$this->unread = $n;
					} else {
						$this->end = -1;
						$this->unread = 0;
					}
				}
			} while ($this->pos == $this->end);
		} catch (IOException $ex) {
			$this->pos = 0;
			$this->end = -1;
			$this->unread = 0;
			throw $ex;
		}
	}
	
	private function readBlockHeader($canBlock) {
		if ($this->this0->defaultDataEnd) {
			return -1;
		}
	    try {
			for (;;) {
				$avail = $canBlock ? \java\lang\Integer::MAX_VALUE : $this->input->available();
				if ($avail == 0) {
					return self::HEADER_BLOCKED;
				}
				
				$tc = $this->input->peek();
				switch ($tc) {
					case self::TC_BLOCKDATA:
						if ($avail < 2) {
							return self::HEADER_BLOCKED;
						}
						$this->input->readFully($this->hbuf, 0, 2);
						return $this->hbuf[1] & 0xFF;
						
					case self::TC_BLOCKDATALONG:
						if ($avail < 5) {
							return self::HEADER_BLOCKED;
						}
						$this->input->readFully($this->hbuf, 0, 5);
						$len = Bits::getInt($this->hbuf, 1);
						if ($len < 0) {
							throw new StreamCorruptedException(
								jstring("illegal block data header length: " . $len));
						}
						return $len;

					/*
					 * TC_RESETs may occur in between data blocks.
					 * Unfortunately, this case must be parsed at a lower
					 * level than other typecodes, since primitive data
					 * reads may span data blocks separated by a TC_RESET.
					 */
					case self::TC_RESET:
						$this->input->read();
						$this->this0->handleReset();
						break;

					default:
						if ($tc >= 0 && ($tc < self::TC_BASE || $tc > self::TC_MAX)) {
						throw new StreamCorruptedException(
							\java\lang\String::format("invalid type code: %02X", $tc));
						}
						return -1;
				}
			}
	    } catch (EOFException $ex) {
			throw new StreamCorruptedException(jstring("unexpected EOF while reading block data header"));
	    }
	}
	
	public function readUTF() {
		//return readUTFBody(readUnsignedShort());
		
		//var_dump("BlockInputStream::readUTF");readline();
		$len = $this->readUnsignedShort();
		//var_dump($len); exit;
		if ($len === 0) { return jstring('');}
		return $this->readUTFBody($len);
	}
	
	private function readUTFBody($len) {
		$sb = new \java\lang\StringBuilder();
		
		if (!$this->blockDataMode) {
			$this->end = $this->pos = 0;
		}
		
		while ($len > 0) {
			$avail = $this->end - $this->pos;
			
			if ($avail >= 3 || $avail == $len) {
				$len -= $this->readUTFSpan($sb, $len);
			} else {
				if ($this->blockDataMode) {
					$len -= $this->readUTFSpan($sb, $len);
				} else {
				
					if (avail > 0) {
						\java\lang\System::arraycopy($this->buf, $this->pos, $this->buf, 0, $avail);
					}
					$this->pos = 0;
					$this->end = \java\lang\Math::min(self::MAX_BLOCK_SIZE, $length);
					
					$this->input->readFully($this->buf, $avail, $this->end - $avail);
				}
			}
		}
		
		return $sb->toString();
	}
	
	private function readUTFSpan($stringBuilder, $length) {
		$cpos = 0;
		$start = $this->pos;
		$avail = min($this->end - $this->pos, self::CHAR_BUF_SIZE);
		
		$stop = $this->pos + (($length > $avail) ? $avail - 2 : $length);
		
		$outOfBounds = false;
		
		try {
			while ($this->pos < $stop) {
				$b1 = $this->buf[$this->pos++] & 0xFF;
				switch ($b1 >> 4) {
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:	  // 1 byte format: 0xxxxxxx
						$this->cbuf[$cpos++] = $b1;
						break;

					case 12:
					case 13:  // 2 byte format: 110xxxxx 10xxxxxx
						$b2 = $this->buf[$this->pos++];
						if (($b2 & 0xC0) != 0x80) {
							throw new UTFDataFormatException();
						}
						$this->cbuf[$cpos++] = ((($b1 & 0x1F) << 6) | 
								   (($b2 & 0x3F) << 0));
						break;

					case 14:  // 3 byte format: 1110xxxx 10xxxxxx 10xxxxxx
						$b3 = $this->buf[$this->pos + 1];
						$b2 = $this->buf[$this->pos + 0];
						$this->pos += 2;
						if (($b2 & 0xC0) != 0x80 || ($b3 & 0xC0) != 0x80) {
							throw new UTFDataFormatException();
						}
						$this->cbuf[$cpos++] = ((($b1 & 0x0F) << 12) | 
								   (($b2 & 0x3F) << 6) | 
								   (($b3 & 0x3F) << 0));
						break;

					default:  // 10xx xxxx, 1111 xxxx
						throw new UTFDataFormatException();
				}
			}
		} catch (\RuntimeException $e) {
			$outOfBounds = true;
		} finally {
			if ($outOfBounds || ($this->pos - $start) > $length) {
				/*
				 * Fix for 4450867: if a malformed utf char causes the
				 * conversion loop to scan past the expected end of the utf
				 * string, only consume the expected number of utf bytes.
				 */
				$this->pos = $start + $length;
				throw new UTFDataFormatException();
			}
		}
		$stringBuilder->append($this->cbuf, 0, $cpos);
		//var_dump($stringBuilder); exit;
		return $this->pos - $start;
	}
	
	private function readUnsignedShort() {
		if (!$this->blockDataMode) {
			$this->pos = 0;
			$this->input->readFully($this->buf, 0, 2);
		} else if ($this->end - $this->pos < 2) {
			//var_dump("ObjectInputStream::readUnsignedShort");readline();
			return $this->din->readUnsignedShort();
		}
		$v = Bits::getShort($this->buf, $this->pos);
		$this->pos += 2;
		return $v;
	}
}

CODE
) !== false or exit;

//java/io/Bits
evalLazy('java/io/Bits', <<<'CODE'

namespace java\io;
	
class Bits extends \java\lang\Object {
	
	public static function getShort(\SplFixedArray $b, $off) {
		return ((($b[$off + 1] & 0xFF) << 0) + 
				(($b[$off + 0]) << 8));
	}
	
	public static function getInt(\SplFixedArray $b, $off) {
		return (($b[$off + 3] & 0xFF) << 0) +
			   (($b[$off + 2] & 0xFF) << 8) +
			   (($b[$off + 1] & 0xFF) << 16) +
			   (($b[$off + 0]) << 24);
	}
}

CODE
) !== false or exit;

//java/sql/DriverManager
evalLazy('java/sql/DriverManager1', <<<'CODE'

namespace java\sql;
	
class DriverManager1 extends \java\lang\Object {
	
	private static $logWriter;
	private static $drivers = [];
	
	public static function registerDriver($driver)  {
		self::$drivers[] = $driver;
	}
	
	public static function deregisterDriver($driver) {
		$index = array_search($driver, self::$drivers);
		if ($index !== false) {
			self::$drivers[$index] = null;
			self::$drivers = array_filter(self::$drivers);
		}
	}
	
	public static function getConnection(/*String*/ $url, /*Properties or String user*/ $info = null, /*String*/ $password = null) {
		
		if ($info === null) {
			if (class_exists('java\util\Properties', false) == false) {
				eval(<<<'EVAL'
					namespace java\util;
					
					class Properties extends HashMap {
						public function getProperty($name) {
							return $this->get($name);
						}
						public function setProperty($name, $value) {
							$this->put($name, $value);
						}
					}
EVAL
) !== false or exit;
			}
			$info = new \java\util\Properties();
		}
		return self::getDriver($url)->connect($url, $info);
	}
	
	public static function getDriver($url) {
		foreach (self::$drivers as $driver) {
			if ($driver->acceptsURL($url)) {
				return $driver;
			}
		}
		return null;
	}
	
	public static function /*Enumeration<Driver>*/ getDrivers()  {}
	
	public static function getLoginTimeout() {}
	
	public static function setLoginTimeout($seconds) {}
	
	public static function getLogStream() {}
	
	public static function setLogStream($out) {}
	
	public static function getLogWriter() {
		return self::$logWriter;
	}
	
	public static function setLogWriter($out) {
		self::$logWriter = $out;
	}
	
	public static function println($message) {}
}

CODE
) !== false or exit;

//java/net/URLEncoder
evalLazy('java/net/URLEncoder', <<<'CODE'
namespace java\net;

class URLEncoder extends \java\lang\Object {
	
	public static function encode($string, $encode) {
		return rawurlencode($string);
	}
}
CODE
) !== false or exit;

//java/nio/charset/Charset
evalLazy('java/nio/charset/Charset1', <<<'CODE'
namespace java\nio\charset;

class Chars Charset1 extends \java\lang\Object {
	
	private $name;
	
	private static $defaultCharset;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	public static function defaultCharset() {
		if (self::$defaultCharset === null) {
			self::$defaultCharset = Charset::forName(jstring("UTF-8"));
		}
		return self::$defaultCharset;
	}
	
	public function displayName() {
		return $this->name;
	}
	
	public static function isSupported($name) {
		return "UTF-8" == "$name";
	}
	
	public function name() {
		return $this->name;
	}
	
	public static function forName($name) {
		//class sun.nio.cs.UTF_8
		//$charset = new Charset(new \java\lang\String($name));
		$name = 'sun.nio.cs.'.str_replace('-', '_', "$name");
		//var_dump($name);exit;
		$charset = \java\lang\Clazz::forName($name)->newInstance();
		var_dump($charset);exit;
		return $charset;
	}
}

CODE
) !== false or exit;

//javax/swing/JFrame
evalLazy('javax/swing/JFrame1', <<<'CODE'

namespace javax\swing;
	
class JFrame1 extends \java\lang\Object {
	
}

CODE
) !== false or exit;
