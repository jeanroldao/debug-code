<?php
$dbconn = mysql_connect('localhost', 'root', ''); 
mysql_select_db('dados', $dbconn);
var_dump($dbconn);

$sql = 'select sleep(5)';
$res = mysql_query($sql, $dbconn);
var_dump(mysql_error());
var_dump($res);

return;
$dbconn = pg_connect("dbname=box5_bl user=postgres password=postgres connect_timeout=3", PGSQL_CONNECT_FORCE_NEW) or die("Could not connect");
//$dbconn = new PDO('pgsql:user=postgres dbname=box5_bl password=postgres');
var_dump($dbconn);

//$bs = pg_connection_busy($dbconn);
if ($bs) {
    echo 'connection is busy';
} else {
  $sql = 'select pg_sleep(5)';
  $r = pg_send_query($dbconn, $sql);
  //$r = $dbconn->exec($sql);
  var_dump($r);
  
  echo 'connection is not busy';
}
?>