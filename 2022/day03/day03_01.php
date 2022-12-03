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

$compartments = array_map('split_in_half', $letters);
#var_dump($compartments);

$common = array_map('find_common', $compartments);
#var_dump($common);

$values = array_map('map_to_number', $common);
#var_dump($values);

var_dump(array_sum($values));

function split_in_half($string) {
    $length = strlen($string);
    $half = $length / 2;
    $left = substr($string, 0, $half);
    $right = substr($string, $half);
    
    return [$left, $right];
}

function find_common($pair) {
    $foo1 = str_split($pair[0]);
    $foo2 = str_split($pair[1]);
    
    $result = array_values(array_intersect($foo1, $foo2));
   
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
