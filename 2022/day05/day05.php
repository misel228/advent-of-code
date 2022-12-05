<?php

$file_name = 'input.txt';
#$file_name = 'input_test.txt';
$input = file($file_name, FILE_IGNORE_NEW_LINES);

list($board,$moves) = split_input($input);


$stacks = parse_board($board);
#var_dump($stacks);

$moves = parse_moves($moves);
#var_dump($moves);


$new_stacks = execute_moves($stacks, $moves);
#var_dump($new_stacks,$stacks);die();
$top_crates = get_top_crates($new_stacks);
var_dump($top_crates);

$new_stacks = execute_moves_9001($stacks, $moves);
$top_crates = get_top_crates($new_stacks);
var_dump($top_crates);
#var_dump($new_stacks);die();

function execute_moves_9001($stacks, $moves) {
    foreach($moves as $move) {
        $offset = count($stacks[$move['source']]) - $move['amount'];
        $crates = array_slice($stacks[$move['source']], $offset);
        $stacks[$move['source']] = array_slice($stacks[$move['source']], 0, $offset);
        $stacks[$move['target']] = array_merge($stacks[$move['target']], $crates);
    }
    return $stacks;
}

function get_top_crates($stacks) {
    $result = '';
    foreach($stacks as $stack) {
        $length = count($stack);
        $result .= $stack[$length - 1];
    }
    return $result;
}

function execute_moves($stacks, $moves) {
    foreach($moves as $move) {
        for($step = 0; $step < $move['amount']; $step += 1) {
            $crate = array_pop($stacks[$move['source']]);
            array_push($stacks[$move['target']], $crate);
        }
    }
    return $stacks;
}


function parse_moves($moves) {
    $parsed_moves = array_map('parse_single_move', $moves);
    return $parsed_moves;
}

function parse_single_move($move) {
    $r = preg_match('#^move (\d+) from (\d+) to (\d+)$#', $move, $matches);
    if(!$r) {
        die($move);
    }
    $parsed_move = [
        'amount' => $matches[1],
        'source' => $matches[2],
        'target'   => $matches[3],
    ];
    return $parsed_move;
}

function parse_board($board) {
    //last line are labels
    $labels = parse_labels(array_pop($board));
    $stacks = parse_stacks($board, $labels);
    return $stacks;
}

function parse_stacks($board, $labels) {
    $stacks = prepare_stacks($labels);
    //reverse array and begin from top to bottom to fill up stack array
    $board = array_reverse($board);
    foreach($board as $line) {
        $line_a = str_split($line);
        foreach($labels as $name => $position) {
            $crate = $line_a[$position];
            if($crate == ' ') {
                continue;
            }
            $stacks[$name][] = $crate;
        }
    }
    return $stacks;
}

function prepare_stacks($labels) {
    $stacks = [];
    $names = array_keys($labels);

    foreach($names as $name) {
        $stacks[$name] = [];
    }
    return $stacks;
}

// parse labels into array of name => string position
function parse_labels($labels) {
    $labels = str_split($labels);
    $labels = array_map('trim',$labels);
    $labels = array_filter($labels);
    $labels = array_flip($labels);
    return $labels;
}

function split_input($input) {
    $board = [];
    while(($line = array_shift($input)) != '') {
        $board[] = $line;
    }
    return [$board, $input];
}
