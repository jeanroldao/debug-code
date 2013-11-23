<?php
include_once 'index_includetest.php';
global $i;
if (!isset($i)) {
	$i = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>New Page</title>
</head>
<body>
<h1>New Page for test</h1>
<h2><?=getTime()?></h2>
<h2>visitas: <?=($i++)?></h2>
<form><input name="nome" /><input type="submit"/></form>
<div>(registros=0)</div>
<pre>
<?php
print_r($_GET);
?>
</pre>
</body>
</html>
