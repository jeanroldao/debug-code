<?php

$tick_time = time();
$tick_var = 0;
register_tick_function(function(){
	if ($GLOBALS["tick_time"] == time()) {
		//$GLOBALS["tick_var"]++;
	} else {
		$GLOBALS["tick_time"] = time();
		var_dump($GLOBALS["tick_var"]);
		//$GLOBALS["tick_var"] = 0;
		//echo PHP_EOL;
	}
});
declare(ticks=4440000);

/**
 * soma dos numeros divisiveis por 3 ou 5 menores que 1000
 */
function problem_1() {
	$soma = 0;
	for ($i = 0; $i < 1000; $i++) {
		if ($i % 3 == 0 || $i % 5 == 0) {
			$soma += $i;
		}
	}
	return $soma; //233168
}

/**
 * soma dos numeros pares da sequencia fibonacci menores que 4 000 000
 */
function problem_2() {
	
	$soma_pares = 0;
	
	$num2 = 0;
	$num1 = 1;
	
	$fib = 1;
	for (/*$i = 0; $i < 4000000; $i++*/;;) {
		$fib = $num1 + $num2;
		
		if ($fib >= 4000000) {
			break;
		}
		//echo $fib . PHP_EOL;
		if ($fib % 2 == 0) {
			$soma_pares += $fib;
		}
		
		$num2 = $num1;
		$num1 = $fib;
	}
	echo $soma_pares; //4613732
}

function problem_3() {
	
	function numPrimo($n) {
		if ($n == 1) {
			return false;
		}
		if ($n == 2) {
			return true;
		}
		
		if ($n & 1 == 0) {
			return false;
		}
		
		$sqrtN = sqrt($n);
		for ($i = 2; $i <= $sqrtN; $i++) {
			if ($n % $i == 0) {
				return false;
			}
		}
		
		return true;
	}
	/*
	for ($i = 0; $i < 100; $i++) {
		echo "$i " . (numPrimo($i)) . PHP_EOL;
	}*/
	
	
	$n = 600851475143;
	for ($i = 1; $i <= $n; $i++) {
		$GLOBALS["tick_var"] = (($i / $n) * 100);
		if (numPrimo($i) && $n % $i === 0) {
			echo $i . PHP_EOL;
		}
	}
}


function problem_3b() {
	// -_- desisti e fui numa calculadora online
	// http://www.numberempire.com/600851475143
	
	return 6857; 
}

function problem_3c() {
	//317584931803;
	$n = 600851475143;
	$divisor = 2;
	
	$GLOBALS["tick_var"] = array(&$divisor, &$n);
	while ($n > 1) {
		if (0 == ($n % $divisor)) {
			$n /= $divisor;
			$divisor--;
		}
		$divisor++;
	}
	echo $divisor;
}

problem_3c();
?>