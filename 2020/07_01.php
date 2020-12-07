<?php

$input 	= file_get_contents('07_input.txt');
$phrases = explode(".", $input);
$phrases = array_map("trim", $phrases);
$phrases = array_filter($phrases);
$phrases = array_map("parse_phrases", $phrases);

//var_dump($phrases);

$color_containers = flip_phrases_array($phrases);

$found_colors = [];
$bags_number = travers_color_containers('shiny gold');



var_dump(count($found_colors));

function parse_phrases($phrase) {
	$pos1 = strpos($phrase, ' bags contain ');
	$bag_color = substr($phrase, 0, $pos1);
	
	$content = substr($phrase, $pos1 + 14); //14 = length of search string above
	$content = explode(", ", $content);

	$content = array_map('parse_content', $content);


	return ['bag_color' => $bag_color, 'content' => $content];
}

function parse_content($content) {
	$matched = preg_match('/^([0-9]+) (.*) bag/', $content, $matches);
	//remove first part of array
	array_shift($matches);
	return $matches;
}

function flip_phrases_array($phrases) {
	$color_containers = [];
	foreach($phrases as $phrase) {	
		foreach($phrase['content'] as $content ){
			if(!isset($content[1])) {
				continue;
			}
			$color_containers[$content[1]][] = $phrase['bag_color'];
		}
	}
	return $color_containers;
}

function travers_color_containers($begin_with) {
	global $color_containers, $found_colors;

	if(!isset($color_containers[$begin_with])) {	
		return;
	}
	foreach($color_containers[$begin_with] as $color) {
		if(in_array($color, $found_colors)) {
			continue;
		}
		$found_colors[] = $color;
		travers_color_containers($color);
	}

	return;
}
