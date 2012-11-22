<?php
//$url = 'http://republicavirtual.com.br/web_cep.php?cep=94814060&format=query_string';
//$url = 'http://www.facebook.com/feeds/notifications.php?id=100000412113757&viewer=100000412113757&key=AWjkEew8vkJDidEF&format=rss20';
//$url = 'http://www.google.com/';
//$url = 'http://myanimelist.net/mangalist/jeanfarias';
$url = 'http://myanimelist.net/includes/ajax.inc.php?t=55';
//$url = 'https://graph.facebook.com/fql?q=SELECT uid, "nome", name, pic_square FROM user WHERE uid = me() OR uid IN (SELECT uid2 FROM friend WHERE uid1 = me())&access_token=AAACEdEose0cBAPmgJLkH3X8JF1emPFVaDszeUmvqsZCMbT7seIZCC90fbt86yUxBZAEdAXuZAuLm9vSQXU6KCZAuzKmMjLqBUEurWVEhVoSGhbuWaIWWZC';

//http://myanimelist.net/panel.php?go=editmanga&id=8356500&hidenav=true
$data = 'chap=1&mid=11&id=8356500';
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
//file_put_contents('z.xml', $result);
/*
$pos = strpos($result, '<div id="grand_totals">');
$result = substr($result, $pos + 23);
$pos = strpos($result, '</div>');
$result = substr($result, 0, $pos);
$aResult = explode(PHP_EOL, $result);
foreach($aResult as $i => $linha) {
  $linha = strip_tags($linha);
  $linha = trim($linha);
  $linha = trim($linha, ',');
  $aResult[$i] = $linha;
}
$result = implode(PHP_EOL, $aResult);
*/
echo '<pre>';
echo htmlentities($result);
//echo utf8_decode(htmlspecialchars(print_r(xml2array($result), true)));
echo '</pre>';


function xml2array($source){
    //return simplexml_load_string($source);
    return json_decode(json_encode((array) simplexml_load_string($source)),1);
}