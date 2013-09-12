<?php

for ($i = 0; $i < 1000000; $i++) {
	for ($k = 0; $k < $i * 1000; $k++);
	echo $k, PHP_EOL;
}