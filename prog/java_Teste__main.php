<?php
function & java_Teste__main($locals, $thisObj) {
$stack = [];
java_Teste__main_L0: $stack[] = &java\lang\System::$out;
java_Teste__main_L3: $stack[] = new \java\lang\String('
');
java_Teste__main_L5: $args = [];
$args[] = array_pop($stack);
$args = array_reverse($args);
$obj = array_pop($stack);

				foreach (array (
  0 => 'java/lang/String',
) as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				call_user_func_array([$obj, 'print'], $args);
java_Teste__main_L8: $stack[] = 0;
java_Teste__main_L9: 
				unset($locals[1]);
				$locals[1] = $thisObj->stackArrayPop($stack, 1)[0];
java_Teste__main_L10: $stack[] = $locals[1];
java_Teste__main_L11: $stack[] = 666;
java_Teste__main_L14: 
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 > $v2) {
					goto java_Teste__main_L54;
				}
java_Teste__main_L17: $stack[] = 0;
java_Teste__main_L18: 
				unset($locals[2]);
				$locals[2] = $thisObj->stackArrayPop($stack, 1)[0];
java_Teste__main_L19: $stack[] = $locals[2];
java_Teste__main_L20: $stack[] = $locals[1];
java_Teste__main_L21: $stack[] = 10;
java_Teste__main_L23: $stack[] = array_pop($stack) * array_pop($stack);
java_Teste__main_L24: 
				list($v1, $v2) = $thisObj->stackArrayPop($stack, 2);
				if ($v1 >= $v2) {
					goto java_Teste__main_L33;
				}
java_Teste__main_L27: $locals[2] += 1;
java_Teste__main_L30: goto java_Teste__main_L19;
java_Teste__main_L33: $stack[] = &java\lang\System::$out;
java_Teste__main_L36: $stack[] = $locals[2];
java_Teste__main_L37: $args = [];
$args[] = array_pop($stack);
$args = array_reverse($args);
$obj = array_pop($stack);

				foreach (array (
  0 => 'I',
) as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				call_user_func_array([$obj, 'print'], $args);
java_Teste__main_L40: $stack[] = &java\lang\System::$out;
java_Teste__main_L43: $stack[] = new \java\lang\String('
');
java_Teste__main_L45: $args = [];
$args[] = array_pop($stack);
$args = array_reverse($args);
$obj = array_pop($stack);

				foreach (array (
  0 => 'java/lang/String',
) as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				call_user_func_array([$obj, 'print'], $args);
java_Teste__main_L48: $locals[1] += 1;
java_Teste__main_L51: goto java_Teste__main_L10;
java_Teste__main_L54: $stack[] = &java\lang\System::$out;
java_Teste__main_L57: $stack[] = new \java\lang\String('fim do mundo? D: ');
java_Teste__main_L59: $args = [];
$args[] = array_pop($stack);
$args = array_reverse($args);
$obj = array_pop($stack);

				foreach (array (
  0 => 'java/lang/String',
) as $iArg => $arg) {
					if ($arg == 'C' && is_int($args[$iArg])) {
						$args[$iArg] = chr($args[$iArg]);
					}
				}
				call_user_func_array([$obj, 'println'], $args);
java_Teste__main_L62: return array_pop($stack);
}