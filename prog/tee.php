<?php
if (!empty($_SERVER['argv'][1])) {
  $data = stream_get_contents(STDIN);
  file_put_contents($_SERVER['argv'][1], $data);
  echo $data;
}
?>