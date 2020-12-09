<?php
$input 	 = file('09_input.txt');
$input   = array_map('trim', $input);
$input   = array_filter($input);

$test_cases = [
	[
		'target' => 127,
		'input' => [35,20,15,25,47,40,62,55,65,95,102,117,150,182,127,219,299,277,309,576],
	],
	[
		'target' => 22477624,
		'input' => $input,
	],
];

$test_case = $test_cases[1];

for($i = 0; $i < count($test_case['input']); $i += 1)
{
	$sum = 0;
	$j = $i;
	for($j = $i; $sum < $test_case['target']; $j+= 1) {
		$sum += $test_case['input'][$j];
	}
	if($sum == $test_case['target']) {
		$window = array_slice($test_case['input'], $i, ($j-$i));
		$smallest = min($window);
		$largest  = max($window);
		echo $smallest . ' + '.$largest .' = '. ($smallest + $largest);
		echo "\n";
		break;
	}
}
