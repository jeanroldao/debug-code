<?php
include_once '../adodb/adodb.inc.php';

$DB = NewADOConnection('postgres');
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_CACHE_DIR = dirname(__FILE__).'/tmp/';

$DB->Connect('localhost', 'postgres', 'postgres', 'testelocal');

$res = $DB->Execute('INSERT INTO test_log (nome) VALUES (?)', array(date('Y-m-d H:i:s')));
if ($res == false) {
  echo $DB->ErrorMsg();
  exit;
}

$res = $DB->CacheExecute(10, 'SELECT pg_sleep(5); SELECT * FROM test_log;');
if ($res == false) {
  echo $DB->ErrorMsg();
  exit;
}
while (!$res->EOF) {
  echo implode(' - ', $res->fields) . '<br />';
  $res->MoveNext();
}
$res->Close();
$res = null;
?>