<?php
while (true) {
  if (file_exists('z.txt')) {
    echo file_get_contents('z.txt').PHP_EOL;
    unlink('z.txt');
  }
  usleep(10000);
}
?>