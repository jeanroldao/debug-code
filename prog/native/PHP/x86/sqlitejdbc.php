<?php

$GLOBALS['sqlite_pointers_index'] = 1;
$GLOBALS['sqlite_pointers_array'] = [null];
$GLOBALS['sqlite_pointers_results'] = [];
$GLOBALS['sqlite_pointers_currentrow'] = [];

//protected synchronized native void org.sqlite.NativeDB._open(java.lang.String,int)
function Java_org_sqlite_NativeDB__open($file, $mode) {
	global $sqlite_pointers_index, $sqlite_pointers_array;
	$pointer = $sqlite_pointers_index++;
	$sqlite_pointers_array[$pointer] = new SQLite3("$file");
	$this->pointer = $pointer;
}
	
//synchronized native void org.sqlite.NativeDB.busy_timeout(int)
function Java_org_sqlite_NativeDB_busy_timeout($i) {
	//var_dump($i);
}

//synchronized native int org.sqlite.NativeDB.shared_cache(boolean)
function Java_org_sqlite_NativeDB_shared_cache($enable) {
	return 0;
}

//synchronized native int org.sqlite.NativeDB.enable_load_extension(boolean)
function Java_org_sqlite_NativeDB_enable_load_extension($enable) {
	return 0;
}

//protected synchronized native long org.sqlite.NativeDB.prepare(java.lang.String)
function Java_org_sqlite_NativeDB_prepare($sql) {
	global $sqlite_pointers_index, $sqlite_pointers_array;
	
	//var_dump("$sql");
	
	$stmt_pointer = $sqlite_pointers_index++;
	
	$sqlite_pointers_array[$stmt_pointer] = $sqlite_pointers_array[$this->pointer]->prepare("$sql");
	
	return $stmt_pointer;
}

//synchronized native int org.sqlite.NativeDB.column_count(long)
function Java_org_sqlite_NativeDB_column_count($stmt_pointer) {
	global $sqlite_pointers_array, $sqlite_pointers_results, $sqlite_pointers_currentrow;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	
	//var_dump($sqlite_pointers_results);
	//var_dump("isset(\$sqlite_pointers_results[$stmt_pointer])");
	//var_dump(isset($sqlite_pointers_results[$stmt_pointer]));
	if (!isset($sqlite_pointers_results[$stmt_pointer])) {
		return 0;
	} else {
		return $sqlite_pointers_results[$stmt_pointer]->numColumns();
	}
	/*
	//} else if ($sqlite_pointers_array[$stmt_pointer] instanceof \SQLite3Result) {
		//if(!isset($sqlite_pointers_currentrow[$stmt_pointer])) {
		//	return 0;
		//}
		var_dump($sqlite_pointers_array[$stmt_pointer]);
		var_dump($sqlite_pointers_currentrow);
		var_dump($sqlite_pointers_currentrow[$stmt_pointer]);
		exit;
	//} else {
		var_dump($sqlite_pointers_array);
		var_dump($sqlite_pointers_array[$stmt_pointer]);
		var_dump('Java_org_sqlite_NativeDB_column_count');
		exit;
	}
	//*/
	
	//return $sqlite_pointers_array[$stmt_pointer]->paramCount();
}

//synchronized native int org.sqlite.NativeDB.bind_parameter_count(long)
function Java_org_sqlite_NativeDB_bind_parameter_count($stmt_pointer) {
	global $sqlite_pointers_array;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	
	return $sqlite_pointers_array[$stmt_pointer]->paramCount();
}

//protected synchronized native int org.sqlite.NativeDB.reset(long)
function Java_org_sqlite_NativeDB_reset($stmt_pointer) {
	global $sqlite_pointers_array, $sqlite_pointers_results;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	//unset($sqlite_pointers_results[$stmt_pointer]);
	return (int) !$sqlite_pointers_array[$stmt_pointer]->reset();
}

//synchronized native int org.sqlite.NativeDB.bind_text(long,int,java.lang.String)
function Java_org_sqlite_NativeDB_bind_text($stmt_pointer, $column_num, $text) {
	global $sqlite_pointers_array;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	
	//var_dump("bind_text($column_num, '$text')");
	return (int) !$sqlite_pointers_array[$stmt_pointer]->bindValue($column_num, "$text", SQLITE3_TEXT);
}

//synchronized native int org.sqlite.NativeDB.bind_long(long,int,long)
function Java_org_sqlite_NativeDB_bind_long($stmt_pointer, $column_num, $value) {
	global $sqlite_pointers_array;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	//var_dump("bind_long($column_num, $value)");
	return (int) !$sqlite_pointers_array[$stmt_pointer]->bindValue($column_num, "$value", SQLITE3_INTEGER);
}

//synchronized native int org.sqlite.NativeDB.bind_int(long,int,int)
function Java_org_sqlite_NativeDB_bind_int($stmt_pointer, $column_num, $value) {
	global $sqlite_pointers_array;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	//var_dump("bind_long($column_num, $value)");
	return (int) !$sqlite_pointers_array[$stmt_pointer]->bindValue($column_num, "$value", SQLITE3_INTEGER);
}

//synchronized native java.lang.String org.sqlite.NativeDB.column_name(long,int)
function Java_org_sqlite_NativeDB_column_name($stmt_pointer, $column_num) {
	global $sqlite_pointers_array, $sqlite_pointers_results;
	
	if (empty($stmt_pointer) || empty($sqlite_pointers_array[$stmt_pointer])) {
		throw new \java\lang\NullPointerException();
	}
	return jstring($sqlite_pointers_results[$stmt_pointer]->columnName($column_num));
}

//synchronized native int org.sqlite.NativeDB.column_int(long,int)
function Java_org_sqlite_NativeDB_column_int($stmt_pointer, $column_num) {
	global $sqlite_pointers_currentrow;
	if (!isset($sqlite_pointers_currentrow[$stmt_pointer])) var_dump($sqlite_pointers_currentrow, $stmt_pointer, $column_num);
	return $sqlite_pointers_currentrow[$stmt_pointer][$column_num];
}

//synchronized native long org.sqlite.NativeDB.column_long(long,int)
function Java_org_sqlite_NativeDB_column_long($stmt_pointer, $column_num) {
	global $sqlite_pointers_currentrow;
	if (!isset($sqlite_pointers_currentrow[$stmt_pointer])) var_dump($sqlite_pointers_currentrow, $stmt_pointer, $column_num);
	return $sqlite_pointers_currentrow[$stmt_pointer][$column_num];
}

//synchronized native java.lang.String org.sqlite.NativeDB.column_text(long,int)
function Java_org_sqlite_NativeDB_column_text($stmt_pointer, $column_num) {
	global $sqlite_pointers_currentrow;
	if (!isset($sqlite_pointers_currentrow[$stmt_pointer])) var_dump($sqlite_pointers_currentrow, $stmt_pointer, $column_num);
	return jstring($sqlite_pointers_currentrow[$stmt_pointer][$column_num]);
}

//protected synchronized native int org.sqlite.NativeDB.step(long)
function Java_org_sqlite_NativeDB_step($stmt_pointer) {
	global $sqlite_pointers_index, $sqlite_pointers_array, 
		   $sqlite_pointers_results, $sqlite_pointers_currentrow;
	
	if (!isset($sqlite_pointers_results[$stmt_pointer])) {
		$sqlite_pointers_results[$stmt_pointer] = $sqlite_pointers_array[$stmt_pointer]->execute();
		
		if ($sqlite_pointers_results[$stmt_pointer]->numColumns() == 0)  {
			//SQLITE_DONE = 101;
			return 101;
		}
	}
	
	$res = $sqlite_pointers_results[$stmt_pointer]->fetchArray(SQLITE3_NUM);
	$sqlite_pointers_currentrow[$stmt_pointer] = $res;
	if ($res) {
		//SQLITE_ROW = 100;
		return 100;
	} else {
		//SQLITE_DONE = 101;
		return 101;//?
	}
}

//synchronized native int org.sqlite.NativeDB.changes()
function Java_org_sqlite_NativeDB_changes() {
	global $sqlite_pointers_array;
	return $sqlite_pointers_array[$this->pointer]->changes();
}

//protected synchronized native int org.sqlite.NativeDB.finalize(long)
function Java_org_sqlite_NativeDB_finalize($stmt_pointer) {
	global $sqlite_pointers_array, $sqlite_pointers_results;
	
	/*
	var_dump($sqlite_pointers_array, $sqlite_pointers_results);
	var_dump($stmt_pointer);
	var_dump('what?');
	exit;
	//*/
	
	if (isset($sqlite_pointers_results[$stmt_pointer])) {
		//var_dump("Java_org_sqlite_NativeDB_finalize");exit;
		$sqlite_pointers_results[$stmt_pointer]->finalize();
	}
	
	unset($sqlite_pointers_array[$stmt_pointer]);
	unset($sqlite_pointers_results[$stmt_pointer]);
	return 0;
}

//synchronized native java.lang.String org.sqlite.NativeDB.errmsg()
function Java_org_sqlite_NativeDB_errmsg() {
	global $sqlite_pointers_array;
	var_dump($sqlite_pointers_array[$this->pointer]->lastErrorMsg());
	exit;
}