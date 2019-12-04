<?php

$min = 265275;
$max = 781584;

$test_codes = [
    111111 => true ,#meets these criteria (double 11, never decreases).
    223450 => false ,#does not meet these criteria (decreasing pair of digits 50).
    123789 => false ,# does not meet these criteria (no double).
    266667 => true,
];

#$min = 266666,266677,266688,266699,266777,;
#$max = 779969;
#/*
$valid_codes = range($min,$max); # six-digit numbers

echo "# codes: ".count($valid_codes) . "\n";


$valid_codes = array_filter($valid_codes, 'check_adjacent_digits');

echo "# codes: ".count($valid_codes) . "\n";

$valid_codes = array_filter($valid_codes, 'check_no_decrease');

echo "# codes: ".count($valid_codes) . "\n";

echo implode(',',$valid_codes);
#*/
/*
$test_codes = array_keys($test_codes);
$test_codes = array_filter($test_codes, 'check_adjacent_digits');
$test_codes = array_filter($test_codes, 'check_no_decrease');
#*/
var_dump($test_codes);


function check_adjacent_digits($number) {
    $last_digit = $number % 10;

    $number = intval(floor($number / 10));


    # go from right to left and check for equality
    # if no match found return false
    while(($number > 0 )) {
        $current_last_digit = $number % 10;
        $number = intval(floor($number / 10));
        if($current_last_digit == $last_digit) {
            return true;
        }
        $last_digit = $current_last_digit;
    }
    return false;
}

function check_no_decrease($number) {
    #echo "nr {$number}\n";
    $last_digit = $number % 10;

    #echo "ld {$last_digit}\n";

    $number = intval(floor($number / 10));
    #echo "nr {$number}\n";

    #die();
    # never decrease means that from right to left the number should never get higher
    while(($number > 0 )) {
        $current_last_digit = $number % 10;
        #echo "cd {$current_last_digit}\n";
        #echo "ld {$last_digit}\n";
        if($current_last_digit > $last_digit) {
            #echo "moeep";
            return false;
        }
        $number = intval(floor($number / 10));
        $last_digit = $current_last_digit;
        #echo "nr {$number}\n";
    }
    return true;
}



