<?php
if (!function_exists('check_syntax')) {
  function check_syntax($filename) {
    // load file
    $code = file_get_contents($filename);

    // remove non php blocks
    /*$code = preg_replace('/(^|\?>).*?(<\?php|$)/i', '', $code);*/

    // create lambda function
    $f = create_function('', '?>'.$code);

    // return function error status
    return !empty($f);
  }
}


$php_files = glob(dirname(__FILE__).'/../*.php');
//print_r($php_files);
foreach ($php_files as $file) {
  $file = realpath($file);
  //var_dump($file, check_syntax($file));
  //echo $file.':'.(check_syntax($file)?'ok':'error!!').PHP_EOL;
  if (!check_syntax($file)) {
    echo $file.PHP_EOL;
  }
}
?>