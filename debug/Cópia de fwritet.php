<?php
include 'fwrite.php';

if (empty(DBself::$dados)) {
  DBself::$dados = array();
}
DBself::$dados[] = date('H:i:s');
echo '<pre>';
print_r(DBself::$dados);
echo '</pre>';

?>