<?php

$input 	= file_get_contents('06_input.txt');
$groups = explode("\n\n", $input);
$groups = array_map('trim', $groups);

$group_counts = array_map('get_group_count', $groups);
var_dump(array_sum($group_counts));

$yes_answers = array_map('get_all_yes', $groups);
$yes_answer_counts = array_map("count", $yes_answers);
var_dump(array_sum($yes_answer_counts));

function get_group_count($group_string) {
	$one_line = str_replace("\n","",$group_string);
	$one_array = str_split($one_line);
	$one_array = array_unique($one_array);
	return count($one_array);
}

function get_all_yes($group_string) {
	$answers_per_person = explode("\n", $group_string);
	$answers_per_person = array_map("str_split", $answers_per_person);
	#var_dump($answers_per_person);


	//special case only one person per group
	if(count($answers_per_person) == 1) {
		return $answers_per_person[0];
	}
	
	//unfortunately, intersect on an array of array
	$common_answers = call_user_func_array('array_intersect', $answers_per_person);

	return $common_answers;
}
