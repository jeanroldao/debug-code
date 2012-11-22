<?php
$mime = "image/jpeg";
$imagem = base64_encode(file_get_contents('C:\Documents and Settings\All Users\Documentos\Minhas imagens\Amostras de imagens\Inverno.jpg'));
?>
<img src="data:<?=$mime?>;base64,<?php 
$len = strlen($imagem);
for ($i = 0; $i < $len; $i++) {
  echo $imagem[$i];
  flush();
  //usleep(round(20/100));
}
?>" />