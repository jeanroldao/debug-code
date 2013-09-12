<?php
#define('PHP_PATH', 'C:\Users\jean_roldao\Downloads\php-5.4.13-nts-Win32-VC9-x86\php.exe');

function readline() {
	return trim(fgets(STDIN));
}

function translateProcess($pipes, $text) {
  fputs($pipes[0], addcslashes(addslashes($text), PHP_EOL).PHP_EOL);
  return stripcslashes(trim(fgets($pipes[1])));
}

error_reporting(-1);

if (isset($_SERVER["argv"][1]) && $_SERVER["argv"][1] == "popen") {
  echo "<popen>".PHP_EOL;
  
  //$p = popen("php_proxyinput.bat", "r");
  //$p = popen("php_campominado.bat minas.txt", "r");
  $descriptorspec = array(
    0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
    1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
    2 => array("file", "error-output.txt", "a") // stderr is a file to write to
  );
  $p = proc_open('php_proxyinput.bat', $descriptorspec, $pipes);
  /*
  fputs($pipes[0], "oi".PHP_EOL);
  echo fgets($pipes[1]);
  */
  
  while ($lin = readline()) {
	echo translateProcess($pipes, $lin).PHP_EOL;
  }
  
  /*
  while ($lin = readline()) {
	fputs($pipes[0], addcslashes(addslashes($lin), PHP_EOL).PHP_EOL);
  }
  while ($lin = stripcslashes(trim(fgets($pipes[1])))) {
    echo $lin . PHP_EOL;
  }
  */
  //echo translateProcess($pipes, "ola!").PHP_EOL;
  
  /*
  while ($lin = fgets($pipes[1])) {
    echo "lin=$lin";
  }*/
  
  exit(0);
}

//echo "bah!".PHP_EOL."nada!".PHP_EOL;exit;

while ($lin = readline()) {
  echo "(p)$lin(/p)" . PHP_EOL;
}