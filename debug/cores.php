<?php
for ($i = 0x0; $i < 0xff; $i++) {
  $hex = str_repeat(str_pad(dechex($i), 2, '0', STR_PAD_LEFT), 3);
  echo '<div style="background-color: #'.$hex.'; height: 0.1px;">&nbsp</div>'.PHP_EOL;
}
?>
