<?php
//http://myanimelist.net/panel.php?go=editmanga&id=6638268&hidenav=true

$url = 'http://myanimelist.net/includes/ajax.inc.php?t=55';//mangalist

//$dados = array('chap' => 1, 'mid' => 11, 'id' => 8356500);//naruto
$dados = array('chap' => 1, 'mid' => 25, 'id' => 6638268);//fma

$url = 'http://myanimelist.net/includes/ajax.inc.php?t=50';//animelist
//$dados = array('epNum' => 1, 'aid' => 1604, 'id' => 1604);//reborn 
//$dados = array('epNum' => 1, 'aid' => 6547, 'id' => 6547);//angel beats
$dados = array('epNum' => 1, 'aid' => 5081, 'id' => 5081);//bakemonogatari
$dados = array('epNum' => 1, 'aid' => 918, 'id' => 918);//gintama


for ($i = 0; $i < 25; $i++) {
  
  $dados['epNum'] = $i + 1;
  
  $data = http_build_query($dados);//'chap=1&mid=11&id=8356500';

  $result = urlPost($url, $data);
  //echo htmlentities($result);
  //echo $urlForum; exit;
  $posini = strpos($result, 'http://myanimelist.net/forum/?topicid=');
  if ($posini) {
    echo $dados['epNum'] . ' sim ';
    pausa();
    //echo $urlForum.'<br />';
    $posfim = strpos($result, "'", $posini);
    $urlForum = substr($result, $posini, $posfim - $posini).'&pollresults=1';
    $conteudoForum = urlGet($urlForum, '');
    echo calculaMediaRaw($conteudoForum).'<br />';
  } else {
    echo $dados['epNum'] . ' nao<br />';
  }
  flush();
  pausa();
}
//echo htmlentities($result);

function calculaMediaRaw($html) {
  //5 out of 5
  $totalScore = 0;
  $totalVotos = 0;
  for ($i = 1; $i <= 5; $i++) {
    $pos = strpos($html, $i.' out of 5');
    if ($pos === false) {
      continue;
    }
    $posini = strpos($html, 'align="center">', $pos);
    $posfim = strpos($html, '<', $posini);
    $valor = substr($html, $posini + 15, $posfim - ($posini + 15));
    $totalScore += $i * $valor;
    $totalVotos += $valor;
  }
  return  number_format($totalVotos > 0 ? $totalScore / $totalVotos : 0, 2) * 2;
}

function pausa() {
  usleep(mt_rand(500000, 2000000));
}

function urlPost($url, $data) {
  $proxy = array(
    'http' => array(
      'proxy'           => 'tcp://10.0.0.1:3128',
      'method'          => 'POST',    
      'request_fulluri' => true,
      'header'          => "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20100101 Firefox/15.0.1\r\n"
                          ."Cookie: gn_country=US; __gads=ID=55ba3017df83c2f5:T=1350304280:S=ALNI_MYJfVCykcRfx2T1C2lTF9YW8sDIaw; __utma=242346526.1780274949.1350304202.1350304202.1350308774.2; __utmc=242346526; __utmz=242346526.1350304202.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); Y=408342; Z=93ba956f71727e92a1f43b843dbb8a60f169a99f; __utmb=242346526.5.10.1350308774\r\n"
                          ."Content-Length: " . strlen($data) . "\r\n",
      'content' => $data
    )
  );    
  $context = stream_context_create($proxy);
  $result = file_get_contents($url, false, $context);
  return $result;
}

function urlGet($url, $data) {
  $proxy = array(
    'http' => array(
      'proxy'           => 'tcp://10.0.0.1:3128',
      'method'          => 'GET',    
      'request_fulluri' => true,
      'header'          => "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20100101 Firefox/15.0.1\r\n"
                          ."Cookie: gn_country=US; __gads=ID=55ba3017df83c2f5:T=1350304280:S=ALNI_MYJfVCykcRfx2T1C2lTF9YW8sDIaw; __utma=242346526.1780274949.1350304202.1350304202.1350308774.2; __utmc=242346526; __utmz=242346526.1350304202.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); Y=408342; Z=93ba956f71727e92a1f43b843dbb8a60f169a99f; __utmb=242346526.5.10.1350308774\r\n"
                          ."Content-Length: " . strlen($data) . "\r\n",
      'content' => $data
    )
  );    
  $context = stream_context_create($proxy);
  $result = file_get_contents($url, false, $context);
  return $result;
}
