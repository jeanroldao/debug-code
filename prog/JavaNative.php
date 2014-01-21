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
	if ($method->_java_type) {
		$opcode = [2 => ['type' => $method->_java_type]];
	} else {
		$opcode = null;
	}
	
	$args = $args !== null ? $args->toArray() : [];
	$argsType = \php_javaClass::getArgsType($method->_java_type);
	foreach ($argsType['args'] as $iArg => $argType) {
		if (strlen($argType) == 1) {
			$args[$iArg] = strval($args[$iArg]);
		}
	}
	
	if ($object !== null) {
		$ret = $object->__call($method->getName().'', $args, $opcode);
	} else {
		$class = \php_javaClass::convertNameJavaToPhp($method->getDeclaringClass()->getName());
		$ret = $class::__callstatic($method->getName().'', $args, $opcode);
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

//void java.lang.Runtime.gc()
function Java_java_lang_Runtime_gc() {
	gc_collect_cycles();
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

//private static native void sun.misc.Unsafe.registerNatives()
function Java_sun_misc_Unsafe_registerNatives() {}

//public native void sun.misc.Unsafe.ensureClassInitialized(java.lang.Class)
function Java_sun_misc_Unsafe_ensureClassInitialized($class) {
	var_dump('Java_sun_misc_Unsafe_ensureClassInitialized');
	println($class->getName());
	exit;
}

//public native java.lang.Object sun.misc.Unsafe.staticFieldBase(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_staticFieldBase($field) {
	//return jstring("opa");
	//var_dump($this);
	//var_dump($field->getDeclaringClass()->toString());
	//exit;
	return $field->getDeclaringClass();
}

//public native int sun.misc.Unsafe.arrayBaseOffset(java.lang.Class)
function Java_sun_misc_Unsafe_arrayBaseOffset($cls) {
	return 0;
	//var_dump($this);
	//var_dump("$cls");
	//var_dump('Java_sun_misc_Unsafe_arrayBaseOffset');
	//exit;
}

//public native java.lang.Object sun.misc.Unsafe.getObject(java.lang.Object,long)
function Java_sun_misc_Unsafe_getObject($obj, $offset) {
	if ($obj instanceof \java\lang\Clazz) {
		$className = $obj->getName()->replace(jstring('.'), jstring('\\'))->toString() . '';
		$field = array_keys(get_class_vars($className))[$offset];
		return $className::$$field;
	}
	var_dump('Java_sun_misc_Unsafe_getObject', $obj, $offset);
	exit;
}

//public native long sun.misc.Unsafe.staticFieldOffset(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_staticFieldOffset($field) {
	$className = $field->getDeclaringClass()->getName()->replace(jstring('.'), jstring('\\'))->toString() . '';
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

$Java_sun_misc_Unsafe_totalSize = 1;
$Java_sun_misc_Unsafe_memory = ["\0"];
//public native long sun.misc.Unsafe.allocateMemory(long)
function Java_sun_misc_Unsafe_allocateMemory($bytes) {
	global $Java_sun_misc_Unsafe_totalSize, 
	       $Java_sun_misc_Unsafe_memory;
	
	$addr = $Java_sun_misc_Unsafe_totalSize;
	$Java_sun_misc_Unsafe_totalSize += $bytes;
	
	$Java_sun_misc_Unsafe_memory[$addr] = str_repeat("\0", $bytes);
	return $addr;
}

function Java_sun_misc_Unsafe_getMemBlockOffset($addr) {
	global $Java_sun_misc_Unsafe_totalSize, 
	       $Java_sun_misc_Unsafe_memory;
	
	$addresses = array_keys($Java_sun_misc_Unsafe_memory);
	$addr_count = count($addresses);// need count before!
	$addresses[] = $Java_sun_misc_Unsafe_totalSize;
	
	for ($i = 0; $i < $addr_count; $i++) {
		//var_dump($addresses[$i]);
		
		if ($addr >= $addresses[$i] 
		&& $addr < $addresses[$i + 1]) {
			//var_dump($Java_sun_misc_Unsafe_memory[$addresses[$i]]);
			return $addresses[$i];
		}
	}
	var_dump("Java_sun_misc_Unsafe_getMemBlockRef");
	var_dump("addr not found!");
	exit;
}

//public native void sun.misc.Unsafe.putLong(long,long)
function Java_sun_misc_Unsafe_putLong($addr, $value) {
	global $Java_sun_misc_Unsafe_memory;
	
	// long uses 8 bytes
	$l1 = str_pad(dechex(bcdiv($value, 0x100000000, 0)), 8, '0', STR_PAD_LEFT);
	$l2 = str_pad(dechex(bcmod($value, 0x100000000)), 8, '0', STR_PAD_LEFT);
	
	$b1= pack("H8", $l1);
	$b2 = pack("H8", $l2);
	
	$binary = "$b1$b2";
	
	$blockOffset = Java_sun_misc_Unsafe_getMemBlockOffset($addr);
	$addr -= $blockOffset;
	
	for ($i = 0; $i < 8; $i++) {
		$Java_sun_misc_Unsafe_memory[$blockOffset][$addr + $i] = $binary[$addr + $i];
	}
	//var_dump($Java_sun_misc_Unsafe_memory[$blockOffset]);
	return;
}

//public native void sun.misc.Unsafe.putByte(long,byte)
function Java_sun_misc_Unsafe_putByte($addr, $byte) {
	global $Java_sun_misc_Unsafe_memory;
	
	$blockOffset = Java_sun_misc_Unsafe_getMemBlockOffset($addr);
	$addr -= $blockOffset;
	
	$Java_sun_misc_Unsafe_memory[$blockOffset][$addr] = chr($byte);
}

//public native byte sun.misc.Unsafe.getByte(long)
function Java_sun_misc_Unsafe_getByte($addr) {
	global $Java_sun_misc_Unsafe_memory;
	
	$blockOffset = Java_sun_misc_Unsafe_getMemBlockOffset($addr);
	$addr -= $blockOffset;
	
	return ord($Java_sun_misc_Unsafe_memory[$blockOffset][$addr]);
}

//public native int sun.misc.Unsafe.pageSize()
function Java_sun_misc_Unsafe_pageSize() {
	return 1 << 8; // ???
	//var_dump($this);
	//exit;
}

//public native void sun.misc.Unsafe.setMemory(long,long,byte)
function Java_sun_misc_Unsafe_setMemory($addr, $bytes, $value) {
	global $Java_sun_misc_Unsafe_memory;

	$blockOffset = Java_sun_misc_Unsafe_getMemBlockOffset($addr);
	$addr -= $blockOffset;
	
	for ($i = 0; $i < $bytes; $i++) {
		$Java_sun_misc_Unsafe_memory[$blockOffset][$addr + $i] = chr($value);
	}
}

//public native void sun.misc.Unsafe.freeMemory(long)
function Java_sun_misc_Unsafe_freeMemory($addr) {
	global $Java_sun_misc_Unsafe_memory;
	unset($Java_sun_misc_Unsafe_memory[$addr]);
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

	if ($zip->open($filename /*, ZipArchive::CREATE*/) !== TRUE) {
		exit("cannot open <$filename>\n");
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
	return \java\util\zip\ZipFile::$STORED;
}

//private static native int java.util.zip.ZipFile.read(long,long,long,byte[],int,int)
function Java_java_util_zip_ZipFile_read($jzfile, $jzentry, $pos, $b, $off, $len) {
	global $Java_java_util_zip_ZipFile_openFiles;
	$toRead = substr($Java_java_util_zip_ZipFile_openFiles[$jzentry], $pos, $len);
	$len = strlen($toRead);
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
	$manifest = php_javaClass::readJarManifest($this->name.'');
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
	$file = "$file";
	//$file .= '\smallsql.master';
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
	
	/*
	var_dump($mode);
	var_dump($mode & 1 ? 'BA_EXISTS' : 0);
	var_dump($mode & 2 ? 'BA_REGULAR' : 0);
	var_dump($mode & 4 ? 'BA_DIRECTORY' : 0);
	var_dump($mode & 8 ? 'BA_HIDDEN' : 0);
	*/
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

//public native long java.io.WinNTFileSystem.getLength(java.io.File)
function Java_java_io_WinNTFileSystem_getLength($file) {
	if (!file_exists("$file")) {
		return '0';
		//var_dump("$file");
		//var_dump("Java_java_io_WinNTFileSystem_getLength");
		//var_dump("file does not exist");
		//exit;
	}
	return filesize("$file");
}

//public native long java.io.WinNTFileSystem.getLastModifiedTime(java.io.File)
function Java_java_io_WinNTFileSystem_getLastModifiedTime($file) {
	if (!file_exists("$file")) {
		var_dump("$file");
		var_dump("Java_java_io_WinNTFileSystem_getLastModifiedTime");
		var_dump("file does not exist");
		exit;
	}
	return filemtime("$file");
}

//public native java.lang.String[] java.io.WinNTFileSystem.list(java.io.File)
function Java_java_io_WinNTFileSystem_list($dir) {
	//var_dump($this, $file);
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

//private native long java.lang.ProcessImpl.create(java.lang.String,java.lang.String,java.lang.String,boolean,java.io.FileDescriptor,java.io.FileDescriptor,java.io.FileDescriptor)
function Java_java_lang_ProcessImpl_create($cmdstr, $envblock, $dir, $stdHandles, $redirectErrorStream) {
	//var_dump($cmdstr, $envblock, $dir, $stdHandles, $redirectErrorStream);
	//exit;
	throw new \java\io\IOException(jstring('Cannot run'));
}

//private native void java.io.RandomAccessFile.open(java.lang.String,int)
function Java_java_io_RandomAccessFile_open($file, $mode) {
	$this->fd->handle = openJavaFile($file, $mode);
	//var_dump($file, $mode);
	//var_dump($this);
}
//private native void java.io.RandomAccessFile.close0()
function Java_java_io_RandomAccessFile_close0() {
	fclose($this->fd->handle);
}

//static native int sun.nio.ch.FileDispatcher.read0(java.io.FileDescriptor,long,int)
function Java_sun_nio_ch_FileDispatcher_read0($fd, $addr, $len) {
	global $Java_sun_misc_Unsafe_memory;
	
	//var_dump($fd, $addr, $len);
	//fseek($fd->handle, $addr);
	$data = fread($fd->handle, $len);
	$len = strlen($data);
	
	$blockOffset = Java_sun_misc_Unsafe_getMemBlockOffset($addr);
	$addr -= $blockOffset;
	
	//var_dump($Java_sun_misc_Unsafe_memory[$blockOffset], $addr, $len);
	for ($i = 0; $i < $len; $i++) {
		$Java_sun_misc_Unsafe_memory[$blockOffset][$addr + $i] = $data[$i];
	}
	//var_dump($Java_sun_misc_Unsafe_memory[$blockOffset], $addr, $len);
	
	return $len;
}

//static native int sun.nio.ch.FileDispatcher.write0(java.io.FileDescriptor,long,int)
function Java_sun_nio_ch_FileDispatcher_write0($fd, $addr, $len) {
	global $Java_sun_misc_Unsafe_memory;
	
	$blockOffset = Java_sun_misc_Unsafe_getMemBlockOffset($addr);
	$addr -= $blockOffset;
	
	//var_dump($Java_sun_misc_Unsafe_memory[$blockOffset], $addr, $len);
	$len = fwrite($fd->handle, substr($Java_sun_misc_Unsafe_memory[$blockOffset], $addr, $len), $len);
	if ($len === false) {
		var_dump('Java_sun_nio_ch_FileDispatcher_write0');
		var_dump("error, can't write");
		exit;
	}
	return $len;
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
	//var_dump($this, $fd, $offset);
	if ($offset !== '-1') {
		//var_dump("set pos offset $offset");
		//var_dump('Java_sun_nio_ch_FileChannelImpl_position0');
		//exit;
		fseek($fd->handle, $offset);
	}
	return ftell($fd->handle);
}

//private native long sun.nio.ch.FileChannelImpl.size0(java.io.FileDescriptor)
function Java_sun_nio_ch_FileChannelImpl_size0($fd) {
	//var_dump($this, $fd);
	$fp = $fd->handle;
	$pos = ftell($fp);
	fseek($fp, 0);
	$size = strlen(stream_get_contents($fp));
	//var_dump($size, $pos);
	fseek($fp, $pos);
	return $size;
	//return \sun\nio\ch\IOStatus::$UNSUPPORTED;
	//exit;
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