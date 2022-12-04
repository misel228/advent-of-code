<?php

$input = '2-4,6-8
2-3,4-5
5-7,7-9
2-8,3-7
6-6,4-6
2-6,4-8';

$input = file_get_contents('input.txt');
$lines = explode("\n", $input);
$lines = array_filter($lines);


$overlaps = array_filter($lines, 'is_overlapped');


var_dump(count($overlaps));
$intersect = array_map('count_overlaps', $lines);
$intersect = array_filter($intersect);
var_dump(count($intersect));

function is_overlapped($line) {
    $matches = parse_line($line);
    if(($matches[1] <= $matches[3]) && (($matches[2] >= $matches[4]))) {
        return true;
    }

    if(($matches[1] >= $matches[3]) && (($matches[2] <= $matches[4]))) {
        return true;
    }
    
    return false;  
}

function count_overlaps($line) {
    $matches = parse_line($line);
    $range_1 = range($matches[1],$matches[2]);
    $range_2 = range($matches[3],$matches[4]);
    $intersect = array_intersect($range_1, $range_2);
    return $intersect;
}


function parse_line($line) {
    $r = preg_match('#^(\d+)-(\d+),(\d+)-(\d+)$#', $line, $matches);
    if(!$r) {
        die($line);
    }
    return $matches;
}