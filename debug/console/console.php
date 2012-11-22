<?php
error_reporting(-1);// todos e qualquer tipo de erro do php
ini_set('display_errors', true);

/**
 * funcao e classe para usar o php no modo console
 * @author Jean Farias Roldao <jeanfariasbr@hotmail.com>
 */
if (!defined('MODO_CONSOLE')) {
  /**
   * constant para nao incluir o script de console mais de uma vez
   */
  define('MODO_CONSOLE', true);
  
  if (PHP_SAPI != 'cli') {
    echo 'Execute em modo console!';
    exit;
  }
  
  /**
   * atalho para o metodo Console::ReadLine();
   */
  function ReadLine() {
    return $GLOBALS['console']->ReadLine();
  }
  
  /**
   * classe para executar alguns comendos basicos no console php
   */
  class Console {
    
    /**
     * construtor vazio pois nao precisa fazer nada
     */
    function Console(){
      
    }
    
    /**
     * destrutor para colocar um nova linha no final do script no caso de 
     * parse error nao ficar com a mensagem grudada
     */
    function __destruct() {
      echo PHP_EOL;
    }
    
    /**
     * le uma linha do console ate a quebra de linha fazendo o script pausar ate o proximo enter
     */
    function ReadLine() {
      $in=fgets(STDIN);
      if (PHP_OS == "WINNT") {
        $read = str_replace("\r\n", "", $in);
      } else {
        $read = str_replace("\n", "", $in);
      }
      return $read;
    }
    
    /**
     * mesmo que echo()
     */
    function Write($str) {
      echo $str;
    }
    
    /**
     * mesmo que echo(); mas com uma quebra de linha depois da mensagem
     */
    function WriteLine($str = '') {
      self::Write($str.PHP_EOL);
    }
  }
  
  /**
   * serve apra pode usar o destrutor no final do script
   * e para usar o console como objeto: 
   * $console->ReadLine();
   */
  $console = new Console();
}//fim if define
?>