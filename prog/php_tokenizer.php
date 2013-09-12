<?php
$source = <<<'SOURCE'
<?php
$mundo = 'mundo';
echo ("Oi {$mundo}\"");
?>
SOURCE
;

$code = [];
foreach (token_get_all($source) as $token) {
	if (is_array($token)) {
		
		var_dump(token_name($token[0]));
		var_dump($token[1]);
		var_dump($token[2]);
		echo PHP_EOL;
	} else {
		var_dump($token);
		echo PHP_EOL;
	}
}