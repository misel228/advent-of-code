<?php
require('intcode_class_09.php');
$input_code = file_get_contents('09_input.txt');

$test_codes = [
    #'104,1125899906842624,99',
    #'1102,34915192,34915192,7,4,7,99,0',

    #'109,1,204,-1,1001,100,1,100,1008,100,16,101,1006,101,0,99',
    #'109,200,1101,100,11,190,209,-10,204,0,99',
    '203,10,204,10,99',
];
/*
foreach($test_codes as $test) {
    try {
        $int = new IntCode($test);
        $int->debug = true;
        $int->run_code([1]);
    }  catch (ExitException $e) {
        #echo $int->dump_memory();
        #die();
    }
    die();

}#*/

try {
    $int = new IntCode($input_code);
    #$int->debug = true;
    $int->run_code([1]);
}  catch (ExitException $e) {
    #die();
}

try {
    $int = new IntCode($input_code);
    $int->debug = true;
    $int->run_code([2]);
}  catch (ExitException $e) {
    echo $int->dump_memory();
}
