<?php

const BLACK = 0;
const WHITE = 1;
const TRANS = 2;

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

$image = str_repeat("2", $layer_chunk_size);
var_dump($image);


foreach($layer_chunks as $chunk) {
	$image = add_layer($image, $chunk);
}

$rows = str_split($image, $input['x']);

$colors = [0 => ' ', 1 => 'X'];
foreach($rows as $row) {
	$row_a = str_split($row);
	foreach ($row_a as $pixel) {
		echo $colors[$pixel];
	}
	echo "\n";
}




function add_layer($front, $back) {
	for($i = 0; $i < strlen($front); $i++) {
		if($front[$i] == 2) {
			$front[$i] = $back[$i];
		}
	}
	return $front;
}

function count_char($string, $char) {
	#$array = str_split($string);
	$zeroes = preg_replace("/[^$char]/",'', $string);
	return strlen($zeroes);
	return count($zeroes);
}
