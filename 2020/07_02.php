<?php

$input 	= file_get_contents('07_input.txt');
$phrases = explode(".", $input);
$phrases = array_map("trim", $phrases);
$phrases = array_filter($phrases);
$phrases = array_map("parse_phrases", $phrases);

$colored_phrases = color_2_key($phrases);

#var_dump($colored_phrases);

$found_colors = [];
$bags_number = travers_add_color_containers('shiny gold');

var_dump($bags_number);

function parse_phrases($phrase) {
	$pos1 = strpos($phrase, ' bags contain ');
	$bag_color = substr($phrase, 0, $pos1);
	
	$content = substr($phrase, $pos1 + 14); //14 = length of search string above
	$content = explode(", ", $content);

	$content = array_map('parse_content', $content);
	if($content[0] === false) {
		$content = [];
	}

	return ['bag_color' => $bag_color, 'content' => $content];
}

function parse_content($content) {
	$matched = preg_match('/^([0-9]+) (.*) bag/', $content, $matches);
	if(!$matched) {
		return false;
	}
	//remove first part of array
	array_shift($matches);
	return $matches;
}

function color_2_key($phrases) {
	$colored_phrases = [];
	foreach($phrases as $phrase) {
		$colored_phrases[$phrase['bag_color']] = $phrase['content'];
	}
	
	return $colored_phrases;
}

function travers_add_color_containers($begin_with, $level = "") {
	global $colored_phrases;
	// echo $level."=======================\n";
	// echo $level.$begin_with."\n";
	// echo $level.print_bag_array($colored_phrases[$begin_with])."\n";

	$bags = array_sum(array_map('add_bags', $colored_phrases[$begin_with]));
	// echo $level.$bags."\n";

	foreach($colored_phrases[$begin_with] as $bag) {
		$bags += $bag[0] * travers_add_color_containers($bag[1], $level ."\t");
	}
	// echo $level.$bags."\n";
	return $bags;
}

function add_bags($bag_content) {
	return (int)$bag_content[0];
}

function print_bag_array($bag_array) {
	$string = '';
	foreach($bag_array as $bag) {
		$string .= implode('#', $bag).":";
	}
	return $string;
}