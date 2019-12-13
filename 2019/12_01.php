<?php


/*$test_moons = 
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

$input_moons = 
	'<x=-1, y=7, z=3>
	<x=12, y=2, z=-13>
	<x=14, y=18, z=-8>
	<x=17, y=4, z=-4>';
#*/

Class Vector {
	private $values = null;
	
	public function __construct($value) {
		if(is_array($value)) {
			$this->values = $value;
		}
		if(is_string($value)) {
			$this->values = static::parseString($value);
		}
	}
	
	public static function parseString($vector_string) {
		$reg_exp = '#\<x\=(-?\d+), y\=(-?\d+)\, z\=(-?\d+)\>#';
		$vs = trim($vector_string);
		$r = preg_match($reg_exp, $vector_string, $matches);
		if(!$r) {
			throw new Exception("Could not parse vector: " . $vector_string);
		}
		array_shift($matches);
		$values = array_map("intval",$matches);
		return $values;
	}
	
	public function add(Vector $vector) {
		foreach([0=>'x',1=>'y',2=>'z'] as $key => $component) {
			$this->values[$key] += $vector->$component;
		}
	}

	public function sum() {
		$temp = array_map('abs',$this->values);
		return array_sum($temp);
		
	}
	public function __get($key) {
		switch($key) {
			case 'x':
				return $this->values[0];
			case 'y':
				return $this->values[1];
			case 'z':
				return $this->values[2];
		}
	}
	
	public function __set($key, $value) {
		switch($key) {
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
	
	public function __toString() {
		return  "" . implode(';',$this->values) .";";
	}
}

Class Moon {
	private $position;
	private $velocity;
	
	public function __construct($position) {
		$this->position = new Vector($position);
		$this->velocity = new Vector([0,0,0]);
	}
	
	public function applyGravity($otherMoon) {
		$other_position = $otherMoon->position;
		foreach(['x','y','z'] as $component) {
			if($this->position->$component == $otherMoon->position->$component) {
				continue;
			}
			if($this->position->$component < $otherMoon->position->$component) {
				$this->velocity->$component += 1;
				$otherMoon->velocity->$component -= 1;
				continue;
			}
			if($this->position->$component > $otherMoon->position->$component) {
				$this->velocity->$component -= 1;
				$otherMoon->velocity->$component += 1;
				continue;
			}
		}
	}
	public function calculateEnergy() {
		return $this->position->sum() * $this->velocity->sum();
	}

	public function applyVelocity() {
		$this->position->add($this->velocity);
	}
	
	public function __get($key) {
		switch($key) {
			case 'position':
				return $this->position;
		}
	}

	public function __toString() {
		return  "pos;" . $this->position." vel;".$this->velocity;
	}
}

function dump($moons) {
	$output = '';
	foreach($moons as $moon) {
		$output .= $moon;
	}
	return $output;
}


#read all moons
#$moon_positions = explode("\n", $test_moons);
$moon_positions = explode("\n", $input_moons);


$moons = [];
foreach($moon_positions as $position) {
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

#dump($moons);

$energy_states = [];
$moon_states = [];
$step = 0;
while(true) {
	$step += 1;
	
	if(($step % 10000) == 0) {
		echo ".";
		#dump($moons);
	}
	if(($step % 1000000) == 0) {
		echo "\n";
		#dump($moons);
	}

	foreach($pairs as $pair) {
		$moons[$pair[0]]->applyGravity($moons[$pair[1]]);
	}

	foreach($moons as $moon) {
		$moon->applyVelocity();
	}
	echo $moons[0].";".$moons[1].";".$moons[2]."\n";




	/*
	$current_moon_state = md5(dump($moons));

	#/*
	if(isset($moon_states[$current_moon_state])) {
		echo '#';
		echo "repeated moon state reached!";
		echo $step." steps taken\n";
		break;
	}#*/
	//$moon_states[$current_moon_state] = $step;

	if($step >= 1000) {
		#var_dump($moon_states);die();
		$energy = 0;
		foreach($moons as $moon) {
			$energy += $moon->calculateEnergy();
		}
		var_dump($energy);
		die("END OF PROGRAM\n");
	}#*/
}
#var_dump($step);
#var_dump($moon_states); 
#var_dump($energy_states);

