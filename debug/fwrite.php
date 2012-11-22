<?php
/*
- dados serializados aqui!-
<dados>YTo0OntpOjIwO3M6ODoiMTc6MjE6MDgiO2k6MjE7czo4OiIxNzoyMTowOSI7aToyMjtzOjg6IjE3OjIxOjE0IjtpOjIzO3M6ODoiMTc6MjE6MTUiO30=<dados>
*/
class DBself {
  static $dados = null;
  static $var;
  
  public function __construct($flag = false) {
    self::$var = file_get_contents(__FILE__);
    self::$var = explode('<dados>', self::$var, 3);
    self::$dados = @unserialize(base64_decode(self::$var[1]));
  }
  
  public function __destruct() {
    self::$var[1] = base64_encode(serialize(self::$dados));
    self::$var = implode('<dados>', self::$var);
    file_put_contents(__FILE__, self::$var);
  }
}
$GLOBALS['DBself'] = new DBself(true);

?>