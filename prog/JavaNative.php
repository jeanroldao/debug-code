<?php
function encode_int($in, $pad_to_bits=64, $little_endian=true) {
	$in = decbin($in);
	$in = str_pad($in, $pad_to_bits, '0', STR_PAD_LEFT);
	$out = '';
	for ($i = 0, $len = strlen($in); $i < $len; $i += 8) {
		$out .= chr(bindec(substr($in,$i,8)));
	}
	if($little_endian) {
		$out = strrev($out);
	}
	return $out;
}

function decode_int($data, $bits=false) {
	if ($bits === false) {
		$bits = strlen($data) * 8;
	}
	
	if($bits <= 0 ) {
		return false;
	}
	switch($bits) {
		case 8:
			$return = unpack('C',$data);
			$return = $return[1];
			break;

		case 16:
			$return = unpack('v',$data);
			$return = $return[1];
			break;

		case 24:
			$return = unpack('ca/ab/cc', $data);
			$return = $return['a'] + ($return['b'] << 8) + ($return['c'] << 16);
			break;

		case 32:
			$return = unpack('V', $data);
			$return = $return[1];
			break;

		case 48:
			$return = unpack('va/vb/vc', $data);
			$return = $return['a'] + ($return['b'] << 16) + ($return['c'] << 32);
			break;

		case 64:
			$return = unpack('Va/Vb', $data);
			$return = $return['a'] + ($return['b'] << 32);
			break;

	}
	return $return;
}

//static int java.lang.reflect.Array.getLength(Object array)
function Java_java_lang_reflect_Array_getLength($array) {
	return count($array);
}

//static java.lang.Object[] java.lang.reflect.Array.newArray(Class componentType, int length);
function Java_java_lang_reflect_Array_newArray($componentType, $length) {
	return new JavaArray($length, $componentType->getName());
}

//void java.lang.Runtime.gc()
function Java_java_lang_Runtime_gc() {
	gc_collect_cycles();
}

//public static native <T> T doPrivileged(PrivilegedExceptionAction<T> action, AccessControlContext context) throws PrivilegedActionException;
function Java_java_security_AccessController_doPrivileged($action, $context = null) {
	return $action->run();
	//echo 'doPrivileged()';readline();
}

//private static native int getClassAccessFlags(Class c);
function Java_sun_reflect_Reflection_getClassAccessFlags($c) {
	return $c->getModifiers();
}

//private static native void sun.misc.VM.initialize()
function Java_sun_misc_VM_initialize() {}

//private static native void sun.misc.Unsafe.registerNatives()
function Java_sun_misc_Unsafe_registerNatives() {}

//public static native java.lang.Class sun.reflect.Reflection.getCallerClass(int)
function Java_sun_reflect_Reflection_getCallerClass($realFramesToSkip) {
	//var_dump("getCallerClass($realFramesToSkip)");
	$i = 0;
	$backtrace = debug_backtrace();
	$max = count($backtrace);
	while (	   $i < $max 
			&& (   empty($backtrace[$i]['class']) 
				|| $backtrace[$i]['class'] == 'php_javaClass'
				|| $realFramesToSkip-- > 0)) {
		$i++;
	}
	if (!empty($backtrace[$i]['class'])) {
		return \java\lang\Clazz::forName(str_replace('\\', '.', $backtrace[$i]['class']));
	} else {
		return null;
	}
}

//public native void sun.misc.Unsafe.ensureClassInitialized(java/lang/Class)
function Java_sun_misc_Unsafe_ensureClassInitialized($class) {}


$GLOBALS['Java_sun_misc_Unsafe_staticFieldOffset_offsets'] = [];
$GLOBALS['Java_sun_misc_Unsafe_staticFieldOffset_offset_num'] = 0;
//public native long sun.misc.Unsafe.staticFieldOffset(java/lang/reflect/Field)
function Java_sun_misc_Unsafe_staticFieldOffset($field) {
	$name = $field->toString()->hashCode();
	if (isset($GLOBALS['Java_sun_misc_Unsafe_staticFieldOffset_offsets'][$name])) {
		return $GLOBALS['Java_sun_misc_Unsafe_staticFieldOffset_offsets'][$name];
	} else {
		$offset = $GLOBALS['Java_sun_misc_Unsafe_staticFieldOffset_offset_num']++;
		$GLOBALS['Java_sun_misc_Unsafe_staticFieldOffset_offsets'][$name] = $offset;
		return $offset;
	}
}

$GLOBALS['Java_sun_misc_Unsafe_nextpointer'] = 1;
$GLOBALS['Java_sun_misc_Unsafe_memory'] = "\0";
//public native long sun.misc.Unsafe.allocateMemory(long)
function Java_sun_misc_Unsafe_allocateMemory($bytes) {
	$pointer = $GLOBALS['Java_sun_misc_Unsafe_nextpointer'];
	$GLOBALS['Java_sun_misc_Unsafe_nextpointer'] += $bytes;
	$GLOBALS['Java_sun_misc_Unsafe_memory'] .= str_repeat("\0", $bytes);
	return $pointer;
}

function Java_Teste32_php(/* long */$l) {
	var_dump($l);
}

//public native void sun.misc.Unsafe.putLong(long,long)
function Java_sun_misc_Unsafe_putLong($addr, $value) {
	var_dump($value);
	var_dump(encode_int($value));
	var_dump(decode_int(encode_int($value)));
	var_dump([$addr, "$value", bindec(decbin($value)), unpack('l', pack("l", $value))]);
	//for ($i = 0; $i < ) {} 
	exit;
}

//private static native void java.util.zip.ZipFile.initIDs()
function Java_java_util_zip_ZipFile_initIDs() {}