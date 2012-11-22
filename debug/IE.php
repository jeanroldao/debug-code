<?php
class IEEventSinker {
    var $terminated = false;

   function ProgressChange($progress, $progressmax) {
      echo "Progresso: $progress / $progressmax\n";
    }

    function DocumentComplete(&$dom, $url) {
      echo "Documento concluido: $url\n";
    }

    function OnQuit() {
      echo "Saindo...\n";
      $this->terminated = true;
    }
    
    function __call($nome, $args) {
      //echo "$nome:{$args[0]}:{$args[1]}".PHP_EOL;
    }
}
$ie = new COM("InternetExplorer.Application");
$sink = new IEEventSinker();
com_event_sink($ie, $sink, "DWebBrowserEvents2");
$ie->Visible = true;
$ie->Navigate("http://www.google.com");
$i = 0;
while(!$sink->terminated) {
  com_message_pump(4000);
  //echo ($i++).PHP_EOL;
}
$ie = null;
?>
