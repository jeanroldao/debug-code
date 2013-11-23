<?php
class IEEventSinker {
    var $terminated = false;
	var $contents = '';

    function ProgressChange($progress, $progressmax) {
      //echo "Progresso: $progress / $progressmax\n";
    }

    function DocumentComplete(&$dom, $url) {
		//echo "Documento concluido: $url\n";
		try {
			$this->contents = $dom->document->body->outerHTML;
			//$dom->navigate("javascript:alert('oi');document.fireEvent('alert');window.close();");
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		//var_dump($ie == $dom);
		$dom->Quit();
    }

    function OnQuit() {
      //echo "Saindo...\n";
      $this->terminated = true;
    }
	
	function CommandStateChange1() {
		//print_r(func_get_args());
	}
    
    function __call($nome, $args) {
      //echo "$nome".PHP_EOL;
    }
}
function ie_get_contents($url) {
	$ie = new COM("InternetExplorer.Application");
	$sink = new IEEventSinker();
	com_event_sink($ie, $sink, "DWebBrowserEvents2");
	$ie->Visible = true;
	$ie->Navigate($url);
	$i = 0;
	while(!$sink->terminated) {
	  com_message_pump(10000);
	  //echo ($i++).PHP_EOL;
	}
	return $sink->contents;
	//$ie = null;
}

//var_dump(strlen(ie_get_contents('http://www.profissionaisti.com.br/2013/10/e-book-vmware-de-a-a-z-capitulo-1-parte-1/')));
//echo ie_get_contents('https://lh5.googleusercontent.com/-sBATKISj-Bk/AAAAAAAAAAI/AAAAAAAAAAA/WGkiWNCuZow/s30-c/photo.jpg');
//echo ie_get_contents($_SERVER['argv'][1]);
?>
