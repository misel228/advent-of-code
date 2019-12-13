<?php

if (!function_exists('gmp_lcm')) {
    $message = "I'm sorry, but you have to either upgrade to PHP7.3 and install the GMP extensions, or\nwrite your own function to find the Least Common Denominator.\n";
    die($message);
}

$test_moons =
    '<x=-1, y=0, z=2>
	<x=2, y=-10, z=-7>
	<x=4, y=-8, z=8>
	<x=3, y=5, z=-1>';

#/*
$test_moons =
    '<x=-8, y=-10, z=0>
<x=5, y=5, z=10>
<x=2, y=-7, z=3>
<x=9, y=-8, z=-3>';#*/

#/*
$input_moons =
    '<x=-1, y=7, z=3>
    <x=12, y=2, z=-13>
    <x=14, y=18, z=-8>
    <x=17, y=4, z=-4>';
#*/

class Vector
{
    private $values = null;

    public function __construct($value)
    {
        if (is_array($value)) {
            $this->values = $value;
        }
        if (is_string($value)) {
            $this->values = static::parseString($value);
        }
    }

    public static function parseString($vector_string)
    {
        $reg_exp = '#\<x\=(-?\d+), y\=(-?\d+)\, z\=(-?\d+)\>#';
        $vs = trim($vector_string);
        $r = preg_match($reg_exp, $vector_string, $matches);
        if (!$r) {
            throw new Exception("Could not parse vector: " . $vector_string);
        }
        array_shift($matches);
        $values = array_map("intval", $matches);
        return $values;
    }

    public function add(Vector $vector)
    {
        foreach ([0=>'x',1=>'y',2=>'z'] as $key => $component) {
            $this->values[$key] += $vector->$component;
        }
    }

    public function sum()
    {
        $temp = array_map('abs', $this->values);
        return array_sum($temp);
    }
    public function __get($key)
    {
        switch ($key) {
            case 'x':
                return $this->values[0];
            case 'y':
                return $this->values[1];
            case 'z':
                return $this->values[2];
        }
    }

    public function __set($key, $value)
    {
        switch ($key) {
            case 'x':
                $this->values[0] = $value;
                break;
            case 'y':
                $this->values[1] = $value;
                break;
            case 'z':
                $this->values[2] = $value;
                break;
        }
    }

    public function __toString()
    {
        return  "" . implode(';', $this->values) .";";
    }
}

class Moon
{
    private $position;
    private $velocity;

    public function __construct($position)
    {
        $this->position = new Vector($position);
        $this->velocity = new Vector([0,0,0]);
    }

    public function applyGravity($otherMoon)
    {
        $other_position = $otherMoon->position;
        foreach (['x','y','z'] as $component) {
            if ($this->position->$component == $otherMoon->position->$component) {
                continue;
            }
            if ($this->position->$component < $otherMoon->position->$component) {
                $this->velocity->$component += 1;
                $otherMoon->velocity->$component -= 1;
                continue;
            }
            if ($this->position->$component > $otherMoon->position->$component) {
                $this->velocity->$component -= 1;
                $otherMoon->velocity->$component += 1;
                continue;
            }
        }
    }
    public function calculateEnergy()
    {
        return $this->position->sum() * $this->velocity->sum();
    }

    public function applyVelocity()
    {
        $this->position->add($this->velocity);
    }

    public function __get($key)
    {
        switch ($key) {
            case 'position':
                return $this->position;
            case 'velocity':
                return $this->velocity;
        }
    }

    public function __toString()
    {
        return  "pos;" . $this->position." vel;".$this->velocity;
    }
}

function dump($moons)
{
    $output = '';
    foreach ($moons as $moon) {
        $output .= $moon;
    }
    return $output;
}


#read all moons
#$moon_positions = explode("\n", $test_moons);
$moon_positions = explode("\n", $input_moons);


$moons = [];
foreach ($moon_positions as $position) {
    $moons[] = new Moon($position);
}
#var_dump($moons);

#all pairs

$pairs = [
    [0, 1],
    [0, 2],
    [0, 3],
    [1, 2],
    [1, 3],
    [2, 3],
];

dump($moons);

$moon_states = [
    'x' => [],
    'y' => [],
    'z' => [],
];

$repetition_steps = [
    'x' => 0,
    'y' => 0,
    'z' => 0,
];



$step = -1; #gnarf
while (true) {
    $step += 1;

    if (($step % 10000) == 0) {
        echo ".";
        #dump($moons);
    }
    if (($step % 1000000) == 0) {
        echo "\n";
        #dump($moons);
    }

    foreach ($pairs as $pair) {
        $moons[$pair[0]]->applyGravity($moons[$pair[1]]);
    }

    foreach ($moons as $moon) {
        $moon->applyVelocity();
    }

    if ($repetition_steps['x'] == 0) {
        $current_x_state = $moons[0]->position->x ."T". $moons[1]->position->x ."T". $moons[2]->position->x ."T". $moons[3]->position->x;
        $current_x_state .= 'X';
        $current_x_state .= $moons[0]->velocity->x ."T". $moons[1]->velocity->x ."T". $moons[2]->velocity->x ."T". $moons[3]->velocity->x;

        if (isset($moon_states['x'][$current_x_state])) {
            echo "x axis repetition reached! ";
            echo $step." steps taken\n";
            $repetition_steps['x'] = $step;
        }#*/
        $moon_states['x'][$current_x_state] = $step;
    }


    if ($repetition_steps['y'] == 0) {
        $current_y_state = $moons[0]->position->y ."T". $moons[1]->position->y ."T". $moons[2]->position->y ."T". $moons[3]->position->y;
        $current_y_state .= 'Y';
        $current_y_state .= $moons[0]->velocity->y ."T". $moons[1]->velocity->y ."T". $moons[2]->velocity->y ."T". $moons[3]->velocity->y;
        if (isset($moon_states['y'][$current_y_state])) {
            echo "y axis repetition reached! ";
            echo $step." steps taken\n";
            $repetition_steps['y'] = $step;
        }#*/
        $moon_states['y'][$current_y_state] = $step;
    }

    if ($repetition_steps['z'] == 0) {
        $current_z_state = $moons[0]->position->z ."T". $moons[1]->position->z ."T". $moons[2]->position->z ."T". $moons[3]->position->z;
        $current_z_state .= 'Z';
        $current_z_state .= $moons[0]->velocity->z ."T". $moons[1]->velocity->z ."T". $moons[2]->velocity->z ."T". $moons[3]->velocity->z;
        if (isset($moon_states['z'][$current_z_state])) {
            echo "z axis repetition reached! ";
            echo $step." steps taken\n";
            $repetition_steps['z'] = $step;
        }#*/
        $moon_states['z'][$current_z_state] = $step;
    }

    if (($repetition_steps['x'] > 0) &&
        ($repetition_steps['y'] > 0) &&
        ($repetition_steps['z'] > 0)
    ) {
        echo "all axis repeated. Breaking \n";
        break;
    }


    //stop the program before the sun engulfs the earth
    if ($step >= 10000000) {
        var_dump($moon_states);
        die("END OF PROGRAM\n");
    }
}

$lcm = gmp_lcm($repetition_steps['x'], $repetition_steps['y']);
$lcm = gmp_lcm($repetition_steps['z'], $lcm);
var_dump($lcm);
