<?php

$input = file('03_input.txt');
$input = array_map('trim', $input);
$wood_rows  = array_map('str_split', $input);


$directions = [
	
	['x' => 1, 'y' => 1],	// Right 1, down 1.
	['x' => 3, 'y' => 1],	// Right 3, down 1. (This is the slope you already checked.)
	['x' => 5, 'y' => 1],	// Right 5, down 1.
	['x' => 7, 'y' => 1],	// Right 7, down 1.
	['x' => 1, 'y' => 2],	// Right 1, down 2.
];

$result = [];

foreach($directions as $direction) {
	$result[] = traverse_wood($wood_rows, $direction);
}
var_dump($result);

var_dump(array_product($result));



function traverse_wood($wood_rows, $direction) {
	$row_length = count($wood_rows[0]);
	$pos = ['x' => 0, 'y' => 0];
	$path = [];

	//($step = 0; $step < (count($wood_rows) - 1); $step += 1)
	while(true) {
		$pos['x'] += $direction['x'];
		$pos['y'] += $direction['y'];
		
		if($pos['y'] >= count($wood_rows)) {
			break;
		}
		$short_x = $pos['x'] % $row_length;
		$path[] = $wood_rows[$pos['y']][$short_x];
	}
	
	$path_values = array_count_values($path);
	return $path_values['#'];
}
