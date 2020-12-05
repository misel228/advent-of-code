<?php

$input 	= file('05_input.txt');
$input  = array_map('trim', $input);
$seat_ids = array_map('get_seat_id', $input);

//solution for part 1
var_dump(max($seat_ids));


//part 2
$rows = array_map('get_row', $input);
$row_numbers = array_unique($rows);
$empty_row = array_fill(0,8,'o');
$seats = array_fill(0, count($row_numbers), $empty_row);

foreach($input as $line) {
	$row  = get_row($line);
	$seat = get_seat($line);
	
	$seats[$row][$seat] = 'x';
}

draw_rows($seats);
//determined by looking at the seat arrangement
//row 77 seat 2 (with an seat index beginning with 0)

var_dump(calc_seat_id(77,1));

function get_seat_id($line) {
	$row  = get_row($line);
	$seat = get_seat($line);
	
	$seat_id = calc_seat_id($row, $seat);
	return $seat_id;
}

function calc_seat_id($row, $seat) {
	return ($row * 8) + $seat;
}

function get_row($line) {
	$row_str = substr($line, 0, 7);
	$row_bin = str_replace(['F','B'], ['0','1'], $row_str);
	return bindec($row_bin);
}

function get_seat($line) {
	$seat_str = substr($line, -3);
	$seat_bin = str_replace(['R','L'], ['1','0'], $seat_str);
	return bindec($seat_bin);
}

function draw_rows($rows) {
	foreach($rows as $row => $seats) {
		echo str_pad($row,4,' ', STR_PAD_LEFT).': '.implode($seats)."\n";
	}
}