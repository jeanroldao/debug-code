<?php
include 'console.php';

function difMeses($data1, $data2){
  $arr = explode('/',$data1);
  $arr2 = explode('/',$data2);

  $dia1 = $arr[0];
  $mes1 = $arr[1];
  $ano1 = $arr[2];

  $dia2 = $arr2[0];
  $mes2 = $arr2[1];
  $ano2 = $arr2[2];

  $a1 = ($ano2 - $ano1)*12;
  $m1 = ($mes2 - $mes1)+1;
  $m3 = ($m1 + $a1);
  return $m3;
}

echo 'diga a data1: ';
$data1 = readline();
echo 'diga a data2: ';
$data2 = readline();

echo "$data1 - $data2\n";
$dif = difMeses($data1, $data2);
echo "$dif meses";
?>