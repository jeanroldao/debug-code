<?php
//http://127.0.0.1:1212/php_testpage.php

class Text {
	private $name;
	private $last_i;
	
	public function __construct($name) {
		$this->name = $name;
		$this->last_i = 0;
	}
	
	public function getText($text, $i) {
		$text->last_i = $i;
		return "Bem vindo, {$text->name} ($i)";
	}
}
/*
function getText($name, $n) {
	return "Bem vindo, $name ($n)";
}
*/

$name = "Jean";
//$text = getText($name, 20);
$text = new Text($name);
?>
<title><?=$name?></title>
<?php 
//for ($i = 1; $i <= 3; $i += 1) { 
$i = 1;
while ($i <= 3) {
	?>
	<h<?=$i?>>
		<?=$text->getText($text, $i)?>
	</h<?=$i?>>
	<?php 
	$i++;
} 
?>
<div>fim!</div>