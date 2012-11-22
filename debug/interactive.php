<?php
/**
 * script para usar o php no modo interativo no console
 * @author Jean Farias Roldao <jeanfariasbr@hotmail.com>
 */

//include para o modo de console
include 'console.php';

class interactive {
  function main() {
    echo 'PHP modo interativo, executa um comando por linha:'.PHP_EOL;
    echo '>';
    while (true) {
      $command = readline();
      eval($command.';');
      echo PHP_EOL.'>';
    }
  }
}

interactive::main();
?>