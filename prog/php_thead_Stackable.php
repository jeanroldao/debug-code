<?php
class Sum extends Stackable {
	private $value = 0;
    public function add($inc)  { $this->value += $inc; }
    public function getValue() { return $this->value; }
    public function run(){}
}

class MyThread extends Thread {
    public $sum;
	public $name;

    public function __construct($name, Sum $sum) {
        $this->name = $name;
        $this->sum = $sum;
    }

    public function run(){
        for ($i=0; $i < 10; $i++) {
			$this->sum->synchronized(function($self){
				$self->sum->add(5);
				echo $self->name . ': '
					.$self->sum->getValue() . "\n";
			}, $this);
        }
    }
}

$sum = new Sum();
$t1 = new MyThread('t1', $sum);
$t1->start();

$t2 = new MyThread('t2', $sum);
$t2->start();

$t1->join();
$t2->join();

echo $sum->getValue();
?>