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

//private static native void sun.misc.Unsafe.registerNatives()
function Java_sun_misc_Unsafe_registerNatives() {}

//public native void sun.misc.Unsafe.ensureClassInitialized(java.lang.Class)
function Java_sun_misc_Unsafe_ensureClassInitialized($class) {}

//public native java.lang.Object sun.misc.Unsafe.staticFieldBase(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_staticFieldBase($field) {
	//return jstring("opa");
	//var_dump($this);
	//var_dump($field->getDeclaringClass()->toString());
	//exit;
	return $field->getDeclaringClass();
}

//public native java.lang.Object sun.misc.Unsafe.getObject(java.lang.Object,long)
function Java_sun_misc_Unsafe_getObject($obj, $offset) {
	if ($obj instanceof \java\lang\Clazz) {
		$className = $obj->getName()->replace('.', '\\')->toString() . '';
		$field = array_keys(get_class_vars($className))[$offset];
		return $className::$$field;
	}
	var_dump('Java_sun_misc_Unsafe_getObject', $obj, $offset);
	exit;
}

//public native long sun.misc.Unsafe.staticFieldOffset(java.lang.reflect.Field)
function Java_sun_misc_Unsafe_staticFieldOffset($field) {
	$className = $field->getDeclaringClass()->getName()->replace('.', '\\')->toString() . '';
	$fieldName = $field->getName()->toString().'';
	return array_search($fieldName, array_keys(get_class_vars($className)));
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

$GLOBALS['Java_sun_misc_Unsafe_nextpointer'] = 1;
$GLOBALS['Java_sun_misc_Unsafe_memory'] = "\0";
//public native long sun.misc.Unsafe.allocateMemory(long)
function Java_sun_misc_Unsafe_allocateMemory($bytes) {
	$pointer = $GLOBALS['Java_sun_misc_Unsafe_nextpointer'];
	$GLOBALS['Java_sun_misc_Unsafe_nextpointer'] += $bytes;
	$GLOBALS['Java_sun_misc_Unsafe_memory'] .= str_repeat("\0", $bytes);
	return $pointer;
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

//private static native void java.util.zip.ZipEntry.initIDs()
function Java_java_util_zip_ZipEntry_initIDs() {}

//private native void java.util.zip.ZipEntry.initFields(long)
function Java_java_util_zip_ZipEntry_initFields($jzentry) {
	//var_dump($jzentry);
}

//private static native void java.util.zip.ZipFile.freeEntry(long,long)
function Java_java_util_zip_ZipFile_freeEntry($l1, $l2) {
	global $Java_java_util_zip_ZipFile_openFiles;
	unset($Java_java_util_zip_ZipFile_openFiles[$l2]);
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
	
	//reminder
	ACCESS_READ    = 0x04;
    ACCESS_WRITE   = 0x02;
    ACCESS_EXECUTE = 0x01;	
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
	//var_dump($relative_path);exit;
	$real_path = realpath($relative_path);
	if ($real_path === false) {
		var_dump('Java_java_io_WinNTFileSystem_canonicalize0', $relative_path);
		exit;
	} else {
		return jstring($real_path);
	}
}

//public native long java.io.WinNTFileSystem.getLastModifiedTime(java.io.File)
function Java_java_io_WinNTFileSystem_getLastModifiedTime($file) {
	return filemtime("$file");
}

//private static native void java.io.RandomAccessFile.initIDs()
function Java_java_io_RandomAccessFile_initIDs() {}

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
			$mode = 'rw';
			break;
		case 3:
		case 4:
		default:
			var_dump('unknown mode: ' . $mode);
			var_dump('Java_java_io_RandomAccessFile_open');
			exit;
	}
	$handle = fopen("$file", $mode);
	if ($handle === false) {
		var_dump("$file");
		var_dump("error fopen");
		exit;
	}
	return $handle;
}

//private native void java.io.RandomAccessFile.open(java.lang.String,int)
function Java_java_io_RandomAccessFile_open($file, $mode) {
	$this->fd->handle = openJavaFile($file, $mode);
	//var_dump($file, $mode);
	//var_dump($this);
}

//private static native void java.io.FileDescriptor.initIDs()
function Java_java_io_FileDescriptor_initIDs() {}

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

//static native void sun.nio.ch.IOUtil.initIDs()
function Java_sun_nio_ch_IOUtil_initIDs() {}

//private static native long sun.nio.ch.FileChannelImpl.initIDs()
function Java_sun_nio_ch_FileChannelImpl_initIDs() {}

//native int sun.nio.ch.FileChannelImpl.lock0(java.io.FileDescriptor,boolean,long,long,boolean)
function Java_sun_nio_ch_FileChannelImpl_lock0($fd, $blocking, $pos, $size, $shared) {
	//var_dump($fd, $blocking, $pos, $size, $shared);
	//var_dump(\sun\nio\ch\FileChannelImpl::$RET_EX_LOCK);exit;
	return 1; //\sun\nio\ch\FileChannelImpl::$RET_EX_LOCK
}

//private static native void sun.nio.ch.FileKey.initIDs()
function Java_sun_nio_ch_FileKey_initIDs() {}

//private native void sun.nio.ch.FileKey.init(java.io.FileDescriptor)
function Java_sun_nio_ch_FileKey_init($fd) {
	//var_dump($fd->handle);
	//var_dump($this);
	$this->st_dev = 0;    // ID of device
	$this->st_ino = 0;    // Inode number
	$this->handle = $fd->handle;
}

function Java_Teste32_php(/* long */$l) {
	var_dump($l);
}

function Java_TesteSmallSql_var_dump0($var) {
	println($var->getCmd());
	//var_dump($var->getCmd()->findColumn(jstring('message')));
	//var_dump($var->getCmd()->columnExpressions);
	/*
	var_dump(array_keys((array)$var));
	var_dump($var->values);
	var_dump($var->metaData);
	var_dump($var->cmd);
	//*/
}