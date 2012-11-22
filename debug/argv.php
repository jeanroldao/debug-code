<?php
include 'console.php';

class argv {
  public static function main($argc, $argv){
    //echo 'oi';
    for ($i = 0; $i < $argc; $i++) {
      echo $argv[$i].PHP_EOL;
    }
  }
}

$clas = basename(substr($_SERVER['PHP_SELF'], 0, -4));
eval($clas.'::main($argc, $argv);');

?>