<?php

const BUFFER_SIZE = 14;

$file_names = [
    'input_test_1.txt',
    'input_test_2.txt',
    'input_test_3.txt',
    'input_test_4.txt',
    'input_test_5.txt',
    'input.txt',
];

foreach($file_names as $file_name) {
    var_dump($file_name);
    $pointer = check_buffer($file_name);
    var_dump($pointer);
}


function check_buffer($file_name) {
    $file = fopen($file_name, 'r');
    $buffer = [];

    $pointer = 0;

    //pre-fill the buffer
    for($pointer = 0; $pointer < BUFFER_SIZE; $pointer += 1) {
        $buffer[] = fgets($file, 2);
    }

    while(!feof($file) && !is_unique($buffer)) {
        $pointer += 1;
        array_shift($buffer);
        $buffer[] = fgets($file, 2);
    }
    return $pointer;
}

function is_unique($buffer) {
    return count(array_unique($buffer)) == BUFFER_SIZE;
}
