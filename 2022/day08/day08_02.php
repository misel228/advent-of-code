<?php

$file_name = 'input.txt';
#$file_name = 'input_test.txt';
$input = file($file_name, FILE_IGNORE_NEW_LINES);

$map = array_map('str_split', $input );

#var_dump($map);

$width = count($map[0]);
$height= count($map);

$visible_trees = 0;

$best_score = 0;

$border = 0;
for($y = $border; $y < ($height - $border); $y += 1) {
    for($x = $border; $x < ($width - $border); $x += 1) {
        echo $map[$y][$x];
    }
    echo "\n";
}
#die();
#/*
for($y = 1; $y < ($height - 1); $y += 1) {
    for($x = 1; $x < ($width - 1); $x += 1) {
        $ss = calc_scenic_score($x, $y);
        if($ss > $best_score) {
            $best_score = $ss;
        }
        echo str_pad($ss, 4, ' ');
    }
    echo "\n";
}

echo $best_score . ' best score ' . "\n";
#*/
#var_dump( calc_scenic_score(2, 3, true));

function calc_scenic_score($point_x, $point_y, $db = false) {
    global $map, $width,$height;

    $scan_for_height = $map[$point_y][$point_x];

    if($db) var_dump($scan_for_height, $point_x, $point_y);#die();

    $scores = [
        'top' => 1,
        'bottom' => 1,
        'left' => 1,
        'right' => 1,
    ];

    //top
    if($db) echo "###### TOP ######\n";
    for($y = ($point_y - 1); $y > 0; $y -= 1) {
        if($db) var_dump($map[$y][$point_x],$y,$point_x);
        if($map[$y][$point_x] >= $scan_for_height) {
            break;
        }
        $scores['top'] += 1;
    }

    //bottom
    if($db) echo "##### BOTTOM ####\n";
    $visible = true;
    for($y = ($point_y + 1); $y < $height-1; $y += 1) {
        if($db) var_dump($map[$y][$point_x],$y,$point_x);
        if($map[$y][$point_x] >= $scan_for_height) {
            break;
        }
        $scores['bottom'] += 1;
    }

    //left
    if($db) echo "##### LEFT ######\n";
    $visible = true;
    for($x = ($point_x - 1); $x > 0; $x -= 1) {
        if($db) var_dump($map[$point_y][$x],$x,$point_y);
        if($map[$point_y][$x] >= $scan_for_height) {
            break;
        }
        $scores['left'] += 1;
    }

    //right
    if($db) echo "##### RIGHT #####\n";
    $visible = true;
    for($x = ($point_x + 1); $x < $width-1; $x += 1) {
        if($db) var_dump($map[$point_y][$x],$x,$point_y);
        if($map[$point_y][$x] >= $scan_for_height) {
            break;
        }
        $scores['right'] += 1;
    }

    if($db) {
        var_dump($scores);die();
    }
    return array_product($scores);
}
