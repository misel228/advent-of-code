<?php


$input = 'vJrwpWtwJgWrhcsFMMfFFhFp
jqHRNqRjqzjGDLGLrsFMfFZSrLrFZsSL
PmmdzqPrVvPwwTWBwg
wMqvLMZHhHMvwLHjbvcjnnSBnvTQFn
ttgJtRGJQctTZtZT
CrZsJsPPZsGzwwsLwLmpwMDw';

$input = file_get_contents('input.txt');
$letters = explode("\n", $input);
$letters = array_filter($letters);

$compartments = build_compartments($letters);
#var_dump($compartments);die();

$common = array_map('find_common', $compartments);
#var_dump($common);#die();

$values = array_map('map_to_number', $common);
var_dump($values);

var_dump(array_sum($values));

function build_compartments($letters) {
    $compartments = [];
    for($i = 0; $i < count($letters); $i+=3) {
        $foo = [
            $letters[$i],
            $letters[$i+1],
            $letters[$i+2],
        ];
        $compartments[] = $foo;
    }
    return $compartments;
}

function find_common($triple) {
    $foo1 = str_split($triple[0]);
    $foo2 = str_split($triple[1]);
    $foo3 = str_split($triple[2]);
    
    $result = array_values(array_intersect($foo1, $foo2, $foo3));
   
    return $result[0];
}

function map_to_number($letter) {
    $value = ord($letter);

    if($value >= 97) { //ord('a')
        $value -= 96;
        return $value;
    }
    $value -= 64 - 26;
    return $value;
}
