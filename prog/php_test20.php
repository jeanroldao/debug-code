<?php
require 'PHP-Parser-master/lib/bootstrap.php';

$code = '<?php
echo array(1,2,3);
echo [4,5,6];
//for ($i = 0; $i < 10; $i++) {
	$t = time();
	echo "oi ($t)\n"; 
//}
?>
<h1><?=$t?></h1>';

$parser = new PhpParser\Parser(new PhpParser\Lexer());

try {
    $stmts = $parser->parse($code);
	var_dump($stmts);
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}