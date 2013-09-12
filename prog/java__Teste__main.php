<?php 
 function & java__Teste__main($locals, $thisObj) {
$stack = [];
java__Teste__main_0: $stack[] = &java\lang\System::$out;
java__Teste__main_3: $stack[] = new \java\lang\String('
');
java__Teste__main_5: $args = $thisObj->stackArrayPop($stack, 2);
$obj = array_shift($args);

				$opcode_args = array (
  0 => 'java/lang/String',
);
				if (!is_object($obj)) {
					var_dump([$method, $i, $obj, $args, $stack, 'print', 'need object']);
					exit;
				}
				foreach ($opcode_args as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				$ret = call_user_func_array([$obj, 'print'], $args);
				
java__Teste__main_8: $stack[] = 0;
java__Teste__main_9: unset($locals[1]); list($locals[1]) = $thisObj->stackArrayPop($stack, 1);
java__Teste__main_10: $stack[] = $locals[1];
java__Teste__main_11: $stack[] = 666;
java__Teste__main_14: 
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 > $v2) {
					goto java__Teste__main_54;
				}
java__Teste__main_17: $stack[] = 0;
java__Teste__main_18: unset($locals[2]); list($locals[2]) = $thisObj->stackArrayPop($stack, 1);
java__Teste__main_19: $stack[] = $locals[2];
java__Teste__main_20: $stack[] = $locals[1];
java__Teste__main_21: $stack[] = 10;
java__Teste__main_23: $stack[] = array_pop($stack) * array_pop($stack);
java__Teste__main_24: 
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 >= $v2) {
					goto java__Teste__main_33;
				}
java__Teste__main_27: $locals[2] += 1;
java__Teste__main_30: goto java__Teste__main_19;
java__Teste__main_33: $stack[] = &java\lang\System::$out;
java__Teste__main_36: $stack[] = $locals[2];
java__Teste__main_37: $args = $thisObj->stackArrayPop($stack, 2);
$obj = array_shift($args);

				$opcode_args = array (
  0 => 'I',
);
				if (!is_object($obj)) {
					var_dump([$method, $i, $obj, $args, $stack, 'print', 'need object']);
					exit;
				}
				foreach ($opcode_args as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				$ret = call_user_func_array([$obj, 'print'], $args);
				
java__Teste__main_40: $stack[] = &java\lang\System::$out;
java__Teste__main_43: $stack[] = new \java\lang\String('
');
java__Teste__main_45: $args = $thisObj->stackArrayPop($stack, 2);
$obj = array_shift($args);

				$opcode_args = array (
  0 => 'java/lang/String',
);
				if (!is_object($obj)) {
					var_dump([$method, $i, $obj, $args, $stack, 'print', 'need object']);
					exit;
				}
				foreach ($opcode_args as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				$ret = call_user_func_array([$obj, 'print'], $args);
				
java__Teste__main_48: $locals[1] += 1;
java__Teste__main_51: goto java__Teste__main_10;
java__Teste__main_54: $stack[] = &java\lang\System::$out;
java__Teste__main_57: $stack[] = new \java\lang\String('fim do mundo? D: ');
java__Teste__main_59: $args = $thisObj->stackArrayPop($stack, 2);
$obj = array_shift($args);

				$opcode_args = array (
  0 => 'java/lang/String',
);
				if (!is_object($obj)) {
					var_dump([$method, $i, $obj, $args, $stack, 'println', 'need object']);
					exit;
				}
				foreach ($opcode_args as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				$ret = call_user_func_array([$obj, 'println'], $args);
				
java__Teste__main_62: return end($stack);
} ?>