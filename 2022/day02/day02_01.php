<?php

const ROCK = 'ROCK';
const PAPER = 'PAPER';
const SCISS = 'SCISS';

const POINTS_LOST = 0;
const POINTS_DRAW = 3;
const POINTS_WON = 6;


$game_action = [
    'A' => ROCK,
    'B' => PAPER,
    'C' => SCISS,
];

$game_response = [
    'X' => ROCK,
    'Y' => PAPER,
    'Z' => SCISS,
];

$type_points = [
    ROCK => 1,
    PAPER => 2,
    SCISS => 3,
];

$game_possibilities = [
    ROCK => [
        ROCK => POINTS_DRAW,
        PAPER => POINTS_WON,
        SCISS => POINTS_LOST,
    ],
    PAPER => [
        ROCK => POINTS_LOST,
        PAPER => POINTS_DRAW,
        SCISS => POINTS_WON,
    ],
    SCISS => [
        ROCK => POINTS_WON,
        PAPER => POINTS_LOST,
        SCISS => POINTS_DRAW,
    ],
];

$input = 'A Y
B X
C Z';

$input = file_get_contents('input.txt');
$matches = explode("\n", $input);
$matches = array_filter($matches);

$points = array_map('calc_match_points', $matches);
var_dump(array_sum($points));

function calc_match_points($match) {
    global $game_possibilities, $game_action, $game_response, $type_points;
    $result = preg_match("#^([A-C]{1}) ([X-Z]{1})$#", $match, $parsed);
    
    if(!$result) {
        var_dump($match);die();
    }
    
    $points = $game_possibilities[$game_action[$parsed[1]]][$game_response[$parsed[2]]];
    $points += $type_points[$game_response[$parsed[2]]];
    return $points;
}

