<?php 
if (!empty($_FILES['filemp3']['name'])) {
  //var_dump($_FILES);exit;
	$dir_path = dirname(__FILE__).'/';
	$mp3nome = $_FILES['filemp3']['name'];
	$dir_path .= $mp3nome; 
	
	if (!move_uploaded_file($_FILES['filemp3']['tmp_name'], $dir_path)) {
		echo 'Erro no upload tente novamente!';
    exit;
	} else {
    
	}
	header('Location: '.$_SERVER['PHP_SELF'].'?'.http_build_query(array('musica' => $_FILES['filemp3']['name'])));
}

if (!empty($_GET['arquivo'])) {
  echo file_get_contents($_GET['arquivo']);
  exit;
}

if (!empty($_GET['apagar'])) {
  if (file_exists($_GET['apagar'])) {
    unlink($_GET['apagar']);
  }
	header('Location: '.$_SERVER['PHP_SELF']);
}

if (!empty($_GET['musica'])) {
  $flashvars = http_build_query(array(
    'mp3' => $_SERVER['PHP_SELF'].'?'.http_build_query(array('arquivo' => $_GET['musica'])),
    'autoplay' => true,
  ));
} else {
  $flashvars = '';
}
$listaMusicas = glob('*.mp3');
?>
<h1>mp3</h1>
<form method="post" enctype="multipart/form-data">
	<table>
		<tr>
			<td>musica:</td>
			<td>
				<input type="file" name="filemp3" id="filemp3" onchange="submit();" />
				
			</td>
		</tr>
	</table>
</form>
<? if (empty($_GET['musica'])) {?>
<div><b>parar</b><div>
<? } else { ?>
<div><a href="?">parar</a><div>
<? } ?>
<?php 
foreach ($listaMusicas as $musica) {
  ?><div><a href="?<?=http_build_query(array('apagar' => $musica))?>">[EXC]</a> - <?
  if ($musica == $_GET['musica']) {
    ?><b><?=$musica?></b><?php
  } else {
    ?><a href="?<?=http_build_query(array('musica' => $musica))?>"><?=$musica?></a><?php
  }
  ?></div><? echo PHP_EOL;
}
?>
<div style="visibility: hidden;">
<object type="application/x-shockwave-flash" data="player_mp3_mini.swf" width="200" height="20">
    <param name="movie" value="player_mp3_mini.swf" />
    <param name="bgcolor" value="#000000" />
    <param name="FlashVars" value="<?=$flashvars?>" />
</object>
</div>
