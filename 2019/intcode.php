#!/usr/bin/php
<?php

#param - code
if(!isset($argv[1])) {
	echo "no input code found!\n";
	exit(1);
}

$input_code = explode(',',$argv[1]);

function is_not_numeric($item) {
	return !is_numeric($item);
}

$check = array_filter($input_code, 'is_not_numeric');

if(!empty($check)) {
	var_dump($check);
	echo "non code elements in input found!\n";
	exit(1);
}

#param - code
if(!isset($argv[2])) {
	echo "no input found!\n";
	exit(1);
}

$input_params = explode(',',$argv[2]);

$check = array_filter($input_params, 'is_not_numeric');

if(!empty($check)) {
	var_dump($check);
	echo "non code elements in input found!\n";
	exit(1);
}

$opcode_length = [
    1 => 4, # add
    2 => 4, # multiply
    3 => 2, # input
    4 => 2, # output
    5 => 0, # jump if true  - instruction pointer is set directly
    6 => 0, # jump if false - instruction pointer is set directly
    7 => 4, # less than
    8 => 4, # equals
];

function run_code($memory, $debug = false, $debug2 = false) {
    global $opcode_length,$input_params;
    $instruction_pointer = 0;

    $profiler = array_combine(array_keys($opcode_length), array_fill(0, count($opcode_length), 0));

    while($instruction_pointer < count($memory)) {
        $instruction = $memory[$instruction_pointer];
        if($debug) echo $instruction."\n";
        if($instruction == 99) {
            #echo "fin\n";
            #var_dump($profiler);
            return $memory;
        }

        $mode_par = [0,0,0];

        //check for immediate mode
        if($instruction > 100) {
            $temp        = intval($instruction / 100);
            $instruction = intval($instruction % 100);

            $mode_par[0] = intval($temp % 10);

            $temp      = intval($temp / 10);
            $mode_par[1] = intval($temp % 10);

            $temp      = intval($temp / 10);
            $mode_par[2] = intval($temp % 10);
        }

        $profiler[$instruction] += 1;
        if($debug) echo $instruction_pointer.":".$instruction.' mode: '.implode('', $mode_par)." len: ".$opcode_length[$instruction]."\n";

        if($debug2) readline('continue');
        $pos_op = [];
        $op = [];
        switch($instruction) {
            case 1: #add
                $pos_op = load_addresses($memory, $instruction_pointer, $mode_par, 2);

                $pos_res = $memory[$instruction_pointer + 3];
                $op[0] = $memory[$pos_op[0]];
                $op[1] = $memory[$pos_op[1]];

                $memory[$pos_res] = $op[0] + $op[1];
                break;
            case 2: #multiply
                $pos_op = load_addresses($memory, $instruction_pointer, $mode_par, 2);

                $pos_res = $memory[$instruction_pointer + 3];
                $op[0] = $memory[$pos_op[0]];
                $op[1] = $memory[$pos_op[1]];
                $memory[$pos_res] = $op[0] * $op[1];
                break;

            case 3: #input
                $pos_op[0] = $memory[$instruction_pointer + 1];
                $memory[$pos_op[0]] = array_shift($input_params);
                break;
            case 4: #output
                $pos_op[0] = $memory[$instruction_pointer + 1];
                if($mode_par[0]) {
                    $pos_op[0] = $instruction_pointer + 1;
                }
                $op[0] = $memory[$pos_op[0]];
                echo $op[0]."\n";
                break;
            case 5: #jump if true
                $pos_op = load_addresses($memory, $instruction_pointer, $mode_par, 2);

                if($memory[$pos_op[0]] > 0) {
                    $instruction_pointer = $memory[$pos_op[1]];
                    break;
                }
                $instruction_pointer += 3;
                break;

            case 6: #jump if false
                $pos_op = load_addresses($memory, $instruction_pointer, $mode_par, 2);

                if($memory[$pos_op[0]] == 0) {
                    $instruction_pointer = $memory[$pos_op[1]];
                    break;
                }
                $instruction_pointer += 3;
                break;

            case 7: #less than
                $pos_op = load_addresses($memory, $instruction_pointer, $mode_par, 2);

                $pos_res = $memory[$instruction_pointer + 3];
                $op[0] = $memory[$pos_op[0]];
                $op[1] = $memory[$pos_op[1]];

                $memory[$pos_res] = ($memory[$pos_op[0]] < $memory[$pos_op[1]]) ? 1 : 0;
            break;

            case 8: # equals
                $pos_op = load_addresses($memory, $instruction_pointer, $mode_par, 2);

                $pos_res = $memory[$instruction_pointer + 3];
                $op[0] = $memory[$pos_op[0]];
                $op[1] = $memory[$pos_op[1]];

                $memory[$pos_res] = ($memory[$pos_op[0]] == $memory[$pos_op[1]]) ? 1 : 0;
                break;

            default:
                echo "Unknown instruction: ".$instruction."\n";
                echo "something went wrong!\n";
                die();


        }
        $instruction_pointer += $opcode_length[$instruction];
    }
    return "WHAAAT!\n";
}

function leading_spaces($number) {
    return str_pad($number, 8, ' ', STR_PAD_LEFT);
}

function dump_memory($memory) {
    $beau = array_map('leading_spaces', $memory);
    $return = '';
    while($beau) {
        $temp = [];
        for($i = 0; $i<4; $i++) {
            $temp[$i] = array_shift($beau);
        }
        $return .= implode(',',$temp) ."\n";
    }
    return $return;
}

function run_test_codes($other_test_codes) {
    foreach($other_test_codes as $code => $result) {
        echo "#########################\n";
        $code_memory = explode(',',$code);
        #echo dump_memory($code_memory);
        $result_calc = run_code($code_memory, false, false);
        $result_memory =  explode(',',$code);
        #echo dump_memory($code_memory);
    }
}

function load_addresses($memory, $instruction_pointer, $modes, $num_addresses) {
    $return = [];
    for($i = 0; $i<$num_addresses; $i++) {
        $return[$i] = $memory[$instruction_pointer + $i + 1];
        if($modes[$i]) {
            $return[$i] = $instruction_pointer + $i + 1;
        }
    }
    return $return;

}


$result = run_code($input_code, false);

#echo $result."\n";
die();
