<?php

//static int java.lang.reflect.Array.getLength(Object array)
function Java_java_lang_reflect_Array_getLength($array) {
	return count($array);
}

//static java.lang.Object[] java.lang.reflect.Array.newArray(Class componentType, int length);
function Java_java_lang_reflect_Array_newArray($componentType, $length) {
	return new JavaArray($length, $componentType->getName());
}

//private static native java.lang.Object sun.reflect.NativeMethodAccessorImpl.invoke0(java.lang.reflect.Method,java.lang.Object,java.lang.Object[])
function Java_sun_reflect_NativeMethodAccessorImpl_invoke0($method, $object, $args) {
	//var_dump($method->_java_type);
	
	$args = $args !== null ? $args->toArray() : [];
	
	if (property_exists($method, '_java_type')) {
		$opcode = [2 => ['type' => $method->_java_type]];
		$argsType = \php_javaClass::getArgsType($method->_java_type);
		foreach ($argsType['args'] as $iArg => $argType) {
			if (strlen($argType) == 1) {
				$args[$iArg] = strval($args[$iArg]);
			}
		}
	} else {
		$opcode = null;
		$argsType = ['return' => ''];
	}
	
	$methodName = $method instanceof \java\lang\reflect\Constructor ? '<init>': $method->getName() . '';
	if ($object !== null) {
		$ret = $object->__call($methodName, $args, $opcode);
	} else {
		$class = \php_javaClass::convertNameJavaToPhp($method->getDeclaringClass()->getName());
		$ret = $class::__callstatic($methodName, $args, $opcode);
	}
	
	switch ($argsType['return']) {
		case 'Z': 
			$ret = \java\lang\Boolean::valueOf($ret);
			break;
		case 'C':
			$ret = \java\lang\Character::valueOf($ret);
			break;
		case 'F':
			$ret = \java\lang\Float::valueOf($ret);
			break;
		case 'D':
			$ret = \java\lang\Double::valueOf($ret);
			break;
		case 'B':
			$ret = \java\lang\Byte::valueOf($ret);
			break;
		case 'S':
			$ret = \java\lang\Short::valueOf($ret);
			break;
		case 'I':
			$ret = \java\lang\Integer::valueOf($ret);
			break;
		case 'J':
			$ret = \java\lang\Long::valueOf($ret);
			break;
		case 'V':
			$ret = null;
			break;
	}
	return $ret;
}

//private static native java.lang.Object sun.reflect.NativeConstructorAccessorImpl.newInstance0(java.lang.reflect.Constructor,java.lang.Object[])
function Java_sun_reflect_NativeConstructorAccessorImpl_newInstance0($constructor, $args) {
	$instance = $constructor->getDeclaringClass()->getRefClass()->newInstanceWithoutConstructor();
	Java_sun_reflect_NativeMethodAccessorImpl_invoke0($constructor, $instance, $args);
	return $instance;
}

//void java.lang.Runtime.gc()
function Java_java_lang_Runtime_gc() {
	gc_collect_cycles();
}

//public int java.lang.Runtime.availableProcessors()
function Java_java_lang_Runtime_availableProcessors() {
	return 1;
}

//public static native <T> T doPrivileged(PrivilegedExceptionAction<T> action, AccessControlContext context) throws PrivilegedActionException;
function Java_java_security_AccessController_doPrivileged($action, $context = null) {
	return $action->run();
	//echo 'doPrivileged()';readline();
}

//private static native java.security.AccessControlContext java.security.AccessController.getStackAccessControlContext()
function Java_java_security_AccessController_getStackAccessControlContext() {
	return null; // o.O
}

//private static native int getClassAccessFlags(Class c);
function Java_sun_reflect_Reflection_getClassAccessFlags($c) {
	return $c->getModifiers();
}

//private static native void sun.misc.VM.initialize()
function Java_sun_misc_VM_initialize() {}

//public static native java.lang.Class sun.reflect.Reflection.getCallerClass(int)
function Java_sun_reflect_Reflection_getCallerClass($realFramesToSkip = 0) {
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
		$class = \java\lang\Clazz::forName(str_replace('\\', '.', $backtrace[$i]['class']));
	} else {
		$class = null;
	}
	//println($class);
	//println($class->getClassLoader());
	return $class;
}

//private static native java.lang.String java.util.TimeZone.getSystemGMTOffsetID()
function Java_java_util_TimeZone_getSystemGMTOffsetID() {
	//$tz = date_default_timezone_get();
	//var_dump($tz);
	//exit;
	return jstring("GMT-03:00");//LOL
}

// ---- java.lang.ProcessImpl (Windows) ----

//private static native int java.lang.ProcessImpl.getStillActive()
// Returns Windows STILL_ACTIVE (259 = 0x103) — the exit code meaning "still running"
function Java_java_lang_ProcessImpl_getStillActive() {
	return 259;
}

//private static native int java.lang.ProcessImpl.getExitCodeProcess(long handle)
function Java_java_lang_ProcessImpl_getExitCodeProcess($handle) {
	return 0; // assume success / exited cleanly
}

//private static native long java.lang.ProcessImpl.create(String cmd, String envBlock, String dir, long[] stdHandles, boolean redirectErrorStream)
function Java_java_lang_ProcessImpl_create($cmd, $envBlock, $dir, $stdHandles, $redirectErrorStream) {
	return 1; // dummy process handle
}

//private static native boolean java.lang.ProcessImpl.closeHandle(long handle)
function Java_java_lang_ProcessImpl_closeHandle($handle) {
	return true;
}

//private static native boolean java.lang.ProcessImpl.terminateProcess(long handle)
function Java_java_lang_ProcessImpl_terminateProcess($handle) {
	return true;
}

//private static native boolean java.lang.ProcessImpl.isProcessAlive(long handle)
function Java_java_lang_ProcessImpl_isProcessAlive($handle) {
	return false; // process has finished
}

//private static native boolean java.lang.ProcessImpl.waitForInterruptibly(long handle)
function Java_java_lang_ProcessImpl_waitForInterruptibly($handle) {
	return true;
}

//private static native boolean java.lang.ProcessImpl.waitForTimeoutInterruptibly(long handle, long timeout)
function Java_java_lang_ProcessImpl_waitForTimeoutInterruptibly($handle, $timeout) {
	return true;
}

//private static native long java.lang.ProcessImpl.openForAtomicAppend(String path)
function Java_java_lang_ProcessImpl_openForAtomicAppend($path) {
	return 0;
}

// ---- sun.misc.Unsafe ----

//private static native void sun.misc.Unsafe.registerNatives()
function Java_sun_misc_Unsafe_registerNatives() {}

//public native void sun.misc.Unsafe.ensureClassInitialized(java.lang.Class)
function Java_sun_misc_Unsafe_ensureClassInitialized($class) {
	//var_dump('Java_sun_misc_Unsafe_ensureClassInitialized');
	ensureClassInitialized($class->getName().'');
}

//public native java.lang.Object sun.misc.Unsafe.getObjectVolatile(java.lang.Object,long)
function Java_sun_misc_Unsafe_getObjectVolatile($obj, $offset) {
	return Java_sun_misc_Unsafe_getObject($obj, $offset);
}

//public native int sun.misc.Unsafe.getIntVolatile(java.lang.Object,long)
function Java_sun_misc_Unsafe_getIntVolatile($obj, $offset) {
	//var_dump($obj, $offset);
	return Java_sun_misc_Unsafe_getObject($obj, $offset);
}

//public native java.lang.Object sun.misc.Unsafe.staticFieldBase(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_staticFieldBase($field) {
	//return jstring("opa");
	//var_dump($this);
	//var_dump($field->getDeclaringClass()->toString());
	//exit;
	return $field->getDeclaringClass();
}

//public native void sun.misc.Unsafe.putObject(java.lang.Object,long,java.lang.Object)
function Java_sun_misc_Unsafe_putObject0($o, $offset, $x) {
	if ($obj instanceof \java\lang\Clazz) {
		$className = $obj->getRefClass()->getName();
		$field = array_keys(get_class_vars($className))[$offset];
		$className::$$field = $x;
		return;
	}
	var_dump('Java_sun_misc_Unsafe_getObject', $obj, $offset, $x);
	readline();
	exit;
}

//public native java.lang.Object sun.misc.Unsafe.getObject(java.lang.Object,long)
function Java_sun_misc_Unsafe_getObject($obj, $offset) {
	//var_dump("$obj", $offset);exit;
	if ($obj instanceof \java\lang\Clazz) {
		$className = $obj->getRefClass()->getName();
		$field = array_keys(get_class_vars($className))[$offset];
		ensureClassInitialized($className);
		//var_dump("$className::$$field");
		//var_dump($className::$$field);exit;
		return $className::$$field;
	}
	var_dump('Java_sun_misc_Unsafe_getObject', $obj, $offset);
	exit;
}

//public native long sun.misc.Unsafe.staticFieldOffset(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_staticFieldOffset($field) {
	$className = $field->getDeclaringClass()->getRefClass()->getName();
	$fieldName = $field->getName()->toString().'';
	
	//var_dump($className);
	$vars = get_class_vars($className);
	//var_dump($vars);
	$keys = array_keys($vars);
	//var_dump($keys);
	return array_search($fieldName, $keys);
}

//public native long sun.misc.Unsafe.objectFieldOffset(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_objectFieldOffset($field) {
	return Java_sun_misc_Unsafe_staticFieldOffset($field);
}

//public final native boolean sun.misc.Unsafe.compareAndSwapInt(java.lang.Object,long,int,int)
function Java_sun_misc_Unsafe_compareAndSwapInt($obj, $offset, $expected, $value) {
	//var_dump($o, $offset, $expect, $value);
	
	$className = get_class($obj);
	$field = array_keys(get_class_vars($className))[$offset];
	if ($obj->$field === $expected) {
		$obj->$field = $value;
		return true;
	} else {
		return false;
	}
}

// Flat memory model: single string where $mem[$addr] = byte char.
// Address 0 is reserved (null pointer placeholder).
$Java_sun_misc_Unsafe_memory = "\0"; // index 0 = null pointer
$Java_sun_misc_Unsafe_nextAddr = 1;

//public native long sun.misc.Unsafe.allocateMemory(long)
function Java_sun_misc_Unsafe_allocateMemory($bytes) {
	global $Java_sun_misc_Unsafe_memory, $Java_sun_misc_Unsafe_nextAddr;
	$addr = $Java_sun_misc_Unsafe_nextAddr;
	$Java_sun_misc_Unsafe_nextAddr += $bytes;
	$Java_sun_misc_Unsafe_memory .= str_repeat("\0", $bytes);
	return $addr;
}

function packLong($value) {
	$l1 = str_pad(dechex(bcdiv($value, 0x100000000, 0)), 8, '0', STR_PAD_LEFT);
	$l2 = str_pad(dechex(bcmod($value, 0x100000000)), 8, '0', STR_PAD_LEFT);
	
	$b1 = pack("H8", $l1);
	$b2 = pack("H8", $l2);
	
	return "$b1$b2";
}

function unpackLong($binarydata) {
	if (strlen($binarydata) != 8) {
		var_dump('unpackLong error, need 8 bytes for long');
	}
	$l1 = unpack("H8hex", substr($binarydata, 0, 4))['hex'];
	$l2 = unpack("H8hex", substr($binarydata, 4, 4))['hex'];
	$result = bcadd(bcmul(hexdec($l1), 0x100000000), hexdec($l2));
	while (bccomp($result, '9223372036854775807') === 1) {
		$result = bcsub($result, '18446744073709551616');
	}
	return $result;
}

function packDouble($value) {
	return pack('d', $value);
}

function unpackDouble($binarydata) {
	return unpack('dd', $binarydata)['d'];
}

//public native void sun.misc.Unsafe.putLong(long,long)
function Java_sun_misc_Unsafe_putLong($addr, $value) {
	global $Java_sun_misc_Unsafe_memory;
	$binary = packLong($value);
	$addr = (int)$addr;
	for ($i = 0; $i < 8; $i++) {
		$Java_sun_misc_Unsafe_memory[$addr + $i] = $binary[$i];
	}
}

//public native void sun.misc.Unsafe.putByte(long,byte)
function Java_sun_misc_Unsafe_putByte($addr, $byte) {
	global $Java_sun_misc_Unsafe_memory;
	$Java_sun_misc_Unsafe_memory[(int)$addr] = chr((int)$byte & 0xFF);
}

//public native byte sun.misc.Unsafe.getByte(long)
function Java_sun_misc_Unsafe_getByte($addr) {
	global $Java_sun_misc_Unsafe_memory;
	return ord($Java_sun_misc_Unsafe_memory[(int)$addr]);
}

//public native long sun.misc.Unsafe.getLong(long)
function Java_sun_misc_Unsafe_getLong($addr) {
	global $Java_sun_misc_Unsafe_memory;
	$binary = substr($Java_sun_misc_Unsafe_memory, (int)$addr, 8);
	$val = unpackLong($binary);
	fwrite(STDERR, "getLong($addr)=$val\n");
	return $val;
}

//public native int sun.misc.Unsafe.getInt(long)
function Java_sun_misc_Unsafe_getInt($addr) {
	global $Java_sun_misc_Unsafe_memory;
	$a = (int)$addr;
	$val = (ord($Java_sun_misc_Unsafe_memory[$a]) << 24)
	     | (ord($Java_sun_misc_Unsafe_memory[$a+1]) << 16)
	     | (ord($Java_sun_misc_Unsafe_memory[$a+2]) << 8)
	     |  ord($Java_sun_misc_Unsafe_memory[$a+3]);
	// convert to signed 32-bit
	if ($val >= 0x80000000) $val -= 0x100000000;
	fwrite(STDERR, "getInt($addr)=$val\n");
	return $val;
}

//public native short sun.misc.Unsafe.getShort(long)
function Java_sun_misc_Unsafe_getShort($addr) {
	global $Java_sun_misc_Unsafe_memory;
	$a = (int)$addr;
	$val = (ord($Java_sun_misc_Unsafe_memory[$a]) << 8)
	     |  ord($Java_sun_misc_Unsafe_memory[$a+1]);
	if ($val >= 0x8000) $val -= 0x10000;
	fwrite(STDERR, "getShort($addr)=$val\n");
	return $val;
}

//public native void sun.misc.Unsafe.putInt(long,int)
function Java_sun_misc_Unsafe_putInt($addr, $value) {
	global $Java_sun_misc_Unsafe_memory;
	$a = (int)$addr;
	$v = (int)$value;
	$Java_sun_misc_Unsafe_memory[$a]   = chr(($v >> 24) & 0xFF);
	$Java_sun_misc_Unsafe_memory[$a+1] = chr(($v >> 16) & 0xFF);
	$Java_sun_misc_Unsafe_memory[$a+2] = chr(($v >> 8)  & 0xFF);
	$Java_sun_misc_Unsafe_memory[$a+3] = chr( $v        & 0xFF);
}

//public native int sun.misc.Unsafe.pageSize()
function Java_sun_misc_Unsafe_pageSize() {
	return 1 << 8;
}

//public native int sun.misc.Unsafe.addressSize()
function Java_sun_misc_Unsafe_addressSize() {
	return 4; // 32-bit
}

//public native int sun.misc.Unsafe.arrayBaseOffset(Class)
function Java_sun_misc_Unsafe_arrayBaseOffset($arrayClass) {
	return 0;
}

//public native int sun.misc.Unsafe.arrayIndexScale(Class)
function Java_sun_misc_Unsafe_arrayIndexScale($arrayClass) {
	// Return element size based on component type name
	$name = '';
	if (is_object($arrayClass) && method_exists($arrayClass, 'instance_getName')) {
		$name = $arrayClass->instance_getName() . '';
	}
	switch ($name) {
		case '[B': case '[Z': return 1;  // byte[], boolean[]
		case '[S': case '[C': return 2;  // short[], char[]
		case '[I': case '[F': return 4;  // int[], float[]
		case '[J': case '[D': return 8;  // long[], double[]
		default:              return 4;  // Object[] (reference size)
	}
}

//public native void sun.misc.Unsafe.setMemory(long,long,byte)
function Java_sun_misc_Unsafe_setMemory($addr, $bytes, $value) {
	global $Java_sun_misc_Unsafe_memory;
	$ch = chr((int)$value & 0xFF);
	$addr = (int)$addr;
	for ($i = 0; $i < $bytes; $i++) {
		$Java_sun_misc_Unsafe_memory[$addr + $i] = $ch;
	}
}

//public native void sun.misc.Unsafe.freeMemory(long)
function Java_sun_misc_Unsafe_freeMemory($addr) {
	// no-op: flat string model, memory is never reclaimed
}

//public native void sun.misc.Unsafe.copyMemory(Object,long,Object,long,long)
//public native void sun.misc.Unsafe.copyMemory(long,long,long)
function Java_sun_misc_Unsafe_copyMemory($srcBase, $srcOffset, $destBase, $destOffset = null, $bytes = null) {
	global $Java_sun_misc_Unsafe_memory;

	if ($destOffset === null) {
		// 3-arg form: copyMemory(srcAddr, destAddr, bytes)
		$srcAddr  = (int)$srcBase;
		$destAddr = (int)$srcOffset;
		$n        = (int)$destBase;
		$chunk = substr($Java_sun_misc_Unsafe_memory, $srcAddr, $n);
		for ($i = 0; $i < $n; $i++) {
			$Java_sun_misc_Unsafe_memory[$destAddr + $i] = isset($chunk[$i]) ? $chunk[$i] : "\0";
		}
		return;
	}

	// 5-arg form: copyMemory(srcBase, srcOffset, destBase, destOffset, bytes)
	$n = (int)$bytes;

	// Read source bytes into a string chunk
	if ($srcBase === null) {
		$chunk = substr($Java_sun_misc_Unsafe_memory, (int)$srcOffset, $n);
	} elseif ($srcBase instanceof \JavaArray) {
		$off = (int)$srcOffset;
		$chunk = '';
		for ($i = 0; $i < $n; $i++) {
			$chunk .= chr((int)$srcBase[$off + $i]);
		}
	} else {
		$chunk = substr($Java_sun_misc_Unsafe_memory, (int)$srcOffset, $n);
	}

	// Write chunk to destination
	if ($destBase === null) {
		$d = (int)$destOffset;
		for ($i = 0; $i < $n; $i++) {
			$Java_sun_misc_Unsafe_memory[$d + $i] = isset($chunk[$i]) ? $chunk[$i] : "\0";
		}
	} elseif ($destBase instanceof \JavaArray) {
		$d = (int)$destOffset;
		for ($i = 0; $i < $n; $i++) {
			$destBase[$d + $i] = ord(isset($chunk[$i]) ? $chunk[$i] : "\0");
		}
	} else {
		$d = (int)$destOffset;
		for ($i = 0; $i < $n; $i++) {
			$Java_sun_misc_Unsafe_memory[$d + $i] = isset($chunk[$i]) ? $chunk[$i] : "\0";
		}
	}
}

$Java_java_util_zip_ZipFile_openID = 1; //start at 1
$Java_java_util_zip_ZipFile_openFiles = [null];

//private static native long java.util.zip.ZipFile.open(java.lang.String,int,long,boolean)
function Java_java_util_zip_ZipFile_open($name, $mode, $lastModified, $usemmap) {
	global $Java_java_util_zip_ZipFile_openID, $Java_java_util_zip_ZipFile_openFiles;
	//var_dump($name, $mode, $lastModified, $usemmap);
	//return openJavaFile($name, $mode);
	$zip = new ZipArchive();
	$filename = "$name";

	if ($zip->open($filename /*, ZipArchive::CREATE*/) !== true) {
		//var_dump($mode);
		//exit("cannot open <$filename>\n");
		throw new java\util\zip\ZipException(jstring("cannot open <$filename>"));
	}
	$jzfile = $Java_java_util_zip_ZipFile_openID++;
	$Java_java_util_zip_ZipFile_openFiles[$jzfile] = $zip;
	
	return $jzfile;
}

//private static native int java.util.zip.ZipFile.getTotal(long)
function Java_java_util_zip_ZipFile_getTotal($jzfile) {
	global $Java_java_util_zip_ZipFile_openFiles;
	if (isset($Java_java_util_zip_ZipFile_openFiles[$jzfile])) {
		return $Java_java_util_zip_ZipFile_openFiles[$jzfile]->numFiles;
	} else {
		var_dump('zipfile not open?');
		var_dump($Java_java_util_zip_ZipFile_openFiles, $jzfile);
		exit;
	}
	//return fstat($jzfile)['size'];
}

//private static native long java.util.zip.ZipFile.getEntry(long,java.lang.String,boolean)
function Java_java_util_zip_ZipFile_getEntry($jzfile, $name, $addSlash) {
	global $Java_java_util_zip_ZipFile_openID, $Java_java_util_zip_ZipFile_openFiles;

	$entry = $Java_java_util_zip_ZipFile_openFiles[$jzfile]->getFromName("$name");
	
	$jzentry = $Java_java_util_zip_ZipFile_openID++;
	$Java_java_util_zip_ZipFile_openFiles[$jzentry] = $entry;
	return $jzentry;
}

//private static native long java.util.zip.ZipFile.getCSize(long)
function Java_java_util_zip_ZipFile_getCSize($jzentry) {
	global $Java_java_util_zip_ZipFile_openFiles;
	
	return strlen($Java_java_util_zip_ZipFile_openFiles[$jzentry]);
}

//private static native long java.util.zip.ZipFile.getSize(long)
function Java_java_util_zip_ZipFile_getSize($jzentry) {
	global $Java_java_util_zip_ZipFile_openFiles;
	
	return strlen($Java_java_util_zip_ZipFile_openFiles[$jzentry]);
}

//private static native int java.util.zip.ZipFile.getMethod(long)
function Java_java_util_zip_ZipFile_getMethod($jzfile) {
	//global $Java_java_util_zip_ZipFile_openFiles;
	//var_dump($Java_java_util_zip_ZipFile_openFiles[$jzfile]);
	//exit;
	//return \java\util\zip\ZipFile::$STORED;
	return \java\util\zip\ZipEntry::$STORED;
}

//private static native int java.util.zip.ZipFile.read(long,long,long,byte[],int,int)
function Java_java_util_zip_ZipFile_read($jzfile, $jzentry, $pos, $b, $off, $len) {
	global $Java_java_util_zip_ZipFile_openFiles;
	$toRead = substr($Java_java_util_zip_ZipFile_openFiles[$jzentry], $pos, $len);
	$len = strlen($toRead);
	if ($len == 0) {
		return -1;
	}
	for ($i = 0; $i < $len; $i++) {
		$b[$i + $off] = ord($toRead[$i]);
	}
	return $len;
}

//private static native void java.util.zip.ZipFile.freeEntry(long,long)
function Java_java_util_zip_ZipFile_freeEntry($l1, $l2) {
	global $Java_java_util_zip_ZipFile_openFiles;
	unset($Java_java_util_zip_ZipFile_openFiles[$l2]);
}

//private native void java.util.zip.ZipEntry.initFields(long)
function Java_java_util_zip_ZipEntry_initFields($jzentry) {
	//var_dump($jzentry);
}

//private native java.lang.String[] java.util.jar.JarFile.getMetaInfEntryNames()
function Java_java_util_jar_JarFile_getMetaInfEntryNames() {
	$manifest = php_javaClass::readJarManifest($this->getName().'');
	//$lines = new \JavaArray(count($manifest), 'java.lang.String');
	$lines = [];
	foreach ($manifest as $name => $value) {
		$lines[] = jstring("$name: $value");
	}
	$lines = \JavaArray::fromArray($lines, 'java.lang.String');
	return $lines;
}

//public static native java.io.FileSystem java.io.FileSystem.getFileSystem()
function Java_java_io_FileSystem_getFileSystem() {
	return new \java\io\WinNTFileSystem();
}

//private static native void java.io.Win32FileSystem.initIDs()
function Java_java_io_Win32FileSystem_initIDs() {}

//private static native void java.io.WinNTFileSystem.initIDs()
function Java_java_io_WinNTFileSystem_initIDs() {}

//public native int java.io.WinNTFileSystem.getBooleanAttributes(java.io.File)
function Java_java_io_WinNTFileSystem_getBooleanAttributes($file) {
	//java/io/FileSystem::$BA_DIRECTORY;
	/*
	BA_EXISTS    = 0x01;
    BA_REGULAR   = 0x02;
    BA_DIRECTORY = 0x04;
    BA_HIDDEN    = 0x08;
	
	*/
	$path = $file->instance_getPath();
	$file = "$path";
	$mode = 0;
	if (file_exists($file)) {
		$mode |= 0x01;
	}
	if (is_file($file)) {
		$mode |= 0x02;
	}
	if (is_dir($file)) {
		$mode |= 0x04;
	}
	return $mode;
}

//protected native java.lang.String java.io.WinNTFileSystem.canonicalize0(java.lang.String)
function Java_java_io_WinNTFileSystem_canonicalize0($relative_path) {

	$real_path = realpath($relative_path);
	
	if ($real_path === false) {
		$real_path = $relative_path;
		/*
		if (file_exists($relative_path)) {
			var_dump('Java_java_io_WinNTFileSystem_canonicalize0', $relative_path);
			exit;
		}
		file_put_contents($relative_path, '');
		$real_path = realpath($relative_path);
		unlink($relative_path);
		//*/
	}
	
	return jstring($real_path);
}

//protected native java.lang.String java.io.WinNTFileSystem.canonicalizeWithPrefix0(java.lang.String,java.lang.String)
function Java_java_io_WinNTFileSystem_canonicalizeWithPrefix0($canonicalPrefix, $pathWithCanonicalPrefix) {
	return $pathWithCanonicalPrefix;
}

//public native long java.io.WinNTFileSystem.getLength(java.io.File)
function Java_java_io_WinNTFileSystem_getLength($file) {
	$file = '' . $file->instance_getPath();
	if (!file_exists($file)) {
		return '0';
	}
	return filesize($file);
}

//public native long java.io.WinNTFileSystem.getLastModifiedTime(java.io.File)
function Java_java_io_WinNTFileSystem_getLastModifiedTime($file) {
	$file = '' . $file->instance_getPath();
	if (!file_exists($file)) {
		return filemtime(".");
	}
	return filemtime($file);
}

//public native java.lang.String[] java.io.WinNTFileSystem.list(java.io.File)
function Java_java_io_WinNTFileSystem_list($dir) {
	$dir = '' . $dir->instance_getPath();
	$list = [];
	foreach (scandir($dir) as $file) {
		if (!in_array($file, ['.', '..'])) {
			$list[] = jstring(realpath("$dir/$file"));
		}
	}
	return \JavaArray::fromArray($list, 'java.lang.String');
}

//public native boolean java.io.WinNTFileSystem.checkAccess(java.io.File,int)
function Java_java_io_WinNTFileSystem_checkAccess($file, $access) {
	//reminder
	/*
	ACCESS_READ    = 0x04;
    ACCESS_WRITE   = 0x02;
    ACCESS_EXECUTE = 0x01;	
	*/
	$result = false;
	println($file);
	if (($access & 0x04) == 0x04) {
		//var_dump('ACCESS_READ');
		return is_readable($file);
	}
	var_dump("$file", $access,'Java_java_io_WinNTFileSystem_checkAccess');
	exit;
}

function openJavaFile($file, $mode) {
	//O_RDONLY = 1; 'r'
	//O_RDWR =   2; 'rw'
	//O_SYNC =   4; 'rws'
	//O_DSYNC =  8; 'rwd'
	switch ($mode) {
		case 1:
			$mode = 'r';
			break;
		case 2:
			$mode = 'r+';
			break;
		case 4:
			$mode = 'rws';
		case 8:
			$mode = 'rwd';
		default:
			var_dump('unknown mode: ' . $mode);
			var_dump('Java_java_io_RandomAccessFile_open');
			exit;
	}
	
	//var_dump("$file", $mode);
	$handle = fopen("$file", $mode);
	if ($handle === false) {
		var_dump("error fopen");
		exit;
	}
	return $handle;
}

//private native void java.io.FileOutputStream.open(java.lang.String)
function Java_java_io_FileOutputStream_open($filename) {
	//var_dump($this, "$filename");
	$handle = fopen("$filename", 'w');
	if ($handle === false) {
		var_dump("error fopen");
		exit;
	}
	$this->fd->handle = $handle;
}

//private native void java.io.FileOutputStream.writeBytes(byte[],int,int)
function Java_java_io_FileOutputStream_writeBytes($b, $offset, $len) {
	//var_dump($b, $offset, $len);
	$data = '';
	for ($i = 0; $i < $len; $i++) {
		$data .= chr($b[$offset + $i]);
	}
	fwrite($this->fd->handle, $data);
}

//private native void java.io.FileOutputStream.close0()
function Java_java_io_FileOutputStream_close0() {
	fclose($this->fd->handle);
}

//private static native java.lang.String java.lang.ProcessEnvironment.environmentBlock()
function Java_java_lang_ProcessEnvironment_environmentBlock() {
	return jstring('');
}

//private native void java.io.RandomAccessFile.open(java.lang.String,int)
function Java_java_io_RandomAccessFile_open($file, $mode) {
	//var_dump($this->fd->handle);
	$this->fd->handle = openJavaFile($file, $mode);
}
//private native void java.io.RandomAccessFile.close0()
function Java_java_io_RandomAccessFile_close0() {
	fclose($this->fd->handle);
}

//private native int java.io.RandomAccessFile.readBytes(byte[],int,int)
function Java_java_io_RandomAccessFile_readBytes($b, $off, $len) {
	$data = fread($this->fd->handle, $len);
	$n = strlen($data);
	if ($n === 0) return -1;
	for ($i = 0; $i < $n; $i++) {
		$b[(int)$off + $i] = ord($data[$i]);
	}
	return $n;
}

//private native int java.io.RandomAccessFile.read0()
function Java_java_io_RandomAccessFile_read0() {
	$c = fgetc($this->fd->handle);
	return $c === false ? -1 : ord($c);
}

//static native int sun.nio.ch.FileDispatcher.read0(java.io.FileDescriptor,long,int)
function Java_sun_nio_ch_FileDispatcher_read0($fd, $addr, $len) {
	global $Java_sun_misc_Unsafe_memory;
	fwrite(STDERR, "read0: addr=$addr len=$len pos=".ftell($fd->handle)."\n");
	$data = fread($fd->handle, $len);
	$len = strlen($data);
	$addr = (int)$addr;
	for ($i = 0; $i < $len; $i++) {
		$Java_sun_misc_Unsafe_memory[$addr + $i] = $data[$i];
	}
	fwrite(STDERR, "read0 done: got $len bytes\n");
	return $len;
}

//static native int sun.nio.ch.FileDispatcher.write0(java.io.FileDescriptor,long,int)
function Java_sun_nio_ch_FileDispatcher_write0($fd, $addr, $len) {
	global $Java_sun_misc_Unsafe_memory;
	$data = substr($Java_sun_misc_Unsafe_memory, (int)$addr, $len);
	$written = fwrite($fd->handle, $data, $len);
	if ($written === false) {
		var_dump('Java_sun_nio_ch_FileDispatcher_write0 error');
		exit;
	}
	return $written;
}

//private static native long java.io.FileDescriptor.set(int)
function Java_java_io_FileDescriptor_set($fd) {

	switch ($fd) {
		case 0: return STDIN;
		case 1: return STDERR;
		case 2: return STDOUT;
		
		default:
			var_dump($int);
			var_dump('Java_java_io_FileDescriptor_set');
			exit;
	}
}

//private static native java.lang.ClassLoader java.sql.DriverManager.getCallerClassLoader()
function Java_java_sql_DriverManager_getCallerClassLoader() {
	//return \java\lang\Thread::currentThread()->getContextClassLoader();
	return \java\lang\ClassLoader::getSystemClassLoader();
}

//private static native void java.io.ObjectStreamClass.initNative()
function Java_java_io_ObjectStreamClass_initNative() {}

//private static native long sun.nio.ch.FileChannelImpl.initIDs()
function Java_sun_nio_ch_FileChannelImpl_initIDs() {
	return '1'; 
}

//private native long sun.nio.ch.FileChannelImpl.position0(java.io.FileDescriptor,long)
function Java_sun_nio_ch_FileChannelImpl_position0($fd, $offset) {
	if ($offset !== '-1') {
		fseek($fd->handle, $offset);
	}
	$pos = ftell($fd->handle);
	fwrite(STDERR, "position0: offset=$offset => pos=$pos\n");
	return $pos;
}

//private native long sun.nio.ch.FileChannelImpl.size0(java.io.FileDescriptor)
function Java_sun_nio_ch_FileChannelImpl_size0($fd) {
	$fp = $fd->handle;
	$pos = ftell($fp);
	fseek($fp, 0, SEEK_END);
	$size = ftell($fp);
	fseek($fp, $pos);
	fwrite(STDERR, "size0: $size bytes\n");
	return $size;
}

//native int sun.nio.ch.FileChannelImpl.lock0(java.io.FileDescriptor,boolean,long,long,boolean)
function Java_sun_nio_ch_FileChannelImpl_lock0($fd, $blocking, $pos, $size, $shared) {
	//var_dump($fd, $blocking, $pos, $size, $shared);
	//var_dump(\sun\nio\ch\FileChannelImpl::$RET_EX_LOCK);
	//var_dump(\sun\nio\ch\FileChannelImpl::$LOCKED);
	//var_dump(\sun\nio\ch\FileChannelImpl::$NO_LOCK);// = -1
	
	if ($shared) {
		$lock = \sun\nio\ch\FileChannelImpl::$RET_EX_LOCK; // return 1;
	} else {
		$lock = \sun\nio\ch\FileChannelImpl::$LOCKED; // return 0;
	}
	//var_dump($this->fileLockTable());
	//var_dump($this, $shared, $lock);
	return $lock;
}

//native void sun.nio.ch.FileChannelImpl.release0(java.io.FileDescriptor,long,long)
function Java_sun_nio_ch_FileChannelImpl_release0($fd, $pos, $size) {
	//var_dump($fd, $pos, $size);
}

//private native void sun.nio.ch.FileKey.init(java.io.FileDescriptor)
function Java_sun_nio_ch_FileKey_init($fd) {
	//var_dump($fd->handle);
	//var_dump($this);
	$this->st_dev = 0;    // ID of device
	$this->st_ino = 0;    // Inode number
	$this->handle = $fd->handle;
}

//private static native java.lang.Class[] java.util.ResourceBundle.getClassContext()
function Java_java_util_ResourceBundle_getClassContext() {
	$context = new \JavaArray(3, 'java.lang.Class');
	return $context;
}

//private static native void sun.awt.Win32GraphicsEnvironment.initDisplay()
function Java_sun_awt_Win32GraphicsEnvironment_initDisplay() {}

//private static native java.lang.String sun.awt.Win32GraphicsEnvironment.getEUDCFontFile()
function Java_sun_awt_Win32GraphicsEnvironment_getEUDCFontFile() {
	return null;
}

//private static native boolean sun.java2d.windows.WindowsFlags.initNativeFlags()
function Java_sun_java2d_windows_WindowsFlags_initNativeFlags() {}

//private static native void java.net.InetAddress.init()
function Java_java_net_InetAddress_init() {}

//static native boolean java.net.InetAddressImplFactory.isIPv6Supported()
function Java_java_net_InetAddressImplFactory_isIPv6Supported() {
	return false;
}

//private static native void java.net.Inet4Address.init()
function Java_java_net_Inet4Address_init() {}

function Java_Teste32_php(/* long */$l) {
	var_dump($l);
}

function Java_TesteSmallSql_var_dump0($var) {
	var_dump($var);
	//println($var->getCmd());
	//var_dump($var->getCmd()->findColumn(jstring('message')));
	//var_dump($var->getCmd()->columnExpressions);
	/*
	var_dump(array_keys((array)$var));
	var_dump($var->values);
	var_dump($var->metaData);
	var_dump($var->cmd);
	//*/
}