<?php
include 'php_javaClass.php';

$l = packLong('221120237041090560');
println($l);
$d = unpack('dd', $l)['d'];
println(pack('d', $d));
//println(unpackLong($l));
//$l = packLong('9221120237041090560');println(unpackLong($l));
