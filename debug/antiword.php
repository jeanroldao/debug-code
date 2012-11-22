<?php
$filename = realpath('teste_portugues.doc');
$content = shell_exec('C:\antiword\antiword.exe '.$filename);
echo '<pre>';
echo ($content);
echo '</pre>';
?>