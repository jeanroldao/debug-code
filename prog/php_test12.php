<?php

$file = 'php_testpage';

echo "start...\n";
$descriptorspec = array(
	0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
	1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
	2 => array("pipe", "w") // stderr is a file to write to
);
$p = proc_open("php -d vld.active=1 -d vld.execute=0 -d vld.verbosity=0 -f $file.php", $descriptorspec, $pipes);

file_put_contents("$file.txt", stream_get_contents($pipes[2]));

echo "...end\n";