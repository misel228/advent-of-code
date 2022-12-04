<?php

$file_name = 'input_day01.txt';
#$file_name = 'test_data.txt';

$content = file_get_contents($file_name);

$calories_per_elf_raw = explode("\n\n", $content);

$calories_per_elf = array_map( function($item) {return array_sum(explode("\n", $item));}  ,$calories_per_elf_raw);

$max = max($calories_per_elf);
var_dump($max);

rsort($calories_per_elf);



$top_three = $calories_per_elf[0] + $calories_per_elf[1] + $calories_per_elf[2];
var_dump($top_three);
