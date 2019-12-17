<?php

$test_cases = [
    #/*
    [
        'input' => '10 ORE => 10 A
        1 ORE => 1 B
        7 A, 1 B => 1 C
        7 A, 1 C => 1 D
        7 A, 1 D => 1 E
        7 A, 1 E => 1 FUEL',
        'ore_needed' => 31,
    ],#*/

    [
        'input' => '
        9 ORE => 2 A
        8 ORE => 3 B
        7 ORE => 5 C
        3 A, 4 B => 1 AB
        5 B, 7 C => 1 BC
        4 C, 1 A => 1 CA
        2 AB, 3 BC, 4 CA => 1 FUEL
		',
        'ore_needed' => 165,
    ],
    [
        'input' => '157 ORE => 5 AAAA
165 ORE => 6 BBBB
179 ORE => 7 CCCC
177 ORE => 5 DDDD
165 ORE => 2 EEEE
12 DDDD, 1 EEEE, 8 CCCC => 9 DDEECC
7 BBBB, 7 CCCC => 2 BBCC
3 BBBB, 7 AAAA, 5 DDDD, 10 CCCC => 8 BADC

44 BBCC, 5 BADC, 1 DDEECC, 29 AAAA, 9 EEEE, 48 DDDD => 1 FUEL',
        'ore_needed' => 13312,
    ],
    [
        'input' => '157 ORE => 5 NZVS
		165 ORE => 6 DCFZ
		44 XJWVT, 5 KHKGT, 1 QDVJ, 29 NZVS, 9 GPVTF, 48 HKGWZ => 1 FUEL
		12 HKGWZ, 1 GPVTF, 8 PSHF => 9 QDVJ
		179 ORE => 7 PSHF
		177 ORE => 5 HKGWZ
		7 DCFZ, 7 PSHF => 2 XJWVT
		165 ORE => 2 GPVTF
		3 DCFZ, 7 NZVS, 5 HKGWZ, 10 PSHF => 8 KHKGT',
        'ore_needed' => 13312 ,
    ],
    [
        'input' => '2 VPVL, 7 FWMGM, 2 CXFTF, 11 MNCFX => 1 STKFG
17 NVRVD, 3 JNWZP => 8 VPVL
53 STKFG, 6 MNCFX, 46 VJHF, 81 HVMC, 68 CXFTF, 25 GNMV => 1 FUEL
22 VJHF, 37 MNCFX => 5 FWMGM
139 ORE => 4 NVRVD
144 ORE => 7 JNWZP
5 MNCFX, 7 RFSQX, 2 FWMGM, 2 VPVL, 19 CXFTF => 3 HVMC
5 VJHF, 7 MNCFX, 9 VPVL, 37 CXFTF => 6 GNMV
145 ORE => 6 MNCFX
1 NVRVD => 8 CXFTF
1 VJHF, 6 MNCFX => 4 RFSQX
176 ORE => 6 VJHF',
        'ore_needed' => 180697  ,
    ],    [
        'input' => '171 ORE => 8 CNZTR
7 ZLQW, 3 BMBT, 9 XCVML, 26 XMNCP, 1 WPTQ, 2 MZWV, 1 RJRHP => 4 PLWSL
114 ORE => 4 BHXH
14 VRPVC => 6 BMBT
6 BHXH, 18 KTJDG, 12 WPTQ, 7 PLWSL, 31 FHTLT, 37 ZDVW => 1 FUEL
6 WPTQ, 2 BMBT, 8 ZLQW, 18 KTJDG, 1 XMNCP, 6 MZWV, 1 RJRHP => 6 FHTLT
15 XDBXC, 2 LTCX, 1 VRPVC => 6 ZLQW
13 WPTQ, 10 LTCX, 3 RJRHP, 14 XMNCP, 2 MZWV, 1 ZLQW => 1 ZDVW
5 BMBT => 4 WPTQ
189 ORE => 9 KTJDG
1 MZWV, 17 XDBXC, 3 XCVML => 2 XMNCP
12 VRPVC, 27 CNZTR => 2 XDBXC
15 KTJDG, 12 BHXH => 5 XCVML
3 BHXH, 2 VRPVC => 7 MZWV
121 ORE => 7 VRPVC
7 XCVML => 6 RJRHP
5 BHXH, 4 VRPVC => 5 LTCX',
        'ore_needed' => 2210736 ,
    ],];

$success = ' ____  _   _  ____ ____ _____ ____ ____
/ ___|| | | |/ ___/ ___| ____/ ___/ ___|
\___ \| | | | |  | |   |  _| \___ \___ \
 ___) | |_| | |__| |___| |___ ___) |__) |
|____/ \___/ \____\____|_____|____/____/
';

$failed = ' _____ _    ___ _     _____ ____
|  ___/ \  |_ _| |   | ____|  _ \
| |_ / _ \  | || |   |  _| | | | |
|  _/ ___ \ | || |___| |___| |_| |
|_|/_/   \_\___|_____|_____|____/
';

foreach ($test_cases as $test) {
    $factory_reactions = parse_reactions($test['input']);


    $wasted_elements = array_fill_keys (array_keys($factory_reactions), 0);
    $produced_elements = array_fill_keys (array_keys($factory_reactions), 0);
    $produced_elements['ORE'] = 0;
    #print_r($factory_reactions);    die();
    $ore_needed = get_ore_needed_for('FUEL', 1);


    var_dump($ore_needed);
    echo dump($wasted_elements, 'wasted')."\n";
    echo dump($produced_elements, 'produc')."\n";
    var_dump($test['ore_needed']);

    #if ($produced_elements['ORE'] == $test['ore_needed']) {
    if ($ore_needed == $test['ore_needed']) {
        echo $success;
        continue;
    }#*/
    echo $failed;
    break;
}

$input_string = file_get_contents('14_input.txt');
$factory_reactions = parse_reactions($input_string);
$ore_needed = get_ore_needed_for('FUEL', 1);
var_dump($ore_needed);
die("END OF PROGRAM\n");


function parse_reactions($input_string)
{
    $factory_reactions = [];
    $input_array = explode("\n", $input_string);
    //filter empty lines
    $input_array = array_map('trim', $input_array);
    $input_array = array_filter($input_array);
    foreach ($input_array as $line) {
        list($input_string, $output_string) = explode("=>", $line);
        $input_array = explode(",", $input_string);

        $output = parse_reaction_part($output_string);
        $components = array_map('parse_reaction_part', $input_array);

        #$components['result'] = $output['amount'];
        $factory_reactions[$output['chemical']] = [
            'components'  => $components,
            'result'      => $output['amount'],
        ];
    }
    return $factory_reactions;
}

function parse_reaction_part($string)
{
    list($amount, $input) = explode(" ", trim($string));
    return ['amount' => trim($amount), 'chemical' => trim($input)];
}

function dump($array, $name)
{
    ksort($array);
    $output = $name.'# '.'';
    foreach ($array as $ele => $amount) {
        $output .= $ele . ':'. str_pad($amount, 4, " ", STR_PAD_LEFT) . " - ";
        ;
    }
    return $output;
}

function check_waste($chemical, $amount_needed, $pad = '')
{
    global $wasted_elements;

    $debug = true;
    if (@$wasted_elements[$chemical] > 0) {
        if ($debug) {
            echo $pad."waste exists: ".$wasted_elements[$chemical] . " ". $chemical. "\n";
        }
        if ($amount_needed < $wasted_elements[$chemical]) {
            $wasted_elements[$chemical] -= $amount_needed;
            $amount_needed = 0;
            return 0; # nothing to be done
        } elseif ($amount_needed == $wasted_elements[$chemical]) { //waste equals amount needed
            $amount_needed = 0;
            $wasted_elements[$chemical] = 0;
            return 0; # nothing to be done
        } else {
            $amount_needed = $amount_needed - $wasted_elements[$chemical];
            $wasted_elements[$chemical] = 0;
            return $amount_needed;
        }
    }
    return $amount_needed;
}

function get_ore_needed_for($chemical, $amount_needed, $pad = '')
{
    global $factory_reactions,$wasted_elements, $produced_elements;
    echo $pad.$chemical."\n";
    $amount_needed = check_waste($chemical, $amount_needed,$pad);

    $factor = intval(ceil($amount_needed / $factory_reactions[$chemical]['result']));

    $produced = $factor * $factory_reactions[$chemical]['result'];
    $produced_elements[$chemical] += $produced;

    $wasted   = $produced - $amount_needed;
    $wasted_elements[$chemical] += $wasted;

    $ore_produced = 0;
    echo $factor;
    foreach($factory_reactions[$chemical]['components'] as $component) {
        if($component['chemical'] == 'ORE') {
            return ($component['amount'] * $factor);
        } else {
            $needed = $factor * $component['amount'];
            $ore_produced += get_ore_needed_for($component['chemical'], $needed, $pad."\t");
        }
    }
    return $ore_produced;
}
