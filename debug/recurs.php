<?php
function a($n) {
  return a($n+1);
}
echo a(1);
?>