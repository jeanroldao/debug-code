<?php
include 'fwrite.php';
$DB = &DBself::$dados;
if (empty($DB)) {
  $DB = array();
}
if (!empty($_GET['a'])) {
  $DB[] = date('H:i:s');
  header('Location: '.$_SERVER['PHP_SELF']);
}
if (isset($_GET['del'])) {
  unset($DB[$_GET['del']]);
  header('Location: '.$_SERVER['PHP_SELF']);
}
echo '<a href="?a=1">marcar</a>';

foreach ($DB as $i => $value) {
  echo '<div><a href="?del='.$i.'">'.$i.'</a>-'.$value.'</div>';
}
?>