<?php
class lol {
	public static function main() {
		echo "Oi" . PHP_EOL;
	}
}
$fh = fopen("lol.exe","w");

bcompiler_write_header($fh);
bcompiler_write_class($fh,"lol");
bcompiler_write_footer($fh);
fclose($fh);
