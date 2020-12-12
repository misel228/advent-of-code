<?php

$input = 
'F10
N3
F7
R90
F11'
;


$input      = file_get_contents('12_input.txt');

$input = explode("\n", $input);
$input = array_map('trim', $input);

$instructions = array_filter($input);

define('X', 0);
define('Y', 1);

$ship = new \Ship();

foreach($instructions as $step => $instruction) {

	echo str_pad($step, 5, ' ', STR_PAD_LEFT)."\t";
	echo $instruction;
	
	$ship->move($instruction);

	echo "\t".$ship->direction;
	echo "\t".str_pad($ship->position[X], 5, ' ', STR_PAD_LEFT);
	echo "\t".str_pad($ship->position[Y], 5, ' ', STR_PAD_LEFT);
	echo "\t".str_pad($ship->way_point[X], 5, ' ', STR_PAD_LEFT);
	echo "\t".str_pad($ship->way_point[Y], 5, ' ', STR_PAD_LEFT);
	echo "\n";
	#var_dump($ship->position);
}

list($x, $y) = $ship->position;
var_dump($ship->position);
var_dump(abs($x) + abs($y));
die("END OF PROGRAM\n");


class Ship {
	
	public $position = [];
	public $direction = 'E';
	public $way_point = [10,1];
	private $operations = ['N','S','E','W','L','R','F'];

	public function __construct($x = 0, $y = 0) {
		$this->position[X] = $x;
		$this->position[Y] = $y;
	}
	
	public function move($instruction) {
		list($operation, $parameter) = $this->decode($instruction);
		#		var_dump($operation, $parameter);die();
		
		switch($operation) {
			case 'N':
				$this->way_point[Y] += $parameter;
				break;
			case 'S':
				$this->way_point[Y] -= $parameter;
				break;
			case 'E':
				$this->way_point[X] += $parameter;
				break;
			case 'W':
				$this->way_point[X] -= $parameter;
				break;
			case 'L':
				$this->turn_left($parameter);
				break;
			case 'R':
				$this->turn_right($parameter);
				break;
			case 'F':
				$this->move_forward($parameter);
				break;
			default:
				throw new Exception ("How did you get here?");
		}
		
	}

	private function turn_left($angle) {
		$turns = $angle / 90;
		for($i = 0; $i < $turns; $i += 1) {
			$this->one_turn_left();
		}
	}

	private function one_turn_left() {
		$temp = $this->way_point;
		$this->way_point[X] = -1 * $temp[Y];
		$this->way_point[Y] = $temp[X];
	}


	private function turn_right($angle) {
		$turns = $angle / 90;
		for($i = 0; $i < $turns; $i += 1) {
			$this->one_turn_right();
		}
	}

	private function one_turn_right() {
		$temp = $this->way_point;
		$this->way_point[X] = $temp[Y];
		$this->way_point[Y] = -1 * $temp[X];
	}

	private function move_forward($times) {
		for($i = 0; $i < $times; $i += 1) {
			$this->position[X] += $this->way_point[X];
			$this->position[Y] += $this->way_point[Y];
		}

	}
	
	
	private function decode($instruction) {
		$operation = substr($instruction, 0 , 1);
		if(!in_array($operation, $this->operations)) {
			throw new Exception ("invalid Operator: ".$operation);
		}
		$parameter = substr($instruction, 1);
		if(!is_numeric($parameter)) {
			throw new Exception ("non-numeric paramter: ".$parameter);
		}
		return [
			$operation,
			$parameter,
		];
	}
}
