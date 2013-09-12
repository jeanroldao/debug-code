<?php
namespace ReactivePhp;

/**
 * @public
 */
class Reactive {
  
  private static $_ref_id = 0;
  private static $_ref_list = array();

  private $_expressions = array();
  
  public function __construct() {}
  public function __destruct() {
    //var_dump(array_keys(self::$_ref_list));
  }
  
  function __set($name, $value) {
    $this->_expressions[$name] = (array) $value;
  }
  
  function __get($name) {
    if (!isset($this->_expressions[$name])) {
      return null;
    }

    $exp = implode(' ', $this->_expressions[$name]);
    
    return eval('return '.$exp .';');
  }
  
  function __toString() {
    
    $id = self::saveRef($this);
    
    return "self::getRef($id)";
  }
  
  public static function saveRef($ref) {
    $id = self::$_ref_id++;
    self::$_ref_list[$id] = $ref;
    return $id;
  }
  
  public static function getRef($id) {
    $ref = self::$_ref_list[$id];
    unset(self::$_ref_list[$id]);
    return $ref;
  }
}

$r20 = new Reactive();
$r20->v = 20;

$r = new Reactive();

$a = 12;
$b = 43;
$r->c = array(&$a, '+', &$b, '+', &$r20, '->v');

var_dump($r->c);

$a = 1;
$b = 99;
var_dump($r->c);

$r->d = array(&$r, '->c');
var_dump($r->d);

$a = 26;
$a1 = 20;
$r->e = array(&$r, '->c - ', &$a1);
var_dump($r->e);
//echo $r;

?>