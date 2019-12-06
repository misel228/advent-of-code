<?php

$test_map = 
	[
		'orbits' => 42,
		'map'    => ['COM)B','B)C','C)D','D)E','E)F','B)G','G)H','D)I','E)J','J)K','K)L'],
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

function count_orbits($map_flipped) {
	$planets = get_all_planets($map_flipped);
	
	$counter = 0;
	foreach($planets as $planet) {
		echo "######\n";
		var_dump($planet);
		$target = $planet;
		do {
			$counter += 1;
			if(!isset($map_flipped[$target]) & $map_flipped[$planet] != 'COM') {
				echo $target;
				die();
			}#*/
			$target = $map_flipped[$target];
			#var_dump($target);
			#break;
		} while ($target != 'COM');
	}
	return $counter;
}

$map = prepare_map($test_map['map']);
$count = count_orbits($map);
var_dump($count);

$file = file('input_06.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
#var_dump($map);die();
$map = prepare_map($file);
$count = count_orbits($map);
var_dump($count);



