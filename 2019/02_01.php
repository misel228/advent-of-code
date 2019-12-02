<?php

$input_code = [1,0,0,3,1,1,2,3,1,3,4,3,1,5,0,3,2,1,10,19,1,19,5,23,2,23,9,27,1,5,27,31,1,9,31,35,1,35,10,39,2,13,39,43,1,43,9,47,1,47,9,51,1,6,51,55,1,13,55,59,1,59,13,63,1,13,63,67,1,6,67,71,1,71,13,75,2,10,75,79,1,13,79,83,1,83,10,87,2,9,87,91,1,6,91,95,1,9,95,99,2,99,10,103,1,103,5,107,2,6,107,111,1,111,6,115,1,9,115,119,1,9,119,123,2,10,123,127,1,127,5,131,2,6,131,135,1,135,5,139,1,9,139,143,2,143,13,147,1,9,147,151,1,151,2,155,1,9,155,0,99,2,0,14,0];

$input_code = [1,12,2,3,1,1,2,3,1,3,4,3,1,5,0,3,2,1,10,19,1,19,5,23,2,23,9,27,1,5,27,31,1,9,31,35,1,35,10,39,2,13,39,43,1,43,9,47,1,47,9,51,1,6,51,55,1,13,55,59,1,59,13,63,1,13,63,67,1,6,67,71,1,71,13,75,2,10,75,79,1,13,79,83,1,83,10,87,2,9,87,91,1,6,91,95,1,9,95,99,2,99,10,103,1,103,5,107,2,6,107,111,1,111,6,115,1,9,115,119,1,9,119,123,2,10,123,127,1,127,5,131,2,6,131,135,1,135,5,139,1,9,139,143,2,143,13,147,1,9,147,151,1,151,2,155,1,9,155,0,99,2,0,14,0];

$test_code = [1,9,10,3,2,3,11,0,99,30,40,50];

$other_test_codes = [
    '1,0,0,0,99' => '2,0,0,0,99', # (1 + 1 = 2).
    '2,3,0,3,99' => '2,3,0,6,99', # (3 * 2 = 6).
    '2,4,4,5,99,0' => '2,4,4,5,99,9801', # (99 * 99 = 9801).
    '1,1,1,4,99,5,6,0,99' => '30,1,1,4,2,5,6,0,99', #.
];


function run_code($memory) {
    echo dump_memory($memory);
    echo '######';
    $program_counter = 0;
    while($program_counter < count($memory)) {
        if($memory[$program_counter] == 99) {
            echo dump_memory($memory);
            return "Program finished with Code 99\n";
        }
        $pos_op1 = $memory[$program_counter + 1];
        $pos_op2 = $memory[$program_counter + 2];
        $pos_res = $memory[$program_counter + 3];

        $op1 = $memory[$pos_op1];
        $op2 = $memory[$pos_op2];
        switch($memory[$program_counter]) {
            case 1: #add
                $memory[$pos_res] = $op1 + $op2;
                break;
            case 2: #multiply
                $memory[$pos_res] = $op1 * $op2;
                break;
            default:
                return "something went wrong!\n";
        }
        $program_counter += 4;
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
        $result_calc = run_code($code_memory);
        $result_memory =  explode(',',$code);
        dump_memory($code_memory);
    }
}

#run_code($test_code);

#run_test_codes($other_test_codes);

run_code($input_code);
