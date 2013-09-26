<?php

//java/lang/Object
eval(<<<'CODE'

namespace java\lang;
	
class Object {
	public function __construct() {}
	
	public function toString() {
		$str = new String($this->getClass()->getName() . '@'.\dechex(System::identityHashCode($this)));
		return $str->replace('\\', '.');
	}
	
	public function hashCode() {
		return System::identityHashCode($this);
	}
	
	public function getClass() {
		return Clazz::forName(str_replace('\\', '.', str_replace('_S_', '$', get_class($this))));
	}
	
	public function __call($method, $args) {
		$fname = $method;
		if ($method == '<init>') {
			$fname = '__construct';
		} else if ($method == 'clone') {
			$fname = '_clone';
		} else if ($method == 'empty') {
			$fname = '__empty';
		} else if ($method == 'echo') {
			$fname = '__echo';
		} else if ($method == 'print') {
			$fname = '__print';
		} else if ($method == 'exit') {
			$fname = '__exit';
		} else if ($method == 'list') {
			$fname = '__list';
		}
		if ($fname != $method) {
			return call_user_func_array([$this, $fname], $args);
		} else {
			throw new \Exception($method.' method does not exists');
		}
	}

	public static function __callstatic($method, $args) {
		$fname = $method;
		if ($method == '<init>') {
			$fname = '__construct';
		} else if ($method == 'clone') {
			$fname = '_clone';
		} else if ($method == 'empty') {
			$fname = '__empty';
		} else if ($method == 'echo') {
			$fname = '__echo';
		} else if ($method == 'print') {
			$fname = '__print';
		} else if ($method == 'exit') {
			$fname = '__exit';
		} else if ($method == 'list') {
			$fname = '__list';
		}
		if ($fname != $method) {
			return call_user_func_array([get_class($this), $fname], $args);
		} else {
			throw new \Exception($method.' static method do not exists');
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
}


CODE
) !== false or exit;

//java/lang/Class
eval(<<<'CODE'

namespace java\lang;
	
class Clazz extends Object {
	
	private $name;
	private $refClass;
	
	public function __construct($name) {
		$this->name = "$name";
		
		//teste para tipos primitivos
		if ($name !== strtolower($name)) {
			$this->getRefClass();
		}
	}
	
	private function getRefClass() {
		if ($this->refClass === null) {
			$this->refClass = new \ReflectionClass(str_replace('.', '\\', str_replace('$', '_S_', $this->name)));
		}
		return $this->refClass;
	}
	
	public function getName() {
		return new String($this->name);
	}
	
	public static function forName($name) {
		$cls = new Clazz($name);
		return $cls->intern();
	}
	
	public function newInstance() {
		return $this->getRefClass()->newInstance();
	}
	
	public function intern() {
		return $this;
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
	
	public static function getPrimitiveClass($type) {
		return Clazz::forName($type);
	}
	
	public function toString() {
		return new String("class " . $this->getName()->replace("\\", "."));
	}
	
	public function getClassLoader() {
		try {
			return $this->getRefClass()
						->getProperty('javaClass')
						->getValue()
						->getClassLoader();
		} catch(\Exception $e) {
			return null;
		}
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
	
}
CODE
) !== false or exit;
class_alias('java\lang\Clazz', 'java\lang\Class');

//java/lang/System
eval(<<<'CODE'

namespace java\lang;
	
class System extends Object {
	public static $out;
	public static $in;
	
	public static $native_classes = [];
	
	public static function currentTimeMillis() {
		//var_dump((microtime(true) * 1000));
		//var_dump(intval(microtime(true) * 1000));
		return intval(microtime(true) * 1000);
	}
	
	public static function identityHashCode(Object $obj) {
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
	
	public static function loadLibrary($lib) {
		include_once "$lib.php";
	}
}


CODE
) !== false or exit;

//java/io/PrintStream
eval(<<<'CODE'

namespace java\io;
	
class PrintStream extends \java\lang\Object {
	
	private $writer;
	
	public function __construct($writer = null) {
		if ($writer !== null) {
			$this->writer = $writer;
		}
	}
	
	public function __call($func, $args) {
		//var_dump($args);readline();
		$writer = $this->writer;
		if ($func == 'print') {
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
		} else {
			parent::__call($func, $args);
		}
	}
	public function println($s = '') {
		//var_dump($s);
		$this->print($s, PHP_EOL);
	}
	
	public function close() {}
}
\java\lang\System::$out = new \java\io\PrintStream();

CODE
) !== false or exit;

//java/io/PrintStream
eval(<<<'CODE'

namespace java\io;
	
class InputStream extends \java\lang\Object {
	public function nextLine() {
		return new \java\lang\String(trim(fgets(STDIN)));
	}
	
	public function hasNextLine() {
		return true;
	}
}
\java\lang\System::$in = new \java\io\InputStream();

CODE
) !== false or exit;

//java/util/Scanner
eval(<<<'CODE'

namespace java\util;
	
class Scanner extends \java\lang\Object {
	
	private $stream;
	
	public function __construct($stream) {
		if ($stream === null) {throw new \Exeption('null pointer');}
		//var_dump($stream);exit;
		$this->stream = $stream;
	}
	
	public function nextLine() {
		return $this->stream->nextLine();
	}
	
	public function hasNextLine() {
		return $this->stream->hasNextLine();
	}
}

CODE
) !== false or exit;


//java/lang/Character
eval(<<<'CODE'

namespace java\io;
	
class File extends \java\lang\Object {
	
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
	
}

CODE
) !== false or exit;


//java/lang/Character
eval(<<<'CODE'

namespace java\lang;
	
class Character extends \java\lang\Object {
	
}

CODE
) !== false or exit;


//java/lang/String
eval(<<<'CODE'

namespace java\lang;
	
class String extends \java\lang\Object {
	
	private $string;
	
	private $hash = 0;
	
	public function __construct($string = '') {
		if (is_array($string) || $string instanceof \SplFixedArray) {
			$this->string = '';
			foreach ($string as $c) {
				$this->string .= chr($c);
			}
		} else {
			$this->string = "$string";
		}
	}
	
	public function indexOf($str, $fromIndex = 0) {
		if (is_int($str)) {
			$str = chr($str);
		}
		$pos =  strpos($this->string, "$str", +"$fromIndex");
		return $pos !== false ? $pos : -1;
	}
	
	public function split($delimiter, $limit = 0) {
		if ($limit > 0) {
			$ar = explode("$delimiter", $this->string, $limit);
		} else {
			$ar = explode("$delimiter", $this->string);
		}
		if ($limit == 0) {
			while (end($ar) == '') {
				array_pop($ar);
			}
		}
		foreach ($ar as $i => $item) {
			$ar[$i] = new String($ar[$i]);
		}
		return $ar;
	}
	
	public function substring($start, $end) {
		return new self(substr($this->string, $start, $end - $start));
	}
	
	public function replace($from, $to) {
		return new self(str_replace("$from", "$to", $this->string));
	}
	
	public function replaceAll($from, $to) {
		return new self(str_replace("$from", "$to", $this->string));
	}
	
	public function trim() {
		return new self(trim($this->string));
	}
	
	public function toLowerCase() {
		return new self(strtolower($this->string));
	}
	
	public function toUpperCase() {
		return new self(strtoupper($this->string));
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
		return \SplFixedArray::fromArray($ar_chr);
	}
	
	public function charAt($i) {
		return ord($this->string[$i]);
	}
	
	public function startsWith($str) {
		$str = "$str";
		return substr($this->string, 0, strlen($str)) == $str;
	}
	
	public function equals($o) {
		if ($o instanceof self) {
			return $this->string == $o->string;
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
	
	public static function valueOf($o) {
		return new String("$o");
	}
	
	public function intern() {
		return $this;
	}
	
	public function toString() {
		return $this;
	}
	public function __toString() {
		return (string) $this->string;
	}
}

CODE
) !== false or exit;

//java/lang/StringBuilder
eval(<<<'CODE'

namespace java\lang;
	
class StringBuilder extends \java\lang\Object {
	
	private $string;
	
	public function __construct($string = '') {
		$this->string = $string;
	}
	public function append($s) {
		//var_dump($s);
		$this->string .= $s;
		return $this;
	}
	
	public function length() {
		return strlen($this->string);
	}

	public function reverse() {
		$this->string = strrev($this->string);
		return $this;
	}
	
	public function toString() {
		return new String($this->string);
	}
}

class StringBuffer extends StringBuilder {}
CODE
) !== false or exit;


//java/util/StringTokenizer
eval(<<<'CODE'

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
eval(<<<'CODE'

namespace java\util;
	
class ArrayList extends \java\lang\Object {
	
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

class Arrays extends \java\lang\Object {
	public static function asList($array) {
		return new ArrayList($array->toArray());
	}
}

CODE
) !== false or exit;

//java/util/Vector
eval(<<<'CODE'

namespace java\util;
	
class Vector extends ArrayList {
}

CODE
) !== false or exit;

//java/lang/Random
eval(<<<'CODE'

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

//java/util/HashMap
eval(<<<'CODE'

namespace java\util;
	
class HashMap extends ArrayList {

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
eval(<<<'CODE'

namespace java\util;
	
class HashSet extends ArrayList {
	
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
eval(<<<'CODE'

namespace java\util;
	
class HashIterator extends \ArrayIterator {
	
	
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
eval(<<<'CODE'

namespace java\util;
	
class Hashtable extends HashMap {
	
}
CODE
) !== false or exit;

//java/util/WeakHashMap
eval(<<<'CODE'

namespace java\util;
	
class WeakHashMap extends HashMap {
	
}
CODE
) !== false or exit;

//java\util\Properties
eval(<<<'CODE'

namespace java\util;
	
class Properties extends Hashtable {
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
eval(<<<'CODE'

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
eval(<<<'CODE'

namespace java\lang;
	
class Number extends Object {

	public static $TYPE;
	
	protected $v;
	
	public function __construct($v) {
		if (!is_numeric("$v")) {
			throw new \java\lang\NumberFormatException("For input string: \"$v\"");
		}
		$this->v = +"$v";
	}
	
	public function intValue() {
		return (int) $this->v;
	}
	
	public function longValue() {
		return +$this->v;
	}
	
	public function doubleValue() {
		return +$this->v;
	}
	
	public function floatValue() {
		return $this->doubleValue();
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
eval(<<<'CODE'

namespace java\lang;
	
class Integer extends Number {
	
	public static $TYPE;

	public static function parseInt($in) {
		return intval($in.'');
	}

	public static function valueOf($v) {
		return new self($v);
	}
}
Integer::$TYPE = Clazz::getPrimitiveClass('int');

CODE
) !== false or exit;

//java/lang/Byte
eval(<<<'CODE'

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
Byte::$TYPE = Clazz::getPrimitiveClass('byte');

CODE
) !== false or exit;

//java/lang/Double
eval(<<<'CODE'

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
Double::$TYPE = Clazz::getPrimitiveClass('double');

CODE
) !== false or exit;

//java/lang/Float
eval(<<<'CODE'

namespace java\lang;
	
class Float extends Number {
	
	public static $TYPE;
	
	public static function floatToIntBits($f) {
		return $f | 0;
	}
	
	public static function valueOf($v) {
		return new self($v);
	}
}
Float::$TYPE = Clazz::getPrimitiveClass('float');

CODE
) !== false or exit;


//java/lang/Long
eval(<<<'CODE'

namespace java\lang;
	
class Long extends Number {
	
	public static $TYPE;
	
	public static function valueOf($v) {
		return new self($v);
	}
}
Long::$TYPE = Clazz::getPrimitiveClass('long');

CODE
) !== false or exit;

//java/math/BigInteger
eval(<<<'CODE'

namespace java\math;
	
class BigInteger extends \java\lang\Number {
	
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
eval(<<<'CODE'

namespace java\lang;
	
class Boolean extends \java\lang\Object {
	
	private $v;
	
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
	
	public function toString() {
		return new String($this->v ? "true" : "false");
	}
}

CODE
) !== false or exit;

//java/lang/Exception
eval(<<<'CODE'

namespace java\lang;
	
//class Exception extends \Exception {}
class Throwable extends \Exception {
	public function __construct($message = '', $code = 0, Exception $previous = NULL) {
		parent::__construct($message, $code, $previous);
		$this->message = new \java\lang\String($message);
	}
}

CODE
) !== false or exit;

//java/lang/Thread
eval(<<<'CODE'

namespace java\lang;
	
class Thread extends Object {
	
	/* java\lang\Thread[] */
	private static $threads;
	
	/* long */
	public static $currentThreadId = 0;
	
	/* long */
	private static $incrementThreadId = 0; 
	
	/* long */
	public $tid;
	
	/* java\lang\ClassLoader */ 
	private $contextClassLoader;
	
	private $nativeThread;
	
	private $runnable;

	public function __construct($runnable = null) {
		$this->runnable = $runnable;
		$this->tid = self::$incrementThreadId++;
		
		self::$threads[$this->tid] = $this;
	}
	
	public function start() {
		//var_dump($this->runnable); exit;
		//$this->nativeThread = new \PhpThread([$this->runnable, 'run'], [], $this);		
		//$this->nativeThread->start();
		$this->runnable->run();
	}
	
	/* long */
	public function getId() {
		return $this->tid;
	}
	
	/* long */
	public static function sleep($milisecs) {
		usleep($milisecs * 1000);
	}
	
	/* java\lang\Thread */
	public static function currentThread() {
		if (self::$threads[self::$currentThreadId] === null) {
			$thread = new Thread();
			self::$currentThreadId = $thread->tid;
		}
		return self::$threads[self::$currentThreadId];
	}
	
	/* void */
	public function setContextClassLoader(\java\lang\ClassLoader $loader) {
		$this->contextClassLoader = $loader;
	}
	
	/* java\lang\ClassLoader */
	public function getContextClassLoader() {
		if ($this->contextClassLoader === null) {
			$this->contextClassLoader = ClassLoader::getSystemClassLoader();
		}
		return $this->contextClassLoader;
	}
}

CODE
) !== false or exit;

//java/sql/DriverManager
eval(<<<'CODE'

namespace java\sql;
	
class DriverManager extends \java\lang\Object {
	
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
	
	public static function getConnection(/*String*/ $url, /*Properties or String user*/ $info, /*String*/ $password) {
		
		if (false && !$info instanceof \java\util\Properties) {
			
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

//java.net.URLEncoder
eval(<<<'CODE'
namespace java\net;

class URLEncoder extends \java\lang\Object {
	
	public static function encode($string, $encode) {
		return rawurlencode($string);
	}
}
CODE
) !== false or exit;

//java.nio.charset.Charset
eval(<<<'CODE'
namespace java\nio\charset;

class Charset extends \java\lang\Object {
	
	private $name;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	public static function defaultCharset() {
		return new Charset(new \java\lang\String("utf8"));
	}
	
	public function displayName() {
		return $this->name;
	}
}

CODE
) !== false or exit;

//javax/swing/JFrame
eval(<<<'CODE'

namespace javax\swing;
	
class JFrame1 extends \java\lang\Object {
	
}

CODE
) !== false or exit;
