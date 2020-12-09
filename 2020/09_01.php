<?php
$input 	 = file('09_input.txt');
$input   = array_map('trim', $input);
$input   = array_filter($input);

$test_cases = [
	[
		'input' => [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,50],
		'preamble_length' => 25,
		'tests' => [
			26 => true, //would be a valid next number, as it could be 1 plus 25 (or many other pairs, like 2 and 24).
			49 => true, // would be a valid next number, as it is the sum of 24 and 25.
			100 => false, //would not be valid; no two of the previous 25 numbers sum to 100.
			50 => false, // would also not be valid; although 25 appears in the previous 25 numbers, the two numbers in the pair must be different.
		],
	],
	[
		'input' => [35,20,15,25,47,40,62,55,65,95,102,117,150,182,127,219,299,277,309,576],
		'preamble_length' => 5,
		'tests' => [
			127 => false,
		],
	],
	[
		'input' => $input,
		'preamble_length' => 25,
	],
];

$test_case = $test_cases[2];


for($pos = $test_case['preamble_length']; $pos < count($test_case['input']); $pos += 1)
{
	$sums = calc_sums($test_case['input'], $test_case['preamble_length'], $pos);
	if(!in_array($test_case['input'][$pos], $sums)) {
		var_dump($test_case['input'][$pos]);
		die();
	}
}



function calc_sums($numbers, $window_size, $pos) {
	$sums = [];
	$number_windows = array_slice($numbers, ($pos-$window_size), $window_size);
	for($i = 0; $i < $window_size; $i++) {
		for($j = ($i + 1); $j < $window_size; $j++) {
			#echo $i.":".$j." - ";
			$sums[] = $number_windows[$i] + $number_windows[$j];
		}
	}
	return $sums;
}

