<?php
   
   echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
   
   $path = "http://www.ibiblio.org/xml/examples/shakespeare/hamlet.xml";
   $xml = simplexml_load_file($path);
   //var_dump($xml->xpath('//PERSONA'));
   
   echo "SPEECH: ".count($xml->xpath('//SPEECH/LINE')).PHP_EOL;
   foreach ($xml->xpath('//PERSONA') as $linha) {
     //var_dump($linha);
     echo $linha . "\n";
   }
   
   echo "---------------------------------------------------------\n";
?>