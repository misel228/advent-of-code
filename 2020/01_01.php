<?php

$input = file('01_input.txt');
$input = array_map('trim', $input);
$input = array_filter($input);

function sum_is_2020($a, $b) {
	return $a + $b == 2020;
}

function sum_is_2020_2($a, $b, $c) {
	return ($a + $b + $c) == 2020;
}

var_dump(sum_is_2020($input[0], $input[1]));

for($i = 0; $i < count($input); $i++) {
	for($j = $i; $j < count($input); $j++) {
		if(sum_is_2020($input[$i], $input[$j])) {
			echo $input[$i] .' ' . $input[$j] . ' ' . ($input[$i] * $input[$j]) . "\n";
			break 2;
		}
	}
}


for($i = 0; $i < count($input); $i++) {
	for($j = 0; $j < count($input); $j++) {
		for($k = 0; $k < count($input); $k++) {
			if(sum_is_2020_2($input[$i], $input[$j], $input[$k])) {
				echo $input[$i] .' ' . $input[$j] . ' ' . $input[$k] . ' ' . ($input[$i] * $input[$j] * $input[$k]) . "\n";
				break 3;
			}
		}
	}
}
