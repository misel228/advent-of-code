<?php

$file_name = 'input.txt';
#$file_name = 'input_test.txt';
$input = file($file_name, FILE_IGNORE_NEW_LINES);

$map = array_map('str_split', $input );

#var_dump($map);

$width = count($map[0]);
$height= count($map);

$visible_trees = 0;

for($y = 1; $y < ($height - 1); $y += 1) {
    for($x = 1; $x < ($width - 1); $x += 1) {
        $sr = scan_heights($x, $y);
        if($sr == 'v') {
            $visible_trees += 1;
        }
        echo $sr;
    }
    echo "\n";
}


$circumference = (2 * $width) + (2 * $height) - 4; // 4 corners
$visible_trees += $circumference;

echo $visible_trees . ' trees are visible' . "\n";


function scan_heights($point_x, $point_y, $db = false) {
    global $map, $width,$height;

    $scan_for_height = $map[$point_y][$point_x];

    if($db) var_dump($scan_for_height, $point_x, $point_y);#die();

    $visible = true;

    //top
    if($db) echo "###### TOP ######\n";
    for($y = 0; $y < $point_y; $y += 1) {
        if($db) var_dump($map[$y][$point_x],$y,$point_x);
        if($map[$y][$point_x] >= $scan_for_height) {
            $visible = false;
        }
    }
    if($visible) {
        return 'v';
    }

    //bottom
    if($db) echo "##### BOTTOM ####\n";
    $visible = true;
    for($y = ($point_y + 1); $y < $height; $y += 1) {
        if($db) var_dump($map[$y][$point_x],$y,$point_x);
        if($map[$y][$point_x] >= $scan_for_height) {
            $visible = false;
        }
    }
    if($visible) {
        return 'v';
    }

    //left
    if($db) echo "##### LEFT ######\n";
    $visible = true;
    for($x = 0; $x < $point_x; $x += 1) {
        if($db) var_dump($map[$point_y][$x],$x,$point_y);
        if($map[$point_y][$x] >= $scan_for_height) {
            $visible = false;
        }
    }
    if($visible) {
        return 'v';
    }

    //right
    if($db) echo "##### RIGHT #####\n";
    $visible = true;
    for($x = ($point_x + 1); $x < $width; $x += 1) {
        if($db) var_dump($map[$point_y][$x],$x,$point_y);
        if($map[$point_y][$x] >= $scan_for_height) {
            $visible = false;
        }
    }
    if($visible) {
        return 'v';
    }

    return 'i';
}
