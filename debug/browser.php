<?php

if (!empty($_POST['teste'])) {
  echo 'testando: ('.$_POST['teste'].'-'.time().')'.PHP_EOL;
  print_r($_SERVER);
  exit;
}

$url = 'http://localhost'.$_SERVER['REQUEST_URI'];
$proxy = array(
  'http' => array(
    //'proxy'           => 'tcp://10.0.0.1:3128',
    'method'  => 'POST', 
    'content' => http_build_query(array('teste' => time())), 
    //'request_fulluri' => true,
    //'header'          => "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:8.0) Gecko/20100101 Firefox/8.0\r\n",
  )
);    
$context = stream_context_create($proxy);
$result = file_get_contents($url, false, $context);

echo '<pre>'.htmlentities($result);


