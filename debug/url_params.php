<?php

function getParamFromUrl($url, $paramname) {
  $aParams = array();
  $aDadosUrl = parse_url($url);
  parse_str($aDadosUrl['query'], $aParams);
  
  return isset($aParams[$paramname]) ? $aParams[$paramname] : null;
}

function setParamFromUrl($url, $paramname, $value) {
  $aParams = array();
  $aDadosUrl = parse_url($url);
  parse_str($aDadosUrl['query'], $aParams);
  $aParams[$paramname] = $value;
  $query = http_build_query($aParams);
  $aUrl = explode('?', $url);
  $url = $aUrl[0];
  
  return "$url?$query";
}

$url = '/desenv/index.php?nome=sim&idade=20';
var_dump($url);
$url = setParamFromUrl($url, 'nome', 'jeanroldao');
var_dump($url);
var_dump(getParamFromUrl($url, 'nome'));
?>