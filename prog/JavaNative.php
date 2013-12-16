<?php
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