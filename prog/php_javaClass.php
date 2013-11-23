<?php
include 'DataInputStream.php';
include 'JavaMock.php';
include 'JavaNative.php';
include 'JavaLoader.php';
include 'JavaTranslator.php';
include 'JavaInterpreter.php';
include 'JavaPhpCompiler.php';
include 'PhpThread.php';

error_reporting(E_ALL ^ E_STRICT);

//spl_autoload_register([\java\lang\ClassLoader::getSystemClassLoader(), 'loadClass']);
spl_autoload_register(function($className){

	/* java\lang\ClassLoader */
	$classLoader = \java\lang\Thread::currentThread()->getContextClassLoader();
	
	if ($classLoader === null) {
		var_dump($className);
	}
	
	//$classLoader->setDefaultAssertionStatus(true);
	$className = str_replace('\\', '.', str_replace('_S_', '$', $className));
	$classLoader->loadClass($className);
});

class php_javaClass extends \java\lang\Object {
	use \JavaTranslator;
	use \JavaInterpreter;
	use \JavaPhpCompiler;
	
	//const FILE_NAME = "ClassReader.class";
	//const FILE_NAME = "Teste.class";
	//const FILE_NAME = "codecs\$py.class";
	//const FILE_NAME = "CampoMinado.class";
	
	#DataInputStream
	private $input;
	
	private $constant_pool = array();
	
	public $class_attr = array();
	
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
		$this->class_attr['attr'] = $this->attr($attr_count);
		//var_dump(array_keys($this->class_attr['attr']));exit;
		//print_r($this->class_attr);
		println('the end?');
		ob_end_clean();
		
		$this->createPhpClass();
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
			} else if ($attr_name == 'InnerClasses') {
				$number_of_classes = $this->input->readShort();
				println("  attribute $i length: " . $attr_length);
				println("  attribute $i number_of_classes: " . $number_of_classes);
				
				$classes = [];
				for ($class_i = 0; $class_i < $number_of_classes; $class_i++) {
					$enc_class_attr = [];
					$enc_class_attr['inner_class_name'] = $this->getClassName($this->input->readShort());
					println("    enclosing class $class_i/$number_of_classes inner_class_name: " . $enc_class_attr['inner_class_name']);
					
					$outer_class_name_index = $this->input->readShort();
					$outer_class_name = $outer_class_name_index ? $this->getClassName($outer_class_name_index) : '';
					$enc_class_attr['outer_class_name'] = $outer_class_name;
					println("    enclosing class $class_i/$number_of_classes outer_class_name " . $enc_class_attr['outer_class_name']);
					
					$inner_name_index = $this->input->readShort();
					$inner_name = $inner_name_index ? $this->getString($inner_name_index) : '';
					$enc_class_attr['inner_name'] = $inner_name;
					println("    enclosing class $class_i/$number_of_classes inner_name " . $enc_class_attr['inner_name']);
					$enc_class_attr['inner_class_access_flags'] = $this->input->readShort();
					println("    enclosing class $class_i/$number_of_classes inner_class_access_flags " . $enc_class_attr['inner_class_access_flags']);
					$classes[] = $enc_class_attr;
					//println("    enclosing class $class_i/$number_of_classes " . $enc_class_attr['inner_class_name']);
				}
				$attrs[$attr_name] = $classes;
			} else {
				$attrs[$attr_name] = '(?)length '.$attr_length;
				println("  attribute $i length: " . $attr_length);
				$this->input->skipBytes($attr_length);
			}
			println();
		}
		return $attrs;
	}
	
	public function getPhpClassName() {
		return str_replace('/', '\\', str_replace('$', '_S_', $this->class_attr['name']));
	}
	
	public function createPhpClass() {
	
		$className = str_replace('$', '_S_', $this->class_attr['name']);
		
		$super = '\\'.str_replace('/', '\\', str_replace('$', '_S_', $this->class_attr['super']));
		
		//$isInterface = strpos($this->class_attr['flags'], 'interface') !== false;
		$isInterface = false;
		
		$implements = '';
		if (!empty($this->class_attr['interfaces'])) {
			//$implements = str_replace('/', '\\', 'implements /'.implode(', /', $this->class_attr['interfaces']));
		}
		
		$fields = '';
		//$dump = 'var_dump(';
		foreach ($this->class_attr['fields'] as $field) {
			//var_dump($field); exit;
			$iniValue = 'null';
			if (in_array($field['type'], ['J', 'I'])) {
				$iniValue = '0';
			}
			if (strpos($field['flags'], 'static') !== false) {
				$fields .= 'public static $'.str_replace('$', '_S_', $field['name'])." = $iniValue;".PHP_EOL;
			} else {
				$fields .= 'public $'.str_replace('$', '_S_', $field['name'])." = $iniValue;".PHP_EOL;
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
				} else if ($fname == 'hashCode') {
					$fname = 'staticHashCode';
				} else if ($fname == 'equals') {
					$fname = 'staticEquals';
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
		$className = fixPhpClassName(basename($className));
		$clinit = '';
		try {
			$this->findMethod('<clinit>');
			$clinit = "$className::__callstatic('<clinit>', []);";
		} catch (\Exception $e) {
			//
		}
		//$classDeclaration = $isInterface ? 'interface': 'class';
		
		//var_dump($namespace, $className, class_exists($className, false));
		
		if (!$isInterface) {
			$eval = eval($s=<<<CODE
			$namespace
			class $className extends $super $implements {
				public static \$javaClass;
				public static function __callstatic(\$method, \$args) {
					try {
						return self::\$javaClass->run(\$method, \$args);
					} catch(\\JavaMethodNotFoundException \$e) {
						return parent::\$javaClass->run(\$method, \$args);
					}
				}
				
				public function __call(\$method, \$args) {
					try {
						return self::\$javaClass->run(\$method, \$args, \$this);
					} catch(\\JavaMethodNotFoundException \$e) {
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
		} else {
			
			$implements = str_replace('implements', 'extends', $implements);
			$eval = eval($s=<<<CODE
			$namespace
			
			interface $className $implements {}
			
			class {$className}_interface  {
				public static \$javaClass;
				public static function __callstatic(\$method, \$args) {
					try {
						return self::\$javaClass->run(\$method, \$args);
					} catch(\\JavaMethodNotFoundException \$e) {
						return parent::\$javaClass->run(\$method, \$args);
					}
				}
				
				public function __call(\$method, \$args) {
					try {
						return self::\$javaClass->run(\$method, \$args, \$this);
					} catch(\\JavaMethodNotFoundException \$e) {
						return parent::\$javaClass->run(\$method, \$args, \$this);
					}
				}
				$fields
				
				$methods
			}
			{$className}_interface::\$javaClass = \$this;
			
			$clinit
CODE
);
			//exit;
		}
		//echo ($s);readline();
		//echo explode(PHP_EOL, $s)[33];
		if ($eval === false) {
			//echo explode(PHP_EOL, $s)[27];
			echo $s;
			exit;
		}
		
		$package = dirname($this->getPhpClassName());
		if ($package == '.') {
			$realClassName = $className;
		} else {
			$realClassName = $package . '\\' . $className;
		}
		
		//var_dump($this->getPhpClassName(), $realClassName);
		if ($this->getPhpClassName() != $realClassName) {
			class_alias($realClassName, $this->getPhpClassName());
		}
		$className = str_replace('/', '\\', $this->class_attr['name']);
		if ($this->getPhpClassName() != $className) {
			class_alias($this->getPhpClassName(), $className);
		}
	}
	
	public function run($method_name, $args = [], $thisObj = null) {
		$method = $this->findMethod($method_name, $args);
		
		if (strpos($method['flags'], 'native') !== false) {
			$php_function = 'Java_'.str_replace('\\', '_', $this->getPhpClassName()).'_'.$method_name;
			//echo "$php_function()";
			if ($thisObj !== null) {
				$args = array_merge([$thisObj], $args);
			}
			return call_user_func_array($php_function, $args);
		}
		
		
		if ($thisObj !== null) {
			$args = array_merge([$thisObj], $args);
		}
		
		$args = $this->fixLocalArgsIndexes($args, $method['type'], $this->class_attr['name'].'::'.$method_name);
		//var_dump($method['type']);readline();
		return $this->runCode($method['attr']['Code'], $args, $this->class_attr['name'].'::'.$method_name);
	}
	
	private function fixLocalArgsIndexes($args, $type, $name) {
		$args_type = $this->getArgsType($type);
		
		$newArgs = [];
		
		if (count($args_type['args']) === count($args)) {
			$index = 0;
			$newIndex = 0;
		} else {
			$newArgs[0] = $args[0];
			$index = 1;
			$newIndex = 1;
		}
		//var_dump(count($args), count($args_type['args']), $args, $args_type, $name);readline();
		foreach ($args_type['args'] as $arg) {
			$newArgs[$newIndex] = $args[$index];
			
			$index++;
			$newIndex++;
			
			if ($arg === 'J') {
				$newIndex++;
			}
		}
		return $newArgs;
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
		//var_dump($code);readline();
		//$this->getArgsType
		//var_dump($instructions);exit;
		for ($i = 0;$i !== null && $i <= $last_index;) {
			//var_dump($i);
			$opcode = $instructions[$i];
			try {
				$this->interpret($i, $opcode, $stack, $locals, $method);
			} catch (\Exception $e) {
				foreach ($code['exceptions'] as $exception) {
					if (is_a($e, str_replace('/', '\\', '\\'.$exception['catch_type'])) 
						&& $i >= $exception['start_pc'] 
						&& $i < $exception['end_pc']) {
						//var_dump($exception);readline();
						$i = $exception['handler_pc'];
						$stack = [$e];
						continue 2;
					}
				}
				//var_dump(get_class($i));readline();
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
		throw new \JavaMethodNotFoundException('method ' . $name . ' not found!');
		
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
			return (new \java\lang\String($this->getDataFromRef($const[1])))->intern();
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

class JavaMethodNotFoundException extends \Exception {}

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
		
		$thread = new \java\lang\Thread();
		\java\lang\Thread::$currentThreadId = $thread->getId();
		
		if ($arg == '-jar') {
			$jar_file = array_shift($_SERVER["argv"]);
			\JavaLoader::addJarClasspath($jar_file);

			$arg = \php_javaClass::readJarManifest($jar_file)['Main-Class'];
		} else if ($arg == '-cp') {
			$cp = explode(';', array_shift($_SERVER["argv"]));
			//var_dump($_SERVER["argv"]);
			$loader = \java\lang\ClassLoader::getSystemClassLoader();
			for ($i = 1; $i < count($cp); $i++) {
				$loader->addClasspath($cp[$i]);
			}
			$arg = array_shift($_SERVER["argv"]);
		} else if (substr($arg, 0, 1) == '-' ) {
			echo "arg $arg not exists!";
			exit;
		}
		
		$class = str_replace('.', '\\', $arg);
		
		\java\lang\ClassLoader::getSystemClassLoader()->loadClass($class);
		//var_dump($_SERVER["argv"]);readline();
		$class::main($_SERVER["argv"]);
		exit;
	} 
	`java`;
	 
	
	/*
	$class = $_SERVER["argv"][1]?$_SERVER["argv"][1]:'Teste';
	$class = str_replace('.', '\\', $class);
	
	\java\lang\ClassLoader::getSystemClassLoader()->loadClass($class);
	$class::main([]);
	*/
		
	//$javaClass = new php_javaClass($class);
	//$args = array_slice($_SERVER["argv"], 2);
	//var_dump($args);exit;
	//$javaClass->run('main', [$args]);
									 
}

?>
