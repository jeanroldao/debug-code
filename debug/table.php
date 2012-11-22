<?php

class times {
  private $time = null;
  
  public function __construct() {
    $this->time = time();
  }
  
  public function getTime() {
    return date('d/m/Y H:i:s', $this->time);
  }
}

function open($save_path, $session_name)
{
  global $sess_save_path;

  $sess_save_path = dirname(__FILE__);//.'/'.$save_path;
  
  file_put_contents(dirname(__FILE__).'.test', $save_path);
  
  //echo $sess_save_path;
  return(true);
}

function close()
{
  return(true);
}

function read($id)
{
  global $sess_save_path;

  $sess_file = "$sess_save_path/sess_$id";
  $sessao = @unserialize(file_get_contents($sess_file));
  if (empty($sessao)) {
    $sessao = array();
  }
  $string_sessao = '';
  foreach ($sessao as $k => $v) {
    $string_sessao .= $k.'|'.serialize($v);
  }
  return $string_sessao;
}

function write($id, $sess_data)
{
  global $sess_save_path;
  return (bool)file_put_contents("$sess_save_path/sess_$id", serialize($_SESSION));
}

function destroy($id)
{
  global $sess_save_path;

  $sess_file = "$sess_save_path/sess_$id";
  return(@unlink($sess_file));
}

function gc($maxlifetime)
{
  global $sess_save_path;

  foreach (glob("$sess_save_path/sess_*") as $filename) {
    if (filemtime($filename) + $maxlifetime < time()) {
      @unlink($filename);
    }
  }
  return true;
}

session_set_save_handler("open", "close", "read", "write", "destroy", "gc");


//session_cache_limiter('private');


$lista = array(
  'c#' => '.net',
  'vb' => 'ms',
  'java' => 'sun',
  'delphi' => 'borland',
  'phython' => 'google',
);
//session_write_close();

session_name('table');
session_start();

var_dump($_SESSION);

$_SESSION['c'] += 1;

if (empty($_SESSION['strings'])) {
  $_SESSION['strings'] = 'a';
}
$_SESSION['strings']++;

?>
<table border="1">
  <thead>
    <tr>
      <th>language</th>
      <th>developer</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="2">total: <?=count($lista)?></td>
    </tr>
  </tfoot>
  <tbody>
    <? foreach ($lista as $lang => $dev) { ?>
    <tr>
      <td><?=$lang?></td>
      <td><?=$dev?></td>
    </tr>
    <? } ?>
  </tbody>
</table>