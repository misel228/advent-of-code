<?php

$input 	 = file('10_input.txt');
$input   = array_map('trim', $input);
$input   = array_filter($input);

$test_cases = [	
	[
		16,10,15,5,1,11,7,19,6,12,4
	],
	[
		28,33,18,42,31,14,46,20,48,47,24,23,49,45,19,38,39,11,1,32,25,35,8,17,7,9,4,2,34,10,3,
	],
	$input,
];

$test = $test_cases[2];

$test[] = 0;
$max = max($test);
$test[] = ($max + 3);


sort($test);
#var_dump($test);

$differences = [];
for($i = 0; $i < (count($test) - 1) ; $i += 1) {
	$differences[] = $test[$i+1] - $test[$i];
}

#var_dump($differences);
$differences_count = (array_count_values($differences));

var_dump($differences_count[1] *  $differences_count[3]);
