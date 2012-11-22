<?php
/**
 * funcao e classe para usar o php no modo console
 * @author Jean Farias Roldao <jeanfariasbr@hotmail.com>
 */
if (!defined('console')) {
  /**
   * constant para nao incluir o script de console mais de uma vez
   */
  define('console', true);
  
  /**
   * atalho para o metodo Console::ReadLine();
   */
  function ReadLine() {
    return Console::ReadLine();
  }
  
  /**
   * classe para executar alguns comendos basicos no console php
   */
  class Console {
    
    /**
     * construtor vazio pois nao pricisa fazer nada
     */
    public function __construct(){
      
    }
    
    /**
     * destrutor para colocar um nova linha no final do script no caso de 
     * parse error nao ficar com a mensagem grudada
     */
    public function __destruct() {
      echo PHP_EOL;
    }
    
    /**
     * le uma linha do console ate a quebra de linha fazendo o script pausar ate o proximo enter
     */
    public function ReadLine() {
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
    public function Write($str) {
      echo $str;
    }
    
    /**
     * mesmo que echo(); mas com uma quebra de linha depois da mensagem
     */
    public function WriteLine($str = '') {
      self::Write($str.PHP_EOL);
    }
    
  }//fim class
  
  /**
   * serve apra pode usar o destrutor no final do script
   * e para usar o console como objeto: 
   * $console->ReadLine();
   */
  $console = new Console();
  
}//fim if define

/*
$classes = get_declared_classes();
foreach ($classes as $class) {
  $methods = get_class_methods($class);
  if (in_array('main', $methods)) {
    eval("$class::main();");
    exit;
  }
}
*/

?>