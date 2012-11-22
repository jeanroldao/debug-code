<?php

//include_once dirname(__FILE__).'/../desenv/trunk/adodb/adodb.inc.php';
include_once dirname(__FILE__).'/adodb/adodb.inc.php';
$DB = NewADOConnection('postgres');
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$DB->Connect('procion', 'postgres', 'postgres', 'trunk_bl');
/*
//$versao = $DB->Execute('select version() as v;')->fields['v'];
$versao = 'PostgreSQL 9.1.3, on x86_64-pc-linux-gnu, compiled by gcc-4.6.real (Ubuntu/Linaro 4.6.1-9ubuntu3) 4.6.1, 64-bit';
preg_match('/([0-9]+\.([0-9\.])+)/',$versao, $arr);
//$versao = $arr[1];
var_dump($arr);
exit;
*/

function q($v) {
  return is_numeric($v)?$v:"'".str_replace("'", "''", $v)."'";
}

function arr2sql($dados, $alias = 'a') {
  $linhas = array();
  foreach ($dados as $linha) {
    $campos = array_keys($linha);
    $sql = array();
    foreach ($linha as $c => $v) {
      $sql[] = q($v).' AS "'.$c.'" ';
    }
    $linhas[] = 'SELECT '.implode(',', $sql);
  }
  $sql = '('.implode(' UNION ALL ', $linhas).') '.$alias;
  return $sql;
}

$dados = array(
  array('nome' => 'jean',    'idade' => 22),
  array('nome' => 'j1',      'idade' => 21),
  array('nome' => 'safaw',   'idade' => 22),
  array('nome' => "zzas'fa", 'idade' => 7),
);

$res = $DB->Execute('SELECT upper(nome) as nome, idade FROM '.arr2sql($dados).' order by idade limit 2');
$a = array();
while (!$res->EOF) {
  $a[] = $res->fields;
  $res->MoveNext();
}
echo '<pre>';
print_r($a);
echo '</pre>';
exit;
?>