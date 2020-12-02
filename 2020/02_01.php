<?php

$input = file('02_input.txt');
$input = array_map('trim', $input);
$input = array_filter($input);

$test = [
	'1-3 a: abcde',
	'1-3 b: cdefg',
	'2-9 c: ccccccccc',
];

$parsed_input = array_map('parse_input_line', $input);
$policy_valid = array_map('verify_password_policy', $parsed_input);
$number = count(array_filter($policy_valid));

var_dump($number);

$policy_valid_new = array_map('verify_password_policy_new', $parsed_input);
$number = count(array_filter($policy_valid_new));

var_dump($number);

function parse_input_line($line) {
	$matched = preg_match('/([0-9]+)-([0-9]+) ([a-z]): ([a-z]+)/', $line, $matches);
	if(!$matched) {
		return false;
	}
	return [
		'min' 		=> $matches[1],
		'max' 		=> $matches[2],
		'letter' 	=> $matches[3],
		'password'	=> $matches[4],
	];
}

function verify_password_policy($pp) {
	$pass_letter_counts = array_count_values(str_split($pp['password']));
	if(
		isset($pass_letter_counts[$pp['letter']])
		&& ($pass_letter_counts[$pp['letter']] <= $pp['max'])
		&& ($pass_letter_counts[$pp['letter']] >= $pp['min'])
	) {
		return true;
	}
	return false;
}

function verify_password_policy_new($pp) {
	#echo "======================\n";
	#var_dump($pp);
	$pass_letters = str_split($pp['password']);
	#avoid index 0 problems
	array_unshift($pass_letters, '');
	
	$pos_1_found = ($pass_letters[$pp['min']] == $pp['letter']);
	$pos_2_found = ($pass_letters[$pp['max']] == $pp['letter']);
	#var_dump($pos_1_found);
	#var_dump($pos_2_found);
	$result = ($pos_1_found xor $pos_2_found);
	#var_dump($result);
	return $result;
}


