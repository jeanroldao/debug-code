<?php
define('NL', '<br />');
echo 'start'.NL;

$pid = pcntl_fork();
if ($pid) {
  echo 'pai'.NL;
} else {
  echo 'filho'.NL;
}
echo 'fim'.NL;
?>