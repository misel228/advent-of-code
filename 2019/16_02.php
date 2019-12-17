<?php

$test_cases = [
	/*[
		'input' => '98765432109876543210',
		'result' => '21098765',
	],*/
	[
		'input' => '03036732577212944063491565474664',
		'result' => '84462026',
	],
	[
		'input' => '02935109699940807407585447034323',
		'result' => '73745418',
	],
	[
		'input' => '03081770884921959731165446850517',
		'result' => '53553731',
	],
	[
		'input' => '59773590431003134109950482159532121838468306525505797662142691007448458436452137403459145576019785048254045936039878799638020917071079423147956648674703093863380284510436919245876322537671069460175238260758289779677758607156502756182541996384745654215348868695112673842866530637231316836104267038919188053623233285108493296024499405360652846822183647135517387211423427763892624558122564570237850906637522848547869679849388371816829143878671984148501319022974535527907573180852415741458991594556636064737179148159474282696777168978591036582175134257547127308402793359981996717609700381320355038224906967574434985293948149977643171410237960413164669930',
		'result' => 'xxx',
	]
	
];



foreach($test_cases as $test) {
	$real_input = str_repeat($test['input'], 10000);
	$phase      = calc_phase($real_input, 7);
	echo "phase:\t".$phase."\n";
	$offset     = substr($test['input'],0, 7);
	echo "offset:\t".$offset.' -> '.intval($offset)."\n";

	$phase      = calc_phase($real_input, intval($offset));
	$phase_8    = substr($phase,$offset, 8);
	echo "output:\t".$phase_8."\n";
	if($phase_8 == $test['result']) {
		echo "SUCCESS\n";
		continue;
	}
	echo "FAIL\n";
	break;
}

function build_pattern($position, $length) {
	$base_pattern = [ 0, 1, 0, -1 ];
	$pattern = [];
	foreach($base_pattern as $factor) {
		$pattern = array_merge($pattern, array_fill(0, $position, $factor));
	}
	while(count($pattern) < ($length +1)) {
		$pattern = array_merge($pattern, $pattern);
	}
	
	return array_slice($pattern, 1, $length);
	
}

function calc_phase($input, $precision = 8) {

	for($phase_nr = 1; $phase_nr <=100 ; $phase_nr++) {
		
		$length = strlen($input);
		$input = str_split($input);
		$phase = [];
		foreach($input as $position => $value) {
			if($position > $precision) {
				break;
			}
			$pattern = build_pattern(($position + 1), $length);
			$temp = 0;
			foreach($pattern as $i_position => $i_value) {
				#echo $i_value .'*'. $input[$i_position] . " + ";
				$temp += $i_value * $input[$i_position];
			}
			$temp = abs($temp % 10);
			$phase[$position] = $temp;
			#echo "\n";
			#echo implode('', $phase)."\n";
		}
		$input = implode('', $phase);
	}
	return $input;
		
}