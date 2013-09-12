<?php

trait JavaPhpCompiler {
	private function compileOpCode(&$i, $opcode, $method) {
		
		switch ($opcode[1]) {
			case 'aaload':
				return '$args = $thisObj->stackArrayPop($stack, 2);
				$args = $thisObj->stackArrayPop($stack, 2);
				if (!array_key_exists($args[1], $args[0])) {
					throw new \java\lang\Exception(\'out of bounds\');
				}
				$stack[] = &$args[0][$args[1]];';
			case 'aastore':
				$args = $this->stackArrayPop($stack, 3);
				if (!array_key_exists($args[1], $args[0])) {
					throw new \java\lang\Exception('out of bounds');
				}
				$args[0][$args[1]] = &$args[2];
				break;
			case 'arraylength':
				$stack[] = count(array_pop($stack));
				break;
			case 'aload':
				$opcode[1] = 'aload_'.$opcode[2];
			case 'aload_0':
			case 'aload_1':
			case 'aload_2':
			case 'aload_3':
				$stack[] = &$locals[explode('_', $opcode[1])[1]];
				break;
			case 'iaload':
			case 'laload':
			case 'daload':
			case 'faload':
			case 'caload':
				$args = $this->stackArrayPop($stack, 2);
				$stack[] = $args[0][$args[1]];
				break;
			case 'baload':
				$args = $this->stackArrayPop($stack, 2);
				if (!array_key_exists($args[1], $args[0])) {
					throw new \java\lang\Exception('out of bounds');
				}
				$stack[] = $args[0][$args[1]];
				//var_dump($stack);exit;
				break;
			case 'iastore':
			case 'dastore':
			case 'lastore':
			case 'castore':
				$args = $this->stackArrayPop($stack, 3);
				//if ($args[2] != '0') {var_dump($args[2]);exit;}
				if (!array_key_exists($args[1], $args[0])) {
					throw new \java\lang\Exception('out of bounds');
				}
				$args[0][$args[1]] = $args[2];
				break;
			case 'bastore':
				$args = $this->stackArrayPop($stack, 3);
				if (!array_key_exists($args[1], $args[0])) {
					throw new \java\lang\Exception('out of bounds');
				}
				$args[0][$args[1]] = $args[2];
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
				return '$stack[] = $locals['.explode('_', $opcode[1])[1].'];';
			case 'dup':
				$stack[] = &$stack[count($stack)-1];
				break;
			case 'getstatic':
				$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]['class']));
				$var = '$'.str_replace('$', '_S_', $opcode[2]['field']);
				return "\$stack[] = &$class::$var;";
			case 'putstatic':
				$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]['class']));
				$var = '$'.str_replace('$', '_S_', $opcode[2]['field']);
				$args = $this->stackArrayPop($stack, 1);
				eval("$class::$var = &\$args[0];");
				break;
			case 'getfield':
				$var = $opcode[2]['field'];
				//$args = $this->stackArrayPop($stack, 1);
				$stack[] = &array_pop($stack)->$var;
				break;
			case 'putfield':
				//$class = str_replace('/', '\\', $opcode[2]['class']);
				$var = $opcode[2]['field'];
				$args = $this->stackArrayPop($stack, 2);
				//var_dump($args, $var, $method);readline();
				$args[0]->$var = &$args[1];
				break;
			case 'goto':
				return 'goto '.$method.'_L'.($i + $opcode[2]).';';
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
				return '$stack[] = '.explode('_', $opcode[1])[1].';';
			//case 'instanceof':
				break;
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
				if (array_pop($stack) != 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'iflt':
				if (array_pop($stack) < 0) {
					$i += $opcode[2];
					return;
				}
				break;
			case 'ifge':
				if (array_pop($stack) >= 0) {
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
			case 'if_icmpne':
				return '
				list($v1, $v2) = $this->stackArrayPop($stack, 2);
				if ($v1 != $v2) {
					goto '.$method.'_L'.($i + $opcode[2]).';
				}';
			case 'if_icmpge':
				return '
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 >= $v2) {
					goto '.$method.'_L'.($i + $opcode[2]).';
				}';
			case 'if_icmple':
				return '
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 <= $v2) {
					goto '.$method.'_L'.($i + $opcode[2]).';
				}';
			case 'if_icmplt':
				return '
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 < $v2) {
					goto '.$method.'_L'.($i + $opcode[2]).';
				}';
			case 'if_icmpgt':
				return '
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 > $v2) {
					goto '.$method.'_L'.($i + $opcode[2]).';
				}';
			case 'fadd':
			case 'dadd':
			case 'iadd':
			case 'ladd':
				$stack[] = array_pop($stack) + array_pop($stack);
				break;
			case 'iinc':
				return '$locals['.$opcode[2].'] += '.$opcode[3].';';
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
			case 'dmul':
			case 'imul':
			case 'lmul':
				return '$stack[] = array_pop($stack) * array_pop($stack);';
			case 'irem':
			case 'lrem':
				list($n1, $n2) = $this->stackArrayPop($stack, 2);
				if ($n2 == 0) {
					throw new \java\lang\ArithmeticException("/ by zero");
				}
				$stack[] = $n1 % $n2;
				break;
			//case 'irem':
				break;
			case 'lneg':
			case 'ineg':
				$stack[] = -array_pop($stack);
				break;
			case 'invokestatic':
				$numArgs = count($opcode[2]['args']);
				$class = '\\'.str_replace('/', '\\', $opcode[2]['class']);
				$args = $this->stackArrayPop($stack, $numArgs);
				$ret = call_user_func_array([$class, $opcode[2]['method']], $args);
				if ($opcode[2]['return'] != 'V') {
					$stack[] = $ret;
				}
				break;
			case 'invokespecial':
				//ReflectionMethod
				if ($opcode[2]['method'] == '<init>') {
					$opcode[2]['method'] = '__construct';
				}
				$numArgs = 1 + count($opcode[2]['args']);
				$args = $this->stackArrayPop($stack, $numArgs);
				$obj = array_shift($args);
				
				$class = '\\'.str_replace('/', '\\', $opcode[2]['class']);
				$reflectMethod = new ReflectionMethod($class, $opcode[2]['method']);
				//var_dump($reflectMethod);exit;
				$ret = $reflectMethod->invokeArgs($obj, $args);
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
			case 'invokevirtual':
				$numArgs = 1 + count($opcode[2]['args']);
				$op_method = var_export($opcode[2]['method'], true);
				$code = '';'
				$args = $thisObj->stackArrayPop($stack, '.$numArgs.');
				$obj = array_shift($args);';
				
				
				$code = '$args = [];'.PHP_EOL;
				for ($ki = 0; $ki < $numArgs-1; $ki++) {
					$code .= '$args[] = array_pop($stack);'.PHP_EOL;
				}
				$code .= '$args = array_reverse($args);'.PHP_EOL;
				$code .= '$obj = array_pop($stack);'.PHP_EOL;
				/*
				if (!is_object($obj)) {
					var_dump([$method, $i, $obj, $args, $stack, '.$op_method.', \'need object\']);
					exit;
				}*/
				$op_args = var_export($opcode[2]['args'], true);
				$code .= '
				foreach ('.$op_args.' as $iArg => $arg) {
					if ($arg == \'C\' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				';
				if ($opcode[2]['return'] != 'V') {
					$code .= '$stack[] = ';
				}
				$code .= 'call_user_func_array([$obj, '.$op_method.'], $args);';
				return $code;
			case 'astore':
				$opcode[1] = 'astore_'.$opcode[2];
			case 'astore_0':
			case 'astore_1':
			case 'astore_2':
			case 'astore_3':
				$args = $this->stackArrayPop($stack, 1);
				unset($locals[explode('_', $opcode[1])[1]]);
				$locals[explode('_', $opcode[1])[1]] = &$args[0];
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
			case 'dstore_4':
			case 'fstore_0':
			case 'fstore_1':
			case 'fstore_2':
			case 'fstore_4':
				return '
				unset($locals['.explode('_', $opcode[1])[1].']);
				$locals['.explode('_', $opcode[1])[1].'] = $thisObj->stackArrayPop($stack, 1)[0];';
			case 'ldc':
				if (is_string($opcode[2])) {
					return '$stack[] = new \java\lang\String('.var_export($opcode[2], true).');';
				} else {
					return '$stack[] = '.var_export($opcode[2], true).';';
				}
			case 'ldc_w':
				$stack[] = $opcode[2];
				break;
			case 'ldc2_w':
				$stack[] = $opcode[2];
				break;
			case 'anewarray':
				//var_dump($i, $stack);readline();
				$args = $this->stackArrayPop($stack, 1);
				$stack[] = $this->newArray(null, [$args[0]]);
				break;
			case 'newarray':
				$args = $this->stackArrayPop($stack, 1);
				$stack[] = $this->newArray(0, [$args[0]]);
				break;
			case 'multianewarray':
				$val = substr($opcode[2], -1, 1) == ';'? null : 0;
				$args = $this->stackArrayPop($stack, $opcode[3]);
				$stack[] = $this->newArray($val, $args);
				//var_dump($stack);exit;
				break;
			case 'new':
				$class = str_replace('$', '_S_', str_replace('/', '\\', $opcode[2]));
				
				$reflect = new ReflectionClass($class);
				$stack[] = $reflect->newInstanceWithoutConstructor();
				//var_dump($reflect);exit;
				break;
			case 'pop':
				//array_pop($stack);
				unset($stack[count($stack)-1]);
				break;
			case 'bipush':
			case 'sipush':
				return '$stack[] = '.$opcode[2].';';
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
				break;
			case 'instanceof':
			case 'checkcast':
				break;
			case 'areturn':
			case 'ireturn':
			case 'lreturn':
			case 'dreturn':
			case 'freturn':
			case 'return':
				return 'return array_pop($stack);';
			default:
				var_dump($opcode);
				var_dump('not implemented ('.$opcode[1].')');
				exit;
		}
		$i += $opcode[0];
	}
}