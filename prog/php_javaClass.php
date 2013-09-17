<?php
include 'JavaMock.php';
include 'JavaLoader.php';
include 'DataInputStream.php';
include 'JavaInterpreter.php';
include 'JavaPhpCompiler.php';
include 'PhpThread.php';

//spl_autoload_register([\java\lang\ClassLoader::getSystemClassLoader(), 'loadClass']);
spl_autoload_register(function($className){

	/* java\lang\ClassLoader */
	$classLoader = \java\lang\Thread::currentThread()->getContextClassLoader();
	
	if ($classLoader === null) {
		var_dump($className);
	}
	
	//$classLoader->setDefaultAssertionStatus(true);
	$className = str_replace('_S_', '$', $className);
	$classLoader->loadClass($className);
});

class php_javaClass extends \java\lang\Object {
	use \JavaInterpreter;
	use \JavaPhpCompiler;
	
	//const FILE_NAME = "ClassReader.class";
	//const FILE_NAME = "Teste.class";
	//const FILE_NAME = "codecs\$py.class";
	//const FILE_NAME = "CampoMinado.class";
	
	#DataInputStream
	private $input;
	
	private $constant_pool = array();
	
	private $class_attr = array();
	
	private $classLoader = null;
	
	public function __construct($class_name = null) {
		if ($class_name !== null) {
			$this->readClass("$class_name.class");
		}
	}
	
	public function __destruct() {}
	
	public function setClassLoader(\java\lang\ClassLoader $loader) {
		$this->classLoader = $loader;
	}
	
	public function getClassLoader() {
		return $this->classLoader;
	}
	
	public function readClass($file) {
		
		ob_start();
		
		if (!is_resource($file)) {
			$file = fopen($file, 'rb');
		}
		$this->input = $input = new \DataInputStream($file);
		
		if (dechex($input->readInt()) != dechex(0xCAFEBABE)) {
			echo "not a java class!";
			exit;
		}
		println("minor version: " . $input->readShort());
		println("major version: " . $input->readShort());
		
		$constant_pool_count = $input->readShort();
		println("constant_pool_count = " . $constant_pool_count);
		
		for ($i = 1; $i < $constant_pool_count; $i++) {
			$byte_tag = $input->readByte();
			print($i . ": tag=" . $byte_tag . ": ");
			
			switch ($byte_tag) {

				//??? Asciz?
				case 0:
					/*
					$size0 = $input->readInt()
					byte[] str0 = new byte[size0];
					input.readFully(str0);
					System.out.print("Asciz: " + new String(str0));
					break;*/
				
				//utf8
				case 1:
					$utf = $input->readUTF();
					$this->constant_pool[$i] = ['utf8', $utf]; 
					print("utf8 string: " . $utf);
					break;
			
				//int
				case 3:
					$intg = $input->readInt();
					$this->constant_pool[$i] = ['int', $intg];
					print("int: " . $intg);
					break;
				
				//float
				case 4:
					$floatNum = $input->readFloat();
					$this->constant_pool[$i] = ['float', $floatNum];
					
					//uses 2 indexes
					print("float: " . $floatNum);
					//exit;
					break;

				//long
				case 5:
					$longNum = $input->readLong();
					$this->constant_pool[$i] = ['long', $longNum];
					
					//uses 2 indexes
					$i++;
					print("long: " . $longNum);
					break;
				
				//double
				case 6:
					$doubleNum = $input->readDouble();
					$this->constant_pool[$i] = ['double', $doubleNum];
					
					//uses 2 indexes
					$i++;
					print("double: " . $doubleNum);
					//exit;
					break;

				//Class
				case 7:
					$shortNum = $input->readShort();
					$this->constant_pool[$i] = ['Class', $shortNum]; 
					print("Class: utf8 index=" . $shortNum);
					break;

				//String
				case 8:
					$strIndex = $input->readShort();
					$this->constant_pool[$i] = ['String', $strIndex];
					print("String: utf8 index=" . $strIndex);
					break;
				
				//field ref
				case 9:
					$fieldRef = [$input->readShort(), $input->readShort()];
					$this->constant_pool[$i] = ['field', $fieldRef]; 
					print("field: Class index=" . $fieldRef[0]);
					print(", Name and Type index=" . $fieldRef[1]);
					break;
				
				//method ref
				case 10:
					$methRef = [$input->readShort(), $input->readShort()];
					$this->constant_pool[$i] = ['method', $methRef];
					print("method: Class index=" . $methRef[0]);
					print(", Name and Type index=" . $methRef[1]);
					break;

				//interface
				case 11:
					$interfRef = [$input->readShort(), $input->readShort()];
					$this->constant_pool[$i] = ['interface', $interfRef];
					print("interface: Class index=" . $interfRef[0]);
					print(", Name and Type index=" . $interfRef[1]);
					break;

				//name and type descriptor
				case 12:
					$descRef = [$input->readShort(), $input->readShort()];
					$this->constant_pool[$i] = ['name and type', $descRef];
					print("name and type descriptor: name index=" . $descRef[0]);
					print(", type index=" . $descRef[1]);
					break;
					
				default:
					println("unknown byte_tag");
					exit();
					return;
			}
			println();
		}
		
		$flag = $input->readShort();
		print("flag " . $flag);
		$this->class_attr['flags'] = $this->flags($flag);
		println($this->class_attr['flags']);
		
		$this->class_attr['name'] = $this->getClassName($input->readShort());
		println("this class: " .$this->class_attr['name']);
		
		$this->class_attr['super'] = $this->getClassName($input->readShort());
		println("super class: " . $this->class_attr['super']);
		
		$interfaces_count = $input->readShort();
		println("interfaces: " . $interfaces_count);
		
		$this->class_attr['interfaces'] = [];
		for ($i = 0; $i < $interfaces_count; $i++) {
			$interface = $this->getClassName($input->readShort());
			$this->class_attr['interfaces'][] = $interface;
			println(($i+1) . ": " . $interface);
		}

		$number_of_fields = $input->readShort();
		println("fields: " . $number_of_fields);
		
		$this->class_attr['fields'] = [];
		for ($i = 0; $i < $number_of_fields; $i++) {
			$field = [];
			$field['flags'] = $this->flags($input->readShort());
			println($field['flags']);
			
			$field['name'] = $this->getString($input->readShort());
			println("field name: " . $field['name']);
			
			$field['type'] = $this->getString($input->readShort());
			println("field type: " . $field['type']);
			
			$attr_count = $input->readShort();
			println("attr count: " . $attr_count);
			$field['attr'] = $this->attr($attr_count);
			$this->class_attr['fields'][] = $field;
		}
		
		$methods = $input->readShort();
		println("methods: " . $methods);
		
		$this->class_attr['methods'] = [];
		for ($i = 0; $i < $methods; $i++) {
			$method = [];
			
			$method['flags'] = $this->flags($input->readShort());
			println($method['flags']);
			
			$method['name'] = $this->getString($input->readShort());
			println("method name: " . $method['name']);
			
			$method['type'] = $this->getString($input->readShort());
			println("method type: " . $method['type']);
			
			$attr_count = $input->readShort();
			println("attr count: " . $attr_count);
			$method['attr'] = $this->attr($attr_count);
			
			$this->class_attr['methods'][] = $method;
		}

		
		$attr_count = $input->readShort();
		println("\nclass attr count: " . $attr_count);
		$this->attr($attr_count);
		
		//print_r($this->class_attr);
		println('the end?');
		ob_end_clean();
		
		$this->createPhpClass();
	}
	
	public function getPhpClassName() {
		return str_replace('/', '\\', str_replace('$', '_S_', $this->class_attr['name']));
	}
	
	public function createPhpClass() {
	
		$className = str_replace('$', '_S_', $this->class_attr['name']);
		
		$super = '\\'.str_replace('/', '\\', str_replace('$', '_S_', $this->class_attr['super']));
		
		$fields = '';
		//$dump = 'var_dump(';
		foreach ($this->class_attr['fields'] as $field) {
			//var_dump($field); exit;
			if (strpos($field['flags'], 'static') !== false) {
				$fields .= 'public static $'.str_replace('$', '_S_', $field['name']).';'.PHP_EOL;
			} else {
				$fields .= 'public $'.str_replace('$', '_S_', $field['name']).';'.PHP_EOL;
			}
			//$dump .= '"'.$field['name'].'",';
			//$dump .= $className.'::$'.$field['name'].',';
		}
		//$dump .= 'null);';
		//var_dump($dump);exit;
		
		//var_dump($this->class_attr['methods']);
		$methods = '';
		$arfnums = [];
		foreach ($this->class_attr['methods'] as $m) {
			//var_dump($m);readline();
			$fname = $m['name'];
			if (isset($arfnums[strtolower($fname)]) || $fname == '<clinit>') {
				continue;
			}
			
			$arfnums[strtolower($fname)] = true;
			
			if ($fname == '<init>') {
				$fname = '__construct';
			} else if ($fname == 'clone') {
				$fname = '_clone';
			} else if ($fname == 'empty') {
				$fname = '__empty';
			} else if ($fname == 'echo') {
				$fname = '__echo';
			} else if ($fname == 'print') {
				$fname = '__print';
			} else if ($fname == 'exit') {
				$fname = '__exit';
			} else if ($fname == 'list') {
				$fname = '__list';
			} else {
				$fname = str_replace('$', '_S_', $fname);
			}
			//var_dump($className, $fname, $m['flags']);
			if (strpos($m['flags'], 'static') !== false) {
				if ($fname == 'toString') {
					$fname = 'staticToString';//bug?
				}
				$methods .= 'public static function '.$fname.'() {
					$args = func_get_args();
					return self::$javaClass->run("'.$m['name'].'", $args);
				}'.PHP_EOL;
			} else {
				$methods .= 'public function '.$fname.'() {
					$args = func_get_args();
					if (self::$javaClass) {
						//
					}
					return self::$javaClass->run("'.$m['name'].'", $args, $this);
				}'.PHP_EOL;
			}
		}
		$namespace = str_replace('/', '\\', dirname($className));
		$namespace = $namespace != '.' ? "namespace $namespace;" : '';
		$className = basename($className);
		if ($className == 'Class') {
			$className = '__Class';
		}
		$clinit = '';
		try {
			$this->findMethod('<clinit>');
			$clinit = "$className::__callstatic('<clinit>', []);";
		} catch (\Exception $e) {
			//
		}
		
		$eval = eval($s=<<<CODE
		$namespace
		class $className extends $super {
			public static \$javaClass;
			public static function __callstatic(\$method, \$args) {
				try {
					return self::\$javaClass->run(\$method, \$args);
				} catch(\\Exception \$e) {
					return parent::\$javaClass->run(\$method, \$args);
				}
			}
			
			public function __call(\$method, \$args) {
				try {
					return self::\$javaClass->run(\$method, \$args, \$this);
				} catch(\\Exception \$e) {
					return parent::\$javaClass->run(\$method, \$args, \$this);
				}
			}
			$fields
			
			$methods
		}
		$className::\$javaClass = \$this;
		
		$clinit
CODE
);
		//echo ($s);readline();
		echo explode(PHP_EOL, $s)[33];
		if ($eval === false) {
			echo explode(PHP_EOL, $s)[27];
			exit;
		}
		$className = str_replace('/', '\\', $this->class_attr['name']);
		if ($this->getPhpClassName() != $className) {
			class_alias($this->getPhpClassName(), $className);
		}
	}
	
	public function run($method_name, $args = [], $thisObj = null) {
		$method = $this->findMethod($method_name, $args);
		
		if (strpos($method['flags'], 'native') !== false) {
			$native_classes = \java\lang\System::$native_classes;
			//var_dump($native_classes[$this->getPhpClassName()]);
			foreach ((array)$native_classes[$this->getPhpClassName()] as $cls) {
				$cls = "$cls";
				if(method_exists($cls, $method_name)) {
					if (!property_exists($thisObj, 'php_objs')) {
						$thisObj->php_objs = [];
					}
					
					if (empty($thisObj->php_objs[$cls])) {
						$reflect = new ReflectionClass($cls);
						$thisObj->php_objs[$cls] = $reflect->newInstanceWithoutConstructor();
					}
					
					//$thisObj->php_objs["$cls"]->$method_name
					return call_user_func_array([$thisObj->php_objs[$cls], $method_name], $args);
				}
			}
			//exit;
			//return;
		}
		
		if (empty($method['attr']['Code'])) {
			var_dump($this->getPhpClassName());
			var_dump($method);
			//exit;
			return;//?? java native?
		}
		if ($thisObj !== null) {
			$args = array_merge([$thisObj], $args);
		}
		return $this->runCode($method['attr']['Code'], $args, $this->class_attr['name'].'::'.$method_name);
	}
	
	private function & compileAndRun($code, $args = [], $method = '') {
		$method = str_replace('::', '__', $method);
		$method = str_replace('\\', '_', $method);
		$method = 'java_' . $method;
		
		if (function_exists($method)) {
			return $method($args, $this);
		}
		$func_code = ["function & $method(\$locals, \$thisObj) {"];
		$func_code[] = '$stack = [];';
		foreach ($code['instructions'] as $i => $opcode) {
			//var_dump($opcode);
			$code = $this->compileOpCode($i, $opcode, $method);
			if (!$code) {
				exit;
			}
			//echo $code, PHP_EOL;
			$func_code[] = $method.'_L'.$i.': '. $code;
		}
		$func_code[] = '}';
		file_put_contents("$method.php", "<?php\n".implode(PHP_EOL, $func_code));
		eval(implode(PHP_EOL, $func_code));
		return $method($args, $this);
	}
	
	private function runCode($code, $args = [], $method = '') {
		//var_dump($code);exit;
		//return $this->compileAndRun($code, $args, $method);
		
		$instructions = $code['instructions'];
		
		if (!is_array($instructions)) {
			var_dump($method);exit;
		}
		
		$last_index = max(array_keys($instructions));
		
		$stack = [];
		$locals = $args;
		//var_dump($instructions);exit;
		for ($i = 0;$i !== null && $i <= $last_index;) {
			$opcode = $instructions[$i];
			//var_dump($opcode);exit;
			try {
				$this->interpreter($i, $opcode, $stack, $locals, $method);
			} catch (\Exception $e) {
				foreach ($code['exceptions'] as $exception) {
					if (is_a($e, str_replace('/', '\\', '\\'.$exception['catch_type']))) {
						//var_dump($exception);exit;
						$i = $exception['handler_pc'];
						$stack = [$e];
						continue 2;
					}
				}
				//echo "OPS".PHP_EOL; echo get_class($e);
				throw $e;
			}
		}
		return end($stack);
	}
	
	
	private function findMethod($name, &$args = []) {
		if ($name == '__construct') {
			$name = '<init>';
		}
		$countArgs = count($args);
		foreach ($this->class_attr['methods'] as $method) {
			//echo $method['name'] . PHP_EOL;
			if ($method['name'] == $name 
			
			//dynamic dispach emulation by args count
			&& count($this->getArgsType($method['type'])['args']) == $countArgs) {
				//var_dump($this->getArgsType($method['type']));exit;
				foreach ($this->getArgsType($method['type'])['args'] as $i => $arg) {
					//var_dump($arg);exit;
					if ($arg == 'java/lang/String' && !($args[$i] instanceof \java\lang\String)) {
						$args[$i] = new \java\lang\String($args[$i]);
						$args[$i] = $args[$i]->intern();
					}
				}
				return $method;
			}
		}
		throw new \Exception('method not found!');
		
		foreach ($this->class_attr['methods'] as $method) {
			var_dump($method['name']);
			if ($method['name'] == $name) {
				var_dump($this->getArgsType($method['type']));
			}
		}
		if ($name != '<clinit>') {
			var_dump([$name, $countArgs]);exit;
		}
	}
	
	private function attr($count) {
		$attrs = [];
		for ($i = 0; $i < $count; $i++) {
			//System.out.println("attribute name index: " + input.readShort());
			//ConstantValue
			$attr_name = $this->getString($this->input->readShort());
			println("  attribute $i name: " . $attr_name);
			
			$attr_length = $this->input->readInt();
			if ($attr_name == 'ConstantValue') {
				$attrs[$attr_name] = $this->getDataFromRef($this->input->readShort());
				println("  attribute $i value: " . $attrs[$attr_name]);
			} else if ($attr_name == 'SourceFile') {
				$attrs[$attr_name] = $this->getString($this->input->readShort());
				println("  attribute $i value: " . $attrs[$attr_name]);
			} else if ($attr_name == 'Signature') {
				$attrs[$attr_name] = $this->getString($this->input->readShort());
				println("  attribute $i value: " . $attrs[$attr_name]);
			} else if ($attr_name == 'Code') {
				$attrs[$attr_name] = $this->getCodeAttr($attr_length);
			} else {
				$attrs[$attr_name] = '(?)length '.$attr_length;
				println("  attribute $i length: " . $attr_length);
				$this->input->skipBytes($attr_length);
			}
			println();
		}
		return $attrs;
	}
	
	private function getCodeAttr($attr_length) {
		$input = $this->input;
		
		$code = [];
		
		//println("\n  code length: " . $attr_length);
		
		$code['max_stack'] = $input->readShort();
		println("  max stack: " . $code['max_stack']);
		
		$code['max_locals'] = $input->readShort();
		println("  max locals: " . $code['max_locals']);
		
		$code_length = $input->readInt();
		println("  code length: " . $code_length);
		$code['instructions'] = $this->readCode($code_length);
		
		$exceptions_length = $input->readShort();
		println("  exceptions length: " . $exceptions_length);
		$code['exceptions'] = [];
		for ($i = 0; $i < $exceptions_length; $i++) {
			$exception = [];
			
			$exception['start_pc'] = $input->readShort();
			println("    start_pc: " . $exception['start_pc']);
			
			$exception['end_pc'] = $input->readShort();
			println("    end_pc: " . $exception['end_pc']);
			
			$exception['handler_pc'] = $input->readShort();
			println("    handler_pc: " . $exception['handler_pc']);
			
			$exceptionClass = $input->readShort();
			$exception['catch_type'] = $exceptionClass != 0 ? $this->getClassName($exceptionClass) : 'Exception';
			println("    catch_type: " . $exception['catch_type']);
			
			$code['exceptions'][] = $exception;
		}
		
		$code_attr_count = $input->readShort();
		println("  code atributes count: " . $code_attr_count);
		$code['attr'] = $this->attr($code_attr_count);
		
		return $code;
	}
	
	private function readCode($len) {
		$instructions = [];
		for ($i = 0; $i < $len; $i++) {
			$lin = $i;
			$hexOpcode = $this->input->readHex();
			$code = $this->translateHexOpcode($hexOpcode, $i);
			$instructions[$lin] = $code;
			println($lin.': '.$this->arrayToString($code));
		}
		return $instructions;
	}
	
	private function arrayToString($a) {
		if (is_array($a)) {
			$s = [];
			foreach ($a as $v) {
				$s[] = $this->arrayToString($v);
			}
			return implode(' ', $s);
		} else {
			return (string) $a;
		}
	}
	
	private function translateHexOpcode($hexOpcode, &$i) {
		$input = $this->input;

		$hexOpcode = hexdec($hexOpcode);
		switch ($hexOpcode) {
			case 0x00:
				return [1, 'nop'];
			case 0x01:
				return [1, 'aconst_null'];
			case 0x02:
				return [1, 'iconst_m1'];
			case 0x03:
			case 0x04:
			case 0x05:
			case 0x06:
			case 0x07:
			case 0x08:
				return [1, 'iconst_'.($hexOpcode-3 & bindec('000111'))];
			case 0x09:
			case 0x0a:
				return [1, 'lconst_'.($hexOpcode-1 & bindec('000111'))];
			case 0x0b:
			case 0x0c:
			case 0x0d:
				return [1, 'fconst_'.($hexOpcode-3 & bindec('000111'))];
			case 0x0e:
			case 0x0f:
				return [1, 'dconst_'.($hexOpcode-6 & bindec('000111'))];
			case 0x10:
				$i++;
				return [2, 'bipush', $input->readByte()];
			case 0x11:
				$i += 2;
				return [3, 'sipush', $input->readShort()];
			case 0x12:
				$i++;
				return [2, 'ldc', $this->getDataFromRef($input->readByte())];
			case 0x13:
				$i += 2;
				return [3, 'ldc_w', $this->getDataFromRef($input->readShort())];
			case 0x14:
				$i += 2;
				return [3, 'ldc2_w', $this->getDataFromRef($input->readShort())];
			case 0x15:
				$i++;
				return [2, 'iload', $input->readByte()];
			case 0x16:
				$i++;
				return [2, 'lload', $input->readByte()];
			case 0x18:
				$i++;
				return [2, 'dload', $input->readByte()];
			case 0x19:
				$i++;
				return [2, 'aload', $input->readByte()];
			case 0x1a:
			case 0x1b:
			case 0x1c:
			case 0x1d:
				return [1, 'iload_'.($hexOpcode-2 & bindec('000111'))];
			case 0x1e:
			case 0x1f:
			case 0x20:
			case 0x21:
				return [1, 'lload_'.($hexOpcode-6 & bindec('000111'))];
			case 0x22:
			case 0x23:
			case 0x24:
			case 0x25:
				return [1, 'fload_'.($hexOpcode-2 & bindec('000111'))];
			case 0x26:
			case 0x27:
			case 0x28:
			case 0x29:
				return [1, 'dload_'.($hexOpcode-6 & bindec('000111'))];
			case 0x2a:
			case 0x2b:
			case 0x2c:
			case 0x2d:
				return [1, 'aload_'.($hexOpcode-2 & bindec('000111'))];
			case 0x2e:
				return [1, 'iaload'];
			case 0x2f:
				return [1, 'laload'];
			case 0x32:
				return [1, 'aaload'];
			case 0x33:
				return [1, 'baload'];
			case 0x34:
				return [1, 'caload'];
			case 0x35:
				return [1, 'saload'];
			case 0x36:
				$i++;
				return [2, 'istore', $input->readByte()];
			case 0x37:
				$i++;
				return [2, 'lstore', $input->readByte()];
			case 0x39:
				$i++;
				return [2, 'dstore', $input->readByte()];
			case 0x3a:
				$i++;
				return [2, 'astore', $input->readByte()];
			case 0x3b:
			case 0x3c:
			case 0x3d:
			case 0x3e:
				return [1, 'istore_'.($hexOpcode-3 & bindec('000111'))];
			case 0x3f:
			case 0x40:
			case 0x41:
			case 0x42:
				return [1, 'lstore_'.($hexOpcode+1 & bindec('000111'))];
			case 0x43:
			case 0x44:
			case 0x45:
			case 0x46:
				return [1, 'fstore_'.($hexOpcode-3 & bindec('000111'))];
			case 0x47:
			case 0x48:
			case 0x49:
			case 0x4a:
				return [1, 'dstore_'.($hexOpcode+1 & bindec('000111'))];
			case 0x4b:
			case 0x4c:
			case 0x4d:
			case 0x4e:
				return [1, 'astore_'.($hexOpcode-3 & bindec('000111'))];
			case 0x4f:
				return [1, 'iastore'];
			case 0x50:
				return [1, 'lastore'];
			case 0x53:
				return [1, 'aastore'];
			case 0x54:
				return [1, 'bastore'];
			case 0x55:
				return [1, 'castore'];
			case 0x56:
				return [1, 'sastore'];
			case 0x57:
				return [1, 'pop'];
			case 0x59:
				return [1, 'dup'];
			case 0x5a:
				return [1, 'dup_x1'];
			case 0x5c:
				return [1, 'dup2'];
			case 0x5e:
				return [1, 'dup2_x2'];
			case 0x60:
				return [1, 'iadd'];
			case 0x61:
				return [1, 'ladd'];
			case 0x62:
				return [1, 'fadd'];
			case 0x63:
				return [1, 'dadd'];
			case 0x64:
				return [1, 'isub'];
			case 0x65:
				return [1, 'lsub'];
			case 0x66:
				return [1, 'fsub'];
			case 0x67:
				return [1, 'dsub'];
			case 0x68:
				return [1, 'imul'];
			case 0x6a:
				return [1, 'fmul'];
			case 0x6b:
				return [1, 'dmul'];
			case 0x6c:
				return [1, 'idiv'];
			case 0x6d:
				return [1, 'ldiv'];
			case 0x6e:
				return [1, 'fdiv'];
			case 0x6f:
				return [1, 'ddiv'];
			case 0x69:
				return [1, 'lmul'];
			case 0x70:
				return [1, 'irem'];
			case 0x71:
				return [1, 'lrem'];
			case 0x74:
				return [1, 'ineg'];
			case 0x75:
				return [1, 'lneg'];
			case 0x78:
				return [1, 'ishl'];
			case 0x79:
				return [1, 'lshl'];
			case 0x7a:
				return [1, 'ishr'];
			case 0x7b:
				return [1, 'lshr'];
			case 0x7c:
				return [1, 'iushr'];
			case 0x7d:
				return [1, 'lushr'];
			case 0x7e:
				return [1, 'iand'];
			case 0x7f:
				return [1, 'land'];
			case 0x80:
				return [1, 'ior'];
			case 0x81:
				return [1, 'lor'];
			case 0x82:
				return [1, 'ixor'];
			case 0x83:
				return [1, 'lxor'];
			case 0x84:
				$i += 2;
				return [3, 'iinc', ($input->readByte()), ($input->readByte())];
			case 0x85:
				return [1, 'i2l'];
			case 0x86:
				return [1, 'i2f'];
			case 0x87:
				return [1, 'i2d'];
			case 0x88:
				return [1, 'l2i'];
			case 0x89:
				return [1, 'l2f'];
			case 0x8a:
				return [1, 'l2d'];
			case 0x8b:
				return [1, 'f2i'];
			case 0x8c:
				return [1, 'f2l'];
			case 0x8d:
				return [1, 'f2d'];
			case 0x8e:
				return [1, 'd2i'];
			case 0x8f:
				return [1, 'd2l'];
			case 0x90:
				return [1, 'd2f'];
			case 0x91:
				return [1, 'i2b'];
			case 0x92:
				return [1, 'i2c'];
			case 0x93:
				return [1, 'i2s'];
			case 0x94:
				return [1, 'lcmp'];
			case 0x95:
				return [1, 'fcmpl'];
			case 0x96:
				return [1, 'fcmpg'];
			case 0x97:
				return [1, 'dcmpl'];
			case 0x98:
				return [1, 'dcmpg'];
			case 0x99:
				$i += 2;
				return [3, 'ifeq', ($input->readShort())];
			case 0x9a:
				$i += 2;
				return [3, 'ifne', ($input->readShort())];
			case 0x9b:
				$i += 2;
				return [3, 'iflt', ($input->readShort())];
			case 0x9c:
				$i += 2;
				return [3, 'ifge', ($input->readShort())];
			case 0x9d:
				$i += 2;
				return [3, 'ifgt', ($input->readShort())];
			case 0x9e:
				$i += 2;
				return [3, 'ifle', ($input->readShort())];
			case 0x9f:
				$i += 2;
				return [3, 'if_icmpeq', ($input->readShort())];
			case 0xa0:
				$i += 2;
				return [3, 'if_icmpne', ($input->readShort())];
			case 0xa1:
				$i += 2;
				return [3, 'if_icmplt', ($input->readShort())];
			case 0xa2:
				$i += 2;
				return [3, 'if_icmpge', ($input->readShort())];
			case 0xa3:
				$i += 2;
				return [3, 'if_icmpgt', ($input->readShort())];
			case 0xa4:
				$i += 2;
				return [3, 'if_icmple', ($input->readShort())];
			case 0xa5:
				$i += 2;
				return [3, 'if_acmpeq', ($input->readShort())];
			case 0xa6:
				$i += 2;
				return [3, 'if_acmpne', ($input->readShort())];
			case 0xa8:
				$i += 2;
				return [3, 'jsr', ($input->readShort())];
			case 0xa9:
				$i++;
				return [3, 'ret', ($input->readByte())];
			case 0xaa:
				$len = $i - 1;
				while(($i + 1) % 4 != 0) {
					$i++;
					$hexOpcode = $input->readHex();
				}
				
				$i += 4;
				$defaut = $input->readInt();
				
				$i += 4;
				$low = $input->readInt();
				
				$i += 4;
				$high = $input->readInt();

				$length = $high - $low + 1;
				
				$i += $length * 4;
				$input->skipBytes($length * 4 );
				return [$i - $len, 'tableswitch (not suported yet) '.($i+1)];
				//exit;
			case 0xa7:
				$i += 2;
				return [3, 'goto', ($input->readShort())];
			case 0xac:
				return [1, 'ireturn'];
			case 0xad:
				return [1, 'lreturn'];
			case 0xae:
				return [1, 'freturn'];
			case 0xaf:
				return [1, 'dreturn'];
			case 0xb0:
				return [1, 'areturn'];
			case 0xb1:
				return [1, 'return'];
			case 0xb2:
				$i += 2;
				return [3, 'getstatic', $this->getStaticField($input->readShort())];
			case 0xb3:
				$i += 2;
				return [3, 'putstatic', $this->getStaticField($input->readShort())];
			case 0xb4:
				$i += 2;
				return [3, 'getfield', $this->getStaticField($input->readShort())];
			case 0xb5:
				$i += 2;
				return [3, 'putfield', $this->getStaticField($input->readShort())];
			case 0xb6:
				$i += 2;
				return [3, 'invokevirtual', $this->getMethod($input->readShort())];
			case 0xb7:
				$i += 2;
				return [3, 'invokespecial', $this->getMethod($input->readShort())];
			case 0xb8:
				$i += 2;
				return [3, 'invokestatic', $this->getMethod($input->readShort())];
			case 0xb9:
				$i += 4;
				return [5, 'invokeinterface', $this->getInterfaceMethod($input->readShort()), $input->readShort()];
			case 0xbb:
				$i += 2;
				return [3, 'new', $this->getClassName($input->readShort())];
			case 0xbc:
				$i++;
				$types = [
					 4 => 'boolean',
					 5 => 'char',
					 6 => 'float',
					 7 => 'double',
					 8 => 'byte',
					 9 => 'short',
					10 => 'int',
					11 => 'long'
				];
				return [2, 'newarray', $types[$input->readByte()]];
			case 0xbe:
				return [1, 'arraylength'];
			case 0xbd:
				$i += 2;
				return [3, 'anewarray', $this->getClassName($input->readShort())];
			case 0xbf:
				return [1, 'athrow'];
			case 0xc0:
				$i += 2;
				return [3, 'checkcast', $this->getClassName($input->readShort())];
			case 0xc1:
				$i += 2;
				//var_dump([3, 'instanceof', $this->getClassName($input->readShort())]);exit;
				return [3, 'instanceof', $this->getClassName($input->readShort())];
			case 0xc2:
				return [1, 'monitorenter'];
			case 0xc3:
				return [1, 'monitorexit'];
			case 0xc5:
				$i += 3;
				return [4, 'multianewarray', $this->getClassName($input->readShort()), $input->readByte()];
			case 0xc6:
				$i += 2;
				return [3, 'ifnull', ($input->readShort())];
			case 0xc7:
				$i += 2;
				return [3, 'ifnonnull', ($input->readShort())];
			default:
				var_dump(dechex($hexOpcode));
				echo 'unknown opcode';
				exit;
				return $hexOpcode;
		}
	}
	
	private function getString($i) {
		$const = $this->getConstant($i);
		if ($const[0] != 'utf8') {
			throw new \Exception('not an utf8!');
		}
		return $const[1];
	}
	
	private function getClassName($i) {
		$const = $this->getConstant($i);
		if ($const[0] != 'Class') {
			throw new \Exception('not a Class!');
		}
		return $this->getString($const[1]);
	}
	
	private function getFieldName($i) {
		$desc = $this->getConstant($i);
		if ($desc[0] != 'field') {
			throw new \Exception('not a field!');
		}
		//retuns $this->getClassName($desc[1][0]);
		return $this->getDescriptorName($desc[1][1]);
	}
	
	private function getStaticField($i) {
		$desc = $this->getConstant($i);
		if ($desc[0] != 'field') {
			throw new \Exception('not a field!');
		}
		$className = $this->getClassName($desc[1][0]);
		$field = $this->getConstant($desc[1][1]);
		$fieldName = $this->getString($field[1][0]);
		$fieldType = $this->getString($field[1][1]);
		return [
			'class' => $className, 
			'field' => $fieldName, 
			'type'  => $fieldType
		];
	}
	
	private function getMethod($i) {
		$desc = $this->getConstant($i);
		if ($desc[0] != 'method') {
			throw new \Exception('not a method! ('.$desc[0].')');
		}
		$className = $this->getClassName($desc[1][0]);
		$method = $this->getConstant($desc[1][1]);
		$methodName = $this->getString($method[1][0]);
		$methodType = $this->getArgsType($this->getString($method[1][1]));
		//var_dump($methodName, $methodType);exit;
		//var_dump($desc[1]);exit;
		return [
			'class' => $className, 
			'method' => $methodName, 
			'args' => $methodType['args'], 
			'return' => $methodType['return']
		];
	}
	
	private function getInterfaceMethod($i) {
		$desc = $this->getConstant($i);
		if ($desc[0] != 'interface') {
			throw new \Exception('not an interface! ('.$desc[0].')');
		}
		$className = $this->getClassName($desc[1][0]);
		$method = $this->getConstant($desc[1][1]);
		$methodName = $this->getString($method[1][0]);
		$methodType = $this->getArgsType($this->getString($method[1][1]));
		//var_dump($methodName, $methodType);exit;
		//var_dump($desc[1]);exit;
		return [
			'class' => $className, 
			'method' => $methodName, 
			'args' => $methodType['args'], 
			'return' => $methodType['return']
		];
	}
	
	private function getArgsType($argsType) {
		list($argsStr, $return) = explode(')', substr($argsType, 1));
		$args = [];
		$argsLen = strlen($argsStr);
		$argPrefix = '';
		for ($i = 0; $i < $argsLen; $i++) {
			$arg = $argsStr[$i];
			
			if ($arg == '[') {
				$argPrefix .= '[';
				continue;
			} else if ($arg == 'L') {
				$i += strlen($args[] = substr($argsStr, $i+1, strpos($argsStr, ';', $i)-$i-1)) +1;
			} else {
				$args[] = $arg;
			}
			if ($argPrefix) {
				$pos = count($args) - 1;
				$args[$pos] = $argPrefix . $args[$pos];
				$argPrefix = '';
			}
		}
		return ['args' => $args, 'return' => $return];
	}
	
	private function getMethodName($i) {
		$desc = $this->getConstant($i);
		if ($desc[0] != 'method') {
			throw new \Exception('not a method! ('.$desc[0].')');
		}
		//retuns $this->getClassName($desc[1][0]);
		return $this->getDescriptorName($desc[1][1]);
	}

	private function getInterfaceMethodName($i) {
		$desc = $this->getConstant($i);
		if ($desc[0] != 'interface') {
			throw new \Exception('not an interface! ('.$desc[0].')');
		}
		return $this->getDescriptorName($desc[1][1]);
	}
	
	private function getStringFromRef($i) {
		$const = $this->getConstant($i);
		if ($const[0] != 'String') {
			throw new \Exception('not a String!');
		}
		return $this->getString($const[1]);
	}
	
	private function getDataFromRef($i) {
		$const = $this->getConstant($i);
		if ($const[0] == "String") {
			$s = new \java\lang\String($this->getDataFromRef($const[1]));
			return $s->intern();
		} else if ($const[0] == "Class") {
			return \java\lang\Clazz::forName($this->getDataFromRef($const[1]));
		}
		return $const[1];
	}
	
	private function getDataFromRefDebug($i) {
		$const = $this->getConstant($i);
		if ($const[0] == "String") {
			return "(String)" . $this->getDataFromRefDebug($const[1]);
		}
		return "(" . $const[0] . ")"
		     . "" . $const[1];
	}
	
	private function getDescriptorName($i) {
		$desc = $this->getConstant($i)[1];
		return $this->getString($desc[0]);
	}
	
	private function getConstant($i) {
		if (!isset($this->constant_pool[$i])) {
			throw new \Exception('constant not found! ('.$this->class_attr['name'].' '.$i.')');
		}
		return $this->constant_pool[$i];
	}
	
	private function flags($flag) {
		$str_flags = '';
		if (($flag & 0x0001) == 0x0001) {
			$str_flags .= " public";
		}
		if (($flag & 0x0002) == 0x0002) {
			$str_flags .= " private";
		}
		if (($flag & 0x0004) == 0x0004) {
			$str_flags .= " protected";
		}
		if (($flag & 0x0008) == 0x0008) {
			$str_flags .= " static";
		}
		if (($flag & 0x0010) == 0x0010) {
			$str_flags .= " final";
		}
		if (($flag & 0x0020) == 0x0020) {
			$str_flags .= " super";
		}
		if (($flag & 0x0040) == 0x0040) {
			$str_flags .= " brigde";
		}
		if (($flag & 0x0080) == 0x0080) {
			$str_flags .= " varargs";
		}
		if (($flag & 0x0100) == 0x0100) {
			$str_flags .= " native";
		}
		if (($flag & 0x0200) == 0x0200) {
			$str_flags .= " interface";
		}
		if (($flag & 0x0400) == 0x0400) {
			$str_flags .= " abstract";
		}
		if (($flag & 0x0800) == 0x0800) {
			$str_flags .= " strict";
		}
		if (($flag & 0x1000) == 0x1000) {
			$str_flags .= " synthetic";
		}
		if (($flag & 0x2000) == 0x2000) {
			$str_flags .= " annotation";
		}
		if (($flag & 0x4000) == 0x4000) {
			$str_flags .= " enum";
		}
		return trim($str_flags);
	}
	
	public static function readJarManifest($jar_file) {
		$manifest = [];
		$fp = fopen('zip://'.realpath($jar_file).'#META-INF/MANIFEST.MF', 'rb');
		foreach(explode("\n", stream_get_contents($fp)) as $linha) {
			$ar = explode(':', $linha);
			$manifest[trim($ar[0])] = trim($ar[1]);
		};
		fclose($fp);
		return $manifest;
	}
	
}

function println($str = "") {
	echo $str . PHP_EOL;
}

function readLine() {
	return trim(fgets(STDIN));
}

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {

	
	$self_php = array_shift($_SERVER["argv"]);
	
	while (count($_SERVER["argv"]) > 0) {
		$arg = array_shift($_SERVER["argv"]);
		
		if ($arg == '-jar') {
			$jar_file = array_shift($_SERVER["argv"]);
			\JavaLoader::addJarClasspath($jar_file);

			$arg = \php_javaClass::readJarManifest($jar_file)['Main-Class'];
		} else if (substr($arg, 0, 1) == '-' ) {
			echo "arg $arg not exists!";
			exit;
		}
		
		$class = str_replace('.', '\\', $arg);
		
		$thread = new \java\lang\Thread();
		\java\lang\Thread::$currentThreadId = $thread->getId();
		
		\java\lang\ClassLoader::getSystemClassLoader()->loadClass($class);
		$class::main($_SERVER["argv"]);
		exit;
	}
	
	$class = $_SERVER["argv"][1]?$_SERVER["argv"][1]:'Teste';
	$class = str_replace('.', '\\', $class);
	
	\java\lang\ClassLoader::getSystemClassLoader()->loadClass($class);
	$class::main([]);
		
	//$javaClass = new php_javaClass($class);
	//$args = array_slice($_SERVER["argv"], 2);
	//var_dump($args);exit;
	//$javaClass->run('main', [$args]);
									 
}

?>
