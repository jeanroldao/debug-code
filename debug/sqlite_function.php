<?php
$data = array(
   'one',
   'two',
   'three',
   'four',
   'five',
   'six',
   'seven',
   'eight',
   'nine',
   'ten',
   );
$dbhandle = sqlite_open(':memory:');
sqlite_query($dbhandle, "CREATE TABLE strings(a)");
foreach ($data as $str) {
    $str = sqlite_escape_string($str);
    sqlite_query($dbhandle, "INSERT INTO strings VALUES ('$str')");
}

function max_len_step(&$context, $string) 
{
    if (strlen($string) > $context) {
        $context = strlen($string);
    }
}

function max_len_finalize(&$context) 
{
    return $context;
}

sqlite_create_aggregate($dbhandle, 'max_len', 'max_len_step', 'max_len_finalize');
sqlite_create_function($dbhandle, 'strtoupper', 'strtoupper');

sqlite_create_function($dbhandle, 'now', create_function('', 'return date("Y-m-d H:i:s");'));

//sqlite_create_function($dbhandle, 'now', function(){ return date('Y-m-d H:i:s');});

var_dump(sqlite_array_query($dbhandle, "SELECT max_len(a), strtoupper(a), length(a), now() from strings group by a"));

?>