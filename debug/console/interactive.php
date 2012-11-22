<?php
/**
 * script para usar o php no modo interativo no console
 * @author Jean Farias Roldao <jeanfariasbr@hotmail.com>
 */

//include para o modo de console
include 'console.php';

function execPhp($buffer = '') {
  
  $str = readline();
  $buffer .= PHP_EOL . (string) $str;
  $chaves = 0;
  for ($i = 0; $i < strlen($buffer); $i++) {
    if ($buffer[$i] == '{'){
      $chaves++;
    } else if ($buffer[$i] == '}'){
      $chaves--;
    }
  }
  if ($chaves <= 0) {
    return $buffer;
  } else {
    echo '>';
    return execPhp($buffer);
  }
};


echo 'PHP modo interativo, executa um comando por linha:'.PHP_EOL;
echo '>';
while (true) {
  $command = execPhp();
  eval($command.'?><?php ');
  echo PHP_EOL.">";
}
?>