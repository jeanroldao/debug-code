<pre><?php
set_time_limit(0);
$total = 0;
function dirtree($path) {
  global $total;
  $dir = scandir($path);
  array_shift($dir);
  array_shift($dir);
  $list = array();
  foreach ($dir as $file) {
    $dir_path = realpath($path.DIRECTORY_SEPARATOR.$file);
    if (is_dir($dir_path)) {
      $list[$dir_path] = dirtree($dir_path);
    } else {
      $list[$dir_path] = filesize($dir_path);
      $total += $list[$dir_path];
    }
  }
  return $list;  
}

function dirtree_sort($list) {
  $dirs = array();
  $files = array();
  foreach ($list as $item => $info) {
    
  }
}

function format_bytes($size) {
  $units = array(' B', ' KB', ' MB', ' GB', ' TB');
  for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
  return round($size, 2).$units[$i];
}

$dir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
echo $dir.PHP_EOL;
$list = dirtree($dir);
echo format_bytes($total).PHP_EOL;
print_r($list);
?>
</pre>