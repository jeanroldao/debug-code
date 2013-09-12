<?php
//var_dump(unserialize(file_get_contents('serial.txt')));
//exit;
include 'php_javaClass.php';
println('<start>');
$f=(function() {

	$fp = fopen('wide.bmp', 'rb');
	$bmp = new DataInputStream($fp);
	var_dump($bmp->readShort());
	var_dump($bmp->readInt());
	var_dump($bmp->readShort());
	var_dump($bmp->readShort());
	var_dump($bmp->readInt());
	//$bmp->skipBytes(15);
	fseek($fp, 15);
	var_dump($bmp->readInt());
	var_dump($bmp->readInt());
	var_dump($bmp->readShort());
	var_dump($bmp->readShort());
	return;
	
	//var_dump(Position::$PRIMEIRO instanceof Position);
	
	$c = new MyClass("Jean");
	$c->falar("oi");
	
	
	
	$m = new Manager();
	class calla {
		public function call() {
			//echo "LOL\n";
			println(Position::$PRIMEIRO->getClass()->getName());
		}
	}
	$m->addCall(new calla());
	$m->exec();
		
	//file_put_contents('MyClass.txt', serialize(Position::$PRIMEIRO));
	//$TesteInnerClass_dentro_da_classe = 'TesteInnerClass$dentro$da$classe';
	//$TesteInnerClass_dentro_da_classe::falar();
});$f();
println('<end>');