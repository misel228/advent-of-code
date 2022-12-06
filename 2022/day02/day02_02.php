<?php

const ROCK = 'ROCK';
const PAPER = 'PAPER';
const SCISS = 'SCISS';

const LOSE = 'LOSE';
const WIN = 'WIN';
const DRAW = 'DRAW';

const POINTS_LOST = 0;
const POINTS_DRAW = 3;
const POINTS_WON = 6;


$game_action = [
    'A' => ROCK,
    'B' => PAPER,
    'C' => SCISS,
];

$moves = [
    'X' => LOSE,
    'Y' => DRAW,
    'Z' => WIN,
];

$game_points = [
    LOSE => POINTS_LOST,
    WIN => POINTS_WON,
    DRAW => POINTS_DRAW,
];

$type_points = [
    ROCK => 1,
    PAPER => 2,
    SCISS => 3,
];

$moves_required = [
    ROCK => [
        DRAW => ROCK,
        WIN => PAPER,
        LOSE => SCISS,
    ],
    PAPER => [
        DRAW => PAPER,
        WIN => SCISS,
        LOSE => ROCK,
    ],
    SCISS => [
        DRAW => SCISS,
        WIN => ROCK,
        LOSE => PAPER,
    ],
];

$input = 'A Y
B X
C Z';

$input = file_get_contents('input.txt');
$matches = explode("\n", $input);
$matches = array_filter($matches);

$points = array_map('calc_match_points', $matches);
//var_dump($points);
var_dump(array_sum($points));

function calc_match_points($match) {
    global $moves_required, $game_action, $game_response, $type_points, $moves, $game_points;
    $result = preg_match("#^([A-C]{1}) ([X-Z]{1})$#", $match, $parsed);

    if(!$result) {
        var_dump($match);die();
    }
    $required_move = $moves[$parsed[2]];

    $action = $game_action[$parsed[1]];
    $response = $moves_required[$action][$required_move];

    $points = $game_points[$required_move];
    $points += $type_points[$response];
    return $points;
}

