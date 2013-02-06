<?php
function parseDoc($sDoc) {
  $data =  trim(preg_replace('/\r?\n *\* */', ' ', $data));
  preg_match_all('/@([a-z]+)\s+(.*?)\s*(?=$|@[a-z]+\s)/s', $data, $matches);
  $info = array_combine((array)$matches[1], (array)$matches[2]);
  return $info;  
}
class TabelaDb {
  
  public $iLinhas = 0;
  
  function __construct($sFiltro = '') {
    if ($sFiltro) {
      /*
      $sClass = get_class($this);
      
      $aCampos = get_class_vars($sClass);
      
      echo '<pre>';
      print_r($aCampos);
      echo '</pre>';
      exit;
      */
      $sClass = get_class($this);
      $oRef = new ReflectionClass($sClass);
      $aProp = $oRef->getProperties();
      
      $aFields = array();
      foreach ($aProp as $oProp) {
        $sDoc = $oProp->getDocComment();
        if ($sDoc) {
          $aFields[] = strtolower($oProp->getName());
        }
      }
      $sSQL = 'SELECT '.implode(', ', $aFields).' FROM '.$sClass;
      echo '<pre>';
      var_dump($sSQL);
      echo '</pre>';
      exit;
    }
  }
}

class cicclasse extends TabelaDb {

  /**
   * @file    A lot of info about this file
   *          Could even continue on the next line
   * @author  me@example.com
   * @version 2010-05-01
   * @todo    do stuff...
   */  
  var $NM_CLASSE = array();
  
  /**
   * @field id_classe
  */
  var $ID_CLASSE = array();
}

$oCicclasse = new cicclasse('id_classe > 0');
echo '<pre>';
var_dump($oCicclasse);
echo '</pre>';
exit;
?>