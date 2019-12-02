<?php

$module_masses = [
	73617,104372,131825,85022,105514,78478,87420,118553,97680,89479,146989,79746,108085,117895,143811,102509,102382,92975,72978,94208,130521,
	83042,138605,107566,63374,71176,129487,118408,115425,63967,98282,121829,92834,61084,70122,87221,132773,141347,133225,81199,94994,60881,110074,
	63499,143107,76618,86818,135394,106908,96085,99801,112903,51751,56002,70924,62180,133025,68025,122660,64898,77339,62109,133891,134460,84224,
	54836,59748,125540,67796,71845,92899,130103,74612,136820,96212,132002,97405,82629,63717,62805,112693,147810,139827,116220,69711,50236,137833,
	103743,147456,112098,84867,75615,132738,81072,89444,58443,94465,112494,82127,132533,
];

$test_cases = [
	14 => 2,
	1969 => 966,
	100756 => 50346,
];

function calculate_fuel ($mass) {
	$step1 = floor($mass / 3);
	$fuel  = $step1 -2;
	#echo $fuel."\n";
	# 6 / 3 - 2 = 0
	# 7 / 3 - 2 = 0.333
	# 8 / 3 - 2 = 0.666
	# 9 / 3 - 2 = 1
	# any fuel greater than 8 requires fuel
	if($fuel > 8) {
		$fuel += calculate_fuel ($fuel);
	}
	return $fuel;
}

function test_fuel_consumption() {
	global $test_cases;
	foreach($test_cases as $mass => $fuel) {
		if($fuel != calculate_fuel($mass)) {
			echo $mass . " is ".calculate_fuel($mass)." should ".$fuel."\n";
			echo "FAILED!\n";
		}
	}
}

test_fuel_consumption();

$module_fuels = array_map('calculate_fuel', $module_masses);
$fuel_total = array_sum($module_fuels);

echo "Total Fuel needed: ".$fuel_total."\n";
