<?php

trait JavaInterpreter {
	private function interpret(&$i, $opcode, &$stack, &$locals, $method) {
		//var_dump([$method, $i]);
		//var_dump([$i, $opcode, $stack]);
		//var_dump($stack);readline();
		//var_dump([$locals, $stack, $opcode, $method, $i]);readline();
		//var_dump([$locals, $stack, $opcode, $i]);
		//var_dump($method);
		//if ('Teste22$1::<clinit>' == $method) {
			//var_dump(['$locals' => $locals, '$stack' =>$stack, '$opcode' => $opcode, '$i' =>$i]);
			//readline();
			//var_dump([$opcode, $stack, $i]);readline();
		//}
		$stack = array_values($stack);
		switch ($opcode[1]) {
			case 'aaload':
			case 'iaload':
			case 'laload':
			case 'daload':
			case 'faload':
			case 'caload':
			case 'baload':
				$args = $this->stackArrayPop($stack, 2);
				//var_dump($args[1], count($args[0]), $method, $i);
				if (!array_key_exists($args[1], $args[0])) {
					//var_dump($args[1], $args[0], $method, $i);readline();
					throw new \java\lang\Exception('out of bounds');
				}
				$stack[] = $args[0][$args[1]];
				break;
			case 'aastore':
			case 'iastore':
			case 'dastore':
			case 'lastore':
			case 'castore':
			case 'bastore':
				$args = $this->stackArrayPop($stack, 3);
				//var_dump($args[1].'', $args[0]->getSize());
				if (!array_key_exists($args[1].'', $args[0])) {
					throw new \java\lang\Exception('out of bounds');
				}
				$args[0][$args[1]] = $args[2];
				break;
			case 'arraylength':
				$ar = array_pop($stack);
				$stack[] = $ar->getSize();
				break;
			case 'bipush':
				$stack[] = $opcode[2];
				break;
			case 'aload':
				$opcode[1] = 'aload_'.$opcode[2];
			case 'aload_0':
			case 'aload_1':
			case 'aload_2':
			case 'aload_3':
				$stack[] = $locals[explode('_', $opcode[1])[1]];
				break;
			case 'iload':
			case 'lload':
			case 'fload':
			case 'dload':
				$opcode[1] .= '_'.$opcode[2];
			case 'iload_0':
			case 'iload_1':
			case 'iload_2':
			case 'iload_3':
			case 'lload_0':
			case 'lload_1':
			case 'lload_2':
			case 'lload_3':
			case 'dload_0':
			case 'dload_1':
			case 'dload_2':
			case 'dload_3':
			case 'fload_0':
			case 'fload_1':
			case 'fload_2':
			case 'fload_3':
				//var_dump($stack, $locals);exit;
				$stack[] = $locals[explode('_', $opcode[1])[1]];
				break;
			case 'dup':
				$stack[] = $stack[count($stack)-1];
				break;
			case 'dup_x1':
				list($value1, $value2) = [array_pop($stack), array_pop($stack)];
				list($stack[], $stack[], $stack[]) = [$value1, $value2, $value1];
				break;
			case 'dup_x2':
				list($value1, $value2, $value3) = [array_pop($stack), array_pop($stack), array_pop($stack)];
				list($stack[], $stack[], $stack[], $stack[]) = [$value1, $value2, $value3, $value1];
				break;
			//case 'swap':
				
				break;
			case 'getstatic':
				/*
				$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]['class']));
				$var = '$'.str_replace('$', '_S_', $opcode[2]['field']);
				eval("\$stack[] = &$class::$var;");
				*/
				
				$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]['class']));
				$refClass = new ReflectionClass($class);
				$var = str_replace('$', '_S_', $opcode[2]['field']);
				//var_dump($class);
				$property = $refClass->getProperty($var);
				$stack[] = $property->getValue();
				break;
			case 'putstatic':
				
				$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]['class']));
				$var = '$'.str_replace('$', '_S_', $opcode[2]['field']);
				$args = $this->stackArrayPop($stack, 1);
				if (interface_exists($class)) {
					$class .= '_interface';
				}
				eval("$class::$var = \$args[0];");
				break;
				
				/*
				$class = '\\'.str_replace('/', '\\', $opcode[2]['class']);
				$refClass = new ReflectionClass($class);
				$args = $this->stackArrayPop($stack, 1);
				$property = $refClass->getProperty($opcode[2]['field']);
				$property->setValue($args[0]);
				break;
				*/
			case 'getfield':
				$var = $opcode[2]['field'];
				//$args = $this->stackArrayPop($stack, 1);
				$stack[] = array_pop($stack)->$var;
				break;
			case 'putfield':
				//$class = str_replace('/', '\\', $opcode[2]['class']);
				$var = $opcode[2]['field'];
				$args = $this->stackArrayPop($stack, 2);
				//var_dump($args, $var, $method);readline();
				$args[0]->$var = $args[1];
				break;
			case 'goto':
				$i += $opcode[2];
				return;
			case 'aconst_null':
				$stack[] = null;
				break;
			case 'iconst_m1':
				$stack[] = -1;
				break;
			case 'iconst_0':
			case 'iconst_1':
			case 'iconst_2':
			case 'iconst_3':
			case 'iconst_4':
			case 'iconst_5':
			case 'lconst_0':
			case 'lconst_1':
			case 'lconst_2':
			case 'lconst_3':
			case 'lconst_4':
			case 'lconst_5':
			case 'fconst_0':
			case 'fconst_1':
			case 'fconst_2':
			case 'dconst_0':
			case 'dconst_1':
			case 'dconst_2':
				$stack[] = +(explode('_', $opcode[1])[1]);
				break;
			case 'tableswitch':
			case 'lookupswitch':
				
				$offset = array_pop($stack);
				$jump = $opcode[2]['default'];
				
				if (isset($opcode[2][$offset])) {
					$jump = $opcode[2][$offset];
				}
				$i += $jump;
				return;
			case 'ifnull':
				if (array_pop($stack) === null) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifnonnull':
				if (array_pop($stack) !== null) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifeq':
				if (array_pop($stack) == 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifne':
				//$s = array_pop($stack);
				//var_dump($s);
				//if ($s != 0) {
				if (array_pop($stack) != 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'iflt':
				$v = array_pop($stack);
				//var_dump(['$v' => $v]);
				if ($v < 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifge':
				$v = array_pop($stack);
				if (is_object($v)) {
					var_dump(['method' => $method, '$i' => $i, '$v' => $v]);
					exit;
				}
				if ($v >= 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifgt':
				if (array_pop($stack) > 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifle':
				if (array_pop($stack) <= 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'if_icmpeq':
			case 'if_acmpeq':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 === $v2) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'if_icmpne':
			case 'if_acmpne':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 !== $v2) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'if_icmpge':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 >= $v2) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'if_icmple':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 <= $v2) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'if_icmplt':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 < $v2) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'if_icmpgt':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 > $v2) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'fcmpl':
			case 'dcmpl':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				//var_dump($v1, $v2, "$v1" == "$v2");
				if ("$v1" === "$v2") {
					$stack[] = 0;
				} else if ($v1 < $v2) {
					$stack[] = -1;
				} else {
					$stack[] = 1;
				}
				break;
			case 'fcmpg':
			case 'dcmpg':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				//var_dump($v1, $v2, "$v1" == "$v2");
				if ("$v1" === "$v2") {
					$stack[] = 0;
				} else if ($v1 < $v2) {
					$stack[] = -1;
				} else {
					$stack[] = 1;
				}
				//var_dump($method, "$v1 === $v2", $stack);readline();
				break;
			case 'fcmp':
			case 'dcmp':
			case 'lcmp':
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 === $v2) {
					$stack[] = 0;
				} else if ($v1 < $v2) {
					$stack[] = -1;
				} else {
					$stack[] = 1;
				}
				break;
			case 'fadd':
			case 'dadd':
			case 'iadd':
			case 'ladd':
				$stack[] = array_pop($stack) + array_pop($stack);
				break;
			case 'iinc':
				//var_dump(['local' => $locals[$opcode[2]], 'iinc' => $opcode[3]]);
				if ($opcode[3] == 1) {
					$locals[$opcode[2]]++;
				} else {
					$locals[$opcode[2]] += $opcode[3];
				}
				break;
			case 'isub':
			case 'lsub':
			case 'dsub':
			case 'fsub':
				$stack[] = - array_pop($stack) + array_pop($stack);
				break;
			case 'idiv':
			case 'ldiv':
			case 'ddiv':
			case 'fdiv':
				//var_dump($stack);exit;
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				if ($n2 == 0) {
					throw new \java\lang\ArithmeticException("/ by zero");
				}
				$stack[] = $n1 / $n2;
				break;
			case 'fmul':
			case 'dmul':
			case 'imul':
			case 'lmul':
				$stack[] = array_pop($stack) * array_pop($stack);
				break;
			case 'irem':
			case 'lrem':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				if ($n2 == 0) {
					throw new \java\lang\ArithmeticException("/ by zero");
				}
				$stack[] = $n1 % $n2;
				break;
			case 'ior':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				$stack[] = $n1 | $n2;
				break;
			case 'iand':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				$stack[] = $n1 & $n2;
				break;
			case 'ixor':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				$stack[] = $n1 xor $n2;
				break;
			case 'lneg':
			case 'ineg':
			case 'dneg':
				$stack[] = -array_pop($stack);
				break;
			case 'ishl':
			//case 'lshl':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				$stack[] = $n1 << $n2;
				break;
			case 'ishr':
			case 'lshr':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				$stack[] = $n1 >> $n2;
				break;
			case 'lushr':
			case 'iushr':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				if (is_object($n1)) {
					var_dump($locals);
					var_dump([$method, $i]);
					var_dump('not a number!');
					var_dump($n1, $n2);
					
					$c = \sun\reflect\Reflection::getCallerClass(0);
					$cont = 0;
					while ($c != null) {
						\java\lang\System::$out->println($c);
						$c = \sun\reflect\Reflection::getCallerClass(++$cont);
					}
					exit;
				}
				$stack[] = $n1 >> $n2;
				break;
			case 'invokestatic':
				$numArgs = count($opcode[2]['args']);
				$class = '\\'.str_replace('/', '\\', $opcode[2]['class']);
				//var_dump(interface_exists($class));
				$args = $this->stackArrayPop($stack, $numArgs);
				$m = $opcode[2]['method'];
				if ($m == 'toString') {
					$m = 'staticToString';
				}
				
				/*
				if ($m == 'forOutputStreamWriter') {
					var_dump([$locals, $opcode, $method, $i]);
					readline();
				}
				//*/
				
				if (property_exists($class, 'javaClass')) {
					$ret = call_user_func_array([$class, '__callstatic'], [$opcode[2]['method'], $args, $opcode]);
				} else {
					$ret = call_user_func_array([$class, $opcode[2]['method']], $args);
				}
				if ($opcode[2]['return'] != 'V') {
					$stack[] = $ret;
				}
				break;
			case 'invokespecial':
				/*
				//if ($opcode[2]['type'] == '(Ljava/io/OutputStream;)V') {
				//if ($this->class_attr['name'] == 'java/io/PrintWriter') {
				if ($opcode[2]['method'] == 'forOutputStreamWriter') {
					var_dump([$locals, $opcode, $method, $i]);
					readline();
				}
				//*/
				//var_dump([$stack, $opcode, $i]);
				//ReflectionMethod
				$opcode[2]['method'] = fixPhpFuncName($opcode[2]['method']);
				$numArgs = 1 + count($opcode[2]['args']);
				$args = $this->stackArrayPop($stack, $numArgs);
				$obj = array_shift($args);
				
				$class = '\\'.str_replace('/', '\\', $opcode[2]['class']);
				
				if (property_exists($class, 'javaClass')) {
					$reflectMethod = new ReflectionMethod($class, '__call');
					$ret = $reflectMethod->invokeArgs($obj, [$opcode[2]['method'], $args, $opcode]);
				} else {
					$reflectMethod = new ReflectionMethod($class, $opcode[2]['method']);
					$ret = $reflectMethod->invokeArgs($obj, $args);
				}
				if ($opcode[2]['return'] != 'V') {
					$stack[] = $ret;
				}
				break;
			case 'invokespecial2':
				//var_dump($opcode[2]['return']);exit;
				if ($opcode[2]['method'] == '<init>') {
					$opcode[2]['method'] = '__construct';
				}
				$numArgs = 1 + count($opcode[2]['args']);
				$args = $this->stackArrayPop($stack, $numArgs);
				$obj = array_shift($args);
				$ret = call_user_func_array([$obj, $opcode[2]['method']], $args);
				if ($opcode[2]['return'] != 'V') {
					$stack[] = $ret;
				}
				break;
			case 'invokeinterface':
			case 'invokevirtual':
				//$num_args = 1+1; // ?? check this ??
				$numArgs = 1 + count($opcode[2]['args']);
				//$args = array_slice($stack, -$num_args, $num_args);
				//array_splice($stack, -$num_args, $num_args);
				//var_dump($stack);
				//var_dump($locals);
				$args = $this->stackArrayPop($stack, $numArgs);
				$obj = array_shift($args);
				if ($obj == null) {
					//var_dump(explode('::', $method)[0]);
					//var_dump(\java\lang\ClassLoader::getSystemClassLoader()->getResource(explode('::', $method)[0].'.class'));
					//var_dump([$method, $i, $obj, $args, $stack, $opcode[2]['method'], 'need object']);
					throw new \java\lang\NullPointerException();
					//exit;
				}
				//if ($args[0] == 35) {var_dump($i, $opcode, $args);exit;}
				foreach ($opcode[2]['args'] as $iArg => $arg) {
					//var_dump([$arg, $args[$iArg]]);
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				//var_dump([get_class($obj), $opcode[2]['method']], $args);
				
				/*
				if ($opcode[2]['method'] == 'forOutputStreamWriter') {
					var_dump($opcode);
					var_dump([$method, $i, $opcode[2]['method']]);
					var_dump(property_exists(get_class($obj), 'javaClass'));
					exit;
				}
				//*/
				
				if (property_exists(get_class($obj), 'javaClass')) {
					$ret = call_user_func_array([$obj, '__call'], [$opcode[2]['method'], $args, $opcode]);
				} else {
					$ret = call_user_func_array([$obj, $opcode[2]['method']], $args);
				}
				if ($opcode[2]['return'] != 'V') {
					//var_dump('ret', $opcode[2]['method'], $ret);
					$stack[] = $ret;
				}
				break;
			case 'astore':
				$opcode[1] = 'astore_'.$opcode[2];
			case 'astore_0':
			case 'astore_1':
			case 'astore_2':
			case 'astore_3':
				$args = $this->stackArrayPop($stack, 1);
				unset($locals[explode('_', $opcode[1])[1]]);
				$locals[explode('_', $opcode[1])[1]] = $args[0];
				break;
			case 'istore':
			case 'lstore':
			case 'dstore':
			case 'fstore':
				$opcode[1] .= '_'.$opcode[2];
			case 'istore_0':
			case 'istore_1':
			case 'istore_2':
			case 'istore_3':
			case 'lstore_0':
			case 'lstore_1':
			case 'lstore_2':
			case 'lstore_3':
			case 'dstore_0':
			case 'dstore_1':
			case 'dstore_2':
			case 'dstore_3':
			case 'fstore_0':
			case 'fstore_1':
			case 'fstore_2':
			case 'fstore_3':
				//$locals[explode('_', $opcode[1])[1]] = array_pop($stack);
				unset($locals[explode('_', $opcode[1])[1]]);
				list($locals[explode('_', $opcode[1])[1]]) 
				  = $this->stackArrayPop($stack, 1);
				break;
			case 'ldc':
			case 'ldc_w':
			case 'ldc2_w':
				$stack[] = $opcode[2];
				/*
				if (is_string($opcode[2])) {
					$stack[] = new \java\lang\String($opcode[2]);
				} else {
				}*/
				break;
			case 'anewarray':
				//var_dump($i, $stack);readline();
				//var_dump($opcode[2]);readline();
				$args = $this->stackArrayPop($stack, 1);
				$stack[] = $this->newArray([$args[0]], $opcode[2]);
				break;
			case 'newarray':
				//var_dump($opcode[2]);readline();
				$args = $this->stackArrayPop($stack, 1);
				$stack[] = $this->newArray([$args[0]], $opcode[2]);
				break;
			case 'multianewarray':
				$args = $this->stackArrayPop($stack, $opcode[3]);
				$stack[] = $this->newArray($args, $opcode[2]);
				//var_dump($stack);exit;
				break;
			case 'new':
				//$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]));
				$class = str_replace('/', '\\', $opcode[2]);
				
				$reflect = new ReflectionClass($class);
				
				if (is_a($class, 'Exception', true)) {
					$stack[] = $reflect->newInstance();
				} else {
					$stack[] = $reflect->newInstanceWithoutConstructor();
				}
				//var_dump($reflect);exit;
				break;
			//case 'pop2':
				//unset($stack[count($stack)-1]);
				//var_dump($stack);exit;
			case 'pop':
				//array_pop($stack);
				unset($stack[count($stack)-1]);
				break;
			case 'sipush':
				$stack[] = $opcode[2];
				break;
			case 'l2i':
			case 'f2i':
			case 'd2i':
				$stack[] = intval(array_pop($stack));
				break;
			case 'i2l':
			case 'i2f':
			case 'i2d':
			case 'l2f':
			case 'l2d':
			case 'f2l':
			case 'f2d':
			case 'd2l':
			case 'd2f':
			case 'i2b':
			case 'i2s':
				//????
				//echo $opcode[1] . PHP_EOL;
				break;
			case 'i2c':
				//$stack[] = chr(array_pop($stack));
				break;
			case 'monitorenter':
			case 'monitorexit':
				array_pop($stack);
				break;
			case 'instanceof':
				$class = str_replace('/', '\\', $opcode[2]);
				$stack[] = checkcast(array_pop($stack), $class);
				break;
			case 'checkcast':
				$class = str_replace('/', '\\', $opcode[2]);
				$obj = $stack[count($stack)-1];//end($stack);
				if ($obj !== null && !checkcast($obj, $class)) {
					$class = str_replace('/', '.', $opcode[2]);
					$msg = $obj->getClass()->getName() . ' cannot be cast to ' . $class;
					//var_dump($obj);
					/*
					foreach (debug_backtrace() as $b) {
						var_dump($b['class']);
						var_dump($b['function']);
					}*/
					throw new \java\lang\ClassCastException($msg);
				}
				break;
			case 'areturn':
			case 'ireturn':
			case 'lreturn':
			case 'dreturn':
			case 'freturn':
			case 'return':
				//return ?;
				//break;
				$i = null;
				//var_dump($i);
				return;
			case 'athrow':
				throw array_pop($stack);
			default:
				var_dump($opcode);
				var_dump('not implemented ('.$opcode[1].')');
				exit;
		}
		$i += $opcode[0];
	}
	
	public function stackArrayPop(&$stack, $qt) {
		$stack = array_values($stack);
		//$args = array_slice($stack, -$qt, $qt);
		//array_splice($stack, -$qt, $qt);
		$args = [];
		$size = count($stack);
		for ($i = 0; $i < $qt; $i++) {
			$args[] = &$stack[$size - ($i+1)];
			//unset($stack[$size - ($i+1)]);
			//array_pop($stack);
		}
		array_splice($stack, -$qt, $qt);
		$stack = array_values($stack);
		return array_reverse($args);
	}
	
	public function newArray(array $dimentions, $type = null) {
		
		//var_dump($type);
		$valueIni = strlen($type) == 1 ? 0 : null;
		
		if (count($dimentions) == 0) {
			return $valueIni;
		}
		if (count($dimentions) == 1) {
			if ($dimentions[0] < 0) {
				throw new \java\lang\NegativeArraySizeException();
			}
			if (strlen($type) > 1) {
				$type = "L$type;";
			}
			$arr = new JavaArray($dimentions[0], $type);
			if ($valueIni !== null) {
				for ($i = 0; $i < $dimentions[0]; $i++) {
					$arr[$i] = $valueIni;
				}
			}
			return $arr;
		}
		
		$size = array_shift($dimentions);
		
		if ($size < 0) {
			//echo '$size < 0: '.$size.PHP_EOL;
			//debug_print_backtrace();
			throw new \java\lang\NegativeArraySizeException();
		}
		$type = substr($type, 1);
		$javaArray = new JavaArray($size, $type);
		$type = substr($type, 1);
		for ($i = 0; $i < $size; $i++) {
			$javaArray[$i] = $this->newArray($dimentions, $type);
		}
		return $javaArray;
	}
}

function checkcast($obj, $class) {
	//return true;
	if ($obj === null) {
		return true;
	} else if ($obj instanceof JavaArray) {
		$className = $obj->getClass()->getName()->replace('.', '\\').'';
		if ($className == $class) {
			return true;
		}
		while (substr($className, 0, 1) == '[') {
			$className = substr($className, 1);
			$class = substr($class, 1);
		}
		//var_dump(substr($className, 1, -1), substr($class, 1, -1), true);
		return is_a(substr($className, 1, -1), substr($class, 1, -1), true);
		
	} else {
		return is_a($obj, $class);
	}
}

class JavaArray extends SplFixedArray {
	use \java\lang\ObjectTrait; /*{
		\java\lang\ObjectTrait::__construct as private __ObjConstruct;
		\java\lang\ObjectTrait::getClass as private getClassTrait;
	}*/
	
	private $type;
	
	public function __construct($size, $type = null) {
		//println("new JavaArray($size, $type);");
		parent::__construct($size);
		$this->type = "$type";
	}
	
	public function __wakeup() {
		$type = $this->type;
		unset($this->type);
		parent::__wakeup();
		$this->type = $type;
	}
	
	public function getClass() {
		//var_dump($this->type);
		//return $this->getClassTrait();
		
		if (strlen($this->type) > 1 && substr($this->type, -1, 1) != ';') {
			$this->type = "L{$this->type};";
		}
		$name = '['.str_replace('/', '.', str_replace('_S_', '$', $this->type));
		
		return \java\lang\Clazz::forName($name);
	}
	
	public static function fromArray($array, $type = null) {
		$size = count($array);
		$javaArray = new self($size, $type);
		for ($i = 0; $i < $size; $i++) {
			$javaArray[$i] = $array[$i];
		}
		return $javaArray;
	}
}
