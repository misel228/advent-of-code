<?php

$tests = [
	[
		'input' => "939\n7,13,x,x,59,x,31,19",
		'result' => 1068781,
	],
	[
		'input' => "\n17,x,13,19",
		'result' => 3417,
	],
	[
		'input' => "\n67,7,59,61",
		'result' => 754018,
	],
	[
		'input' => "\n67,x,7,59,61",
		'result' => 779210,
	],
	[
		'input' => "\n67,7,x,59,61",
		'result' => 1261476,
	],
	[
		'input' => "\n1789,37,47,1889",
		'result' => 1202161486,
	],
	#/*
	[
		'input' => file_get_contents('13_input.txt'),
		'result' => -1,
	]
	#*/
];

foreach($tests as $test) {
	
	$input = $test['input'];

	$input = explode("\n", $input);
	$input = array_map('trim', $input);
	$bus_lines = read_bus_lines($input[1]);

	$first_line_interval = $bus_lines[0];

	var_dump($bus_lines);
	$result = find_interval($bus_lines, $first_line_interval);
	var_dump($result);
	if($result != $test['result']) {
		die("nope\n");
	}
}


die("END OF PROGRAM\n");

function find_interval($bus_lines, $first_line_interval) {

	$offsets = array_keys($bus_lines);
	$intervals = array_values($bus_lines);

	$tick = $first_line_interval;
	$t = 0;
	$current_line = 1;

	//thank you Lelith from github
	
	//this multiplies the increment by the bus line as soon as it finds a common divisor.
	//Then continues with the next bus line
	while($current_line < count($bus_lines)) {
		$t += $tick;
		$offset   = $offsets[$current_line];
		$interval = $intervals[$current_line];
		if (($t + $offset) % $interval === 0){
			$current_line += 1;
			$tick *= $interval;
		}
		
	}
	return $t;
}


// this is my iterative approach which works fine for the given examples but the actual puzzle would need approximately 
// five months to solve on my small atom core at 2GHz.

function find_interval_small($bus_lines, $first_line_interval) {
	//this is the product of all lines. The result should be somewhere before that, I hope.
	$limit = array_product($bus_lines) * $first_line_interval;
	

	$lines_interval = $first_line_interval;
	for($t = 0; $t < ($limit); $t += $lines_interval) {
		if(($t % 100000000) < $first_line_interval) { 
			echo "\n$t:";
		}
		#if(($t % 100000) < $first_line_interval) echo ".";
		#/
		foreach($bus_lines as $offset => $interval) {
			#echo $t." ".$offset." ".$interval."\n";
			if (($t + $offset) % $interval !== 0) {
				continue 2; //continue timestamp iteration
			}
		}
		echo "found: \n";
		//if you reached this all modulos must be 0 so $t is our searched value
		return $t;
		break;
	}
	return -1;

}

function read_bus_lines($input) {
	$lines = explode(",",$input);
	$lines = array_filter($lines, 'is_numeric');
	return $lines;
}
