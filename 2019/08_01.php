<?php

$test_case = [
	'x' => 3,
	'y' => 2,
	'data' => 123456789012,
	'layers' => [
		[123,456,],
		[789,012],
	],
];

$input = [
	'x' => 25,
	'y' => 6,
	'data' => 'see file',
	'layers' => [],
];

$data = file_get_contents('08_input.txt');

$layer_chunk_size = $input['x'] * $input['y'];

$layer_chunks = str_split($data, $layer_chunk_size);

$min_zero_index = 0;
$min_zero_count = 200;
foreach($layer_chunks as $i => $chunk) {
	$zeroes = count_char($chunk, '0');
	#echo $zeroes."\n";
	if($zeroes < $min_zero_count) {
		$min_zero_index = $i;
		$min_zero_count = $zeroes;
	}
}
var_dump($min_zero_index);
var_dump($layer_chunks[$min_zero_index]);

$ones = count_char($layer_chunks[$min_zero_index], '1');
$twoes = count_char($layer_chunks[$min_zero_index], '2');

var_dump($ones, $twoes);
var_dump(($ones * $twoes) );

function count_char($string, $char) {
	#$array = str_split($string);
	$zeroes = preg_replace("/[^$char]/",'', $string);
	return strlen($zeroes);
	return count($zeroes);
}
