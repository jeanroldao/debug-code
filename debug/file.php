<?php
define('sNL', "
");
$nl = sNL;
file_put_contents('file.log', date('Y-m-d H:i:s').sNL, FILE_APPEND);
?>