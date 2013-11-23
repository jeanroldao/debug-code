<?php
$mem_ini = memory_get_usage();
//$ar = range(0, 1000000);
/*$ar = '';
for ($i = 0; $i < 1000000; $i++) {
	$ar .= "$i,";
}*/
//fgets(STDIN);
//var_dump(explode(',', $ar)[123456]);
$ar = file_get_contents('rt.jar');
echo (memory_get_usage() - $mem_ini) / (1024), PHP_EOL;
echo ($ar[50250075]), PHP_EOL;
echo filesize('rt.jar') - 4, PHP_EOL;
