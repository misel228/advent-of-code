<?php

$input = 
'939
7,13,x,x,59,x,31,19';


$input      = file_get_contents('13_input.txt');

$input = explode("\n", $input);
$input = array_map('trim', $input);

#var_dump($input);

$waiting_time = $input[0];
$bus_lines = read_bus_lines($input[1]);

$next_start_times = [];
foreach($bus_lines as $line) {
	$next_start_times[$line] = next_start_time($line, $waiting_time);
}

//sort and keep key association
asort($next_start_times);
$next_start_lines = array_keys($next_start_times);

$next_time = array_shift($next_start_times);
$next_line = array_shift($next_start_lines);

var_dump($next_time - $waiting_time);
var_dump($next_line);
var_dump(($next_time - $waiting_time) * $next_line);

function min_next_start_time($carry, $item) {
	if($carry === null) {
		return $item;
	}
	if($item < $carry) {
		return $item;
	}
	return $carry;
}

function read_bus_lines($input) {
	$lines = explode(",",$input);
	$lines = array_filter($lines, 'is_numeric');
	return $lines;
}

function next_start_time($line, $waiting_time) {
	$temp = ceil($waiting_time / $line);
	$temp2 = $temp * $line;
	return (int)$temp2;
}
