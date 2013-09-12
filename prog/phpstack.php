<?php
$o = new Pessoa();
execPhpStack(array($p, 'jean'), <<STACK
load_0
load_1
call Pessoa::setNome
STACK;
);

function execPhpStack($args, $code) {
  $stack = array();
  foreach (explode(PHP_EOL, $code) as $lin ) {
	$lin = trim(explode('#', $lin)[0]);
	if (substr($lin, 0, 5) == 'load_') {
	  $i = substr($lin, 5);
	  $stack[] = $args[$i];
	} else if ($lin == 'call') {
	  eval('$stack[] = array_pop($stack)->'.$lin[1].'(array_pop($stack), array_pop($stack));');
	} else {
	  eval('$stack[] = '.$lin.'(array_pop($stack), array_pop($stack));');
	}
  }
}

function somarStack($a, $b) {
  return execPhpStack(array($a, $b), 
<<STACK
load_0 #get a and put on the stack
load_1 #get b and put on the stack
add    #sum the top 2 from the stack e put the result on the stack
return #return the result from the top of the stack
STACK;
  );
}
?>