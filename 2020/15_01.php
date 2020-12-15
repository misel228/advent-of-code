<?php

$tests = [
	[
		'input' => '0,3,6',
		'result' => 436,
	],
	[
		'input' => '1,3,2',
		'result' => 1,
	],
	[
		'input' => '2,1,3',
		'result' => 10,
	],
	[
		'input' => '1,2,3',
		'result' => 27,
	],
	[
		'input' => '2,3,1',
		'result' => 78,
	],
	[
		'input' => '3,2,1',
		'result' => 438,
	],
	[
		'input' => '3,1,2',
		'result' => 1836,
	],
	[
		'input' => '0,3,1,6,7,5',
		'result' => -1,
	]

];

$limit = 2020;

foreach($tests as $test) {

	$input = explode(",", $test['input']);
	$input = array_map('trim', $input);

	$counts = array_count_values($input);
	$last_turns = array_flip($input);
	$second_last_turn = [];
	var_dump($input);
	var_dump($counts);

	for($turn = count($input); $turn < $limit; $turn += 1 ) {
		$last_number = $input[$turn - 1];
		var_dump($last_number);
		if($counts[$last_number] == 1) {
			$input[$turn] = 0;
			$counts[0] += 1;
			$second_last_turn[0] = $last_turns[0];
			$last_turns[0] = $turn;
			continue;
		}

		$current_number = $last_turns[$last_number] - $second_last_turn[$last_number];
		$input[$turn] = $current_number;
		$counts[$current_number] += 1;
		$second_last_turn[$current_number] = $last_turns[$current_number];
		$last_turns[$current_number] = $turn;
		
		
		#break;
	}

	var_dump($last_number);
	if($input[$limit - 1] != $test['result']) {
		echo "FAILED!!!\n";
		var_dump($input);die();
	}
}