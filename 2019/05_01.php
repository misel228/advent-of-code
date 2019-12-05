<?php

$input_code = [3,225,1,225,6,6,1100,1,238,225,104,0,1002,188,27,224,1001,224,-2241,224,4,224,102,8,223,223,1001,224,6,224,1,223,224,223,101,65,153,224,101,-108,224,224,4,224,1002,223,8,223,1001,224,1,224,1,224,223,223,1,158,191,224,101,-113,224,224,4,224,102,8,223,223,1001,224,7,224,1,223,224,223,1001,195,14,224,1001,224,-81,224,4,224,1002,223,8,223,101,3,224,224,1,224,223,223,1102,47,76,225,1102,35,69,224,101,-2415,224,224,4,224,102,8,223,223,101,2,224,224,1,224,223,223,1101,32,38,224,101,-70,224,224,4,224,102,8,223,223,101,3,224,224,1,224,223,223,1102,66,13,225,1102,43,84,225,1101,12,62,225,1102,30,35,225,2,149,101,224,101,-3102,224,224,4,224,102,8,223,223,101,4,224,224,1,223,224,223,1101,76,83,225,1102,51,51,225,1102,67,75,225,102,42,162,224,101,-1470,224,224,4,224,102,8,223,223,101,1,224,224,1,223,224,223,4,223,99,0,0,0,677,0,0,0,0,0,0,0,0,0,0,0,1105,0,99999,1105,227,247,1105,1,99999,1005,227,99999,1005,0,256,1105,1,99999,1106,227,99999,1106,0,265,1105,1,99999,1006,0,99999,1006,227,274,1105,1,99999,1105,1,280,1105,1,99999,1,225,225,225,1101,294,0,0,105,1,0,1105,1,99999,1106,0,300,1105,1,99999,1,225,225,225,1101,314,0,0,106,0,0,1105,1,99999,1108,226,677,224,1002,223,2,223,1005,224,329,101,1,223,223,108,226,226,224,1002,223,2,223,1005,224,344,1001,223,1,223,1107,677,226,224,1002,223,2,223,1006,224,359,101,1,223,223,1008,226,226,224,1002,223,2,223,1005,224,374,101,1,223,223,8,226,677,224,102,2,223,223,1006,224,389,101,1,223,223,7,226,677,224,1002,223,2,223,1005,224,404,1001,223,1,223,7,226,226,224,1002,223,2,223,1005,224,419,101,1,223,223,107,226,677,224,1002,223,2,223,1005,224,434,101,1,223,223,107,226,226,224,1002,223,2,223,1005,224,449,1001,223,1,223,1107,226,677,224,102,2,223,223,1006,224,464,1001,223,1,223,1007,677,226,224,1002,223,2,223,1006,224,479,1001,223,1,223,1107,677,677,224,1002,223,2,223,1005,224,494,101,1,223,223,1108,677,226,224,102,2,223,223,1006,224,509,101,1,223,223,7,677,226,224,1002,223,2,223,1005,224,524,1001,223,1,223,1008,677,226,224,102,2,223,223,1005,224,539,1001,223,1,223,1108,226,226,224,102,2,223,223,1005,224,554,101,1,223,223,107,677,677,224,102,2,223,223,1006,224,569,1001,223,1,223,1007,226,226,224,102,2,223,223,1006,224,584,101,1,223,223,8,677,677,224,102,2,223,223,1005,224,599,1001,223,1,223,108,677,677,224,1002,223,2,223,1005,224,614,101,1,223,223,108,226,677,224,102,2,223,223,1005,224,629,101,1,223,223,8,677,226,224,102,2,223,223,1006,224,644,1001,223,1,223,1007,677,677,224,1002,223,2,223,1006,224,659,1001,223,1,223,1008,677,677,224,1002,223,2,223,1005,224,674,101,1,223,223,4,223,99,226];



$other_test_codes = [
    #'1002,4,3,4,33'   => '1002,4,3,4,99',
    #'1101,100,-1,4,0' => '1101,100,-1,4,99', # (3 * 2 = 6).
    #'3,12,6,12,15,1,13,14,13,4,13,99,-1,0,1,9' => '',
    #'3,3,1105,-1,9,1101,0,0,12,4,12,99,1' => '',
    '3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99' => '',
];

$test_code = [1101,100,-1,4,0];

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
    global $opcode_length;
    $instruction_pointer = 0;

    $profiler = array_combine(array_keys($opcode_length), array_fill(0, count($opcode_length), 0));

    while($instruction_pointer < count($memory)) {
        $instruction = $memory[$instruction_pointer];
        if($debug) echo $instruction."\n";
        if($instruction == 99) {
            echo "fin\n";
            var_dump($profiler);
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
                $memory[$pos_op[0]] = intval(readline("Input: "));
                break;
            case 4: #output
                $pos_op[0] = $memory[$instruction_pointer + 1];
                if($mode_par[0]) {
                    $pos_op[0] = $instruction_pointer + 1;
                }
                $op[0] = $memory[$pos_op[0]];
                echo "Output: ".$op[0]."\n";
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


/*
echo dump_memory($test_code);
$test_code = run_code($test_code);
echo dump_memory($test_code);
#*/

#run_test_codes($other_test_codes);

$result = run_code($input_code, false);
#echo dump_memory($input_code);
