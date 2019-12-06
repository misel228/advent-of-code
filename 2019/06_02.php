<?php

$test_map = 
	[
		'orbits' => 42,
		'map'    => ['COM)B','B)C','C)D','D)E','E)F','B)G','G)H','D)I','E)J','J)K','K)L', 'K)YOU','I)SAN'],
	];
	
function prepare_map($map) {
	$mapp_fliped = [];
	foreach($map as $line) {
		$items = explode(')', $line);
		$mapp_fliped[$items[1]] = $items[0];
	}
	return $mapp_fliped;
}

function get_all_planets($map) {
	$planets = array_unique(array_merge(array_keys($map), array_values($map)));
	array_pop($planets);
	var_dump($planets);
	return $planets;
}

function get_all_orbits($map_flipped, $planet) {
	$planets = [];
	echo "######\n";
	var_dump($planet);
	$target = $planet;
	do {
		
		$target = $map_flipped[$target];
		$planets[] = $target;
		#var_dump($target);
		#break;
	} while ($target != 'COM');
	return $planets;
}

function count_orbits($map_flipped, $source, $goal) {
	$counter = -1;
	echo "######\n";
	var_dump($source);
	$target = $source;
	do {
		$counter += 1;

		$target = $map_flipped[$target];
		#var_dump($target);
		#break;
	} while ($target != $goal);
	return $counter;
}

#$map = prepare_map($test_map['map']);
$file = file('input_06.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
#var_dump($map);die();
$map = prepare_map($file);

$orbits_santa = get_all_orbits($map, 'SAN');
var_dump($orbits_santa);
$orbits_you   = get_all_orbits($map, 'YOU');
var_dump($orbits_you);

$common_orbits = array_values(array_intersect($orbits_santa, $orbits_you));
var_dump($common_orbits);
$count_santa = count_orbits($map, 'SAN', $common_orbits[0]);
$count_you   = count_orbits($map, 'YOU', $common_orbits[0]);
echo "number of orbits\n";
var_dump($count_santa + $count_you);

/*
$count = count_orbits($map);
var_dump($count);

#*/

