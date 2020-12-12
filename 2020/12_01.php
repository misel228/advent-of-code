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


$ship = new \Ship();

foreach($instructions as $step => $instruction) {

	echo str_pad($step, 5, ' ', STR_PAD_LEFT)."\t";
	echo $instruction;
	
	$ship->move($instruction);

	echo "\t".$ship->direction;
	echo "\t".str_pad($ship->position[0], 5, ' ', STR_PAD_LEFT);
	echo "\t".str_pad($ship->position[1], 5, ' ', STR_PAD_LEFT);
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
	private $operations = ['N','S','E','W','L','R','F'];

	public function __construct($x = 0, $y = 0) {
		$this->position[0] = $x;
		$this->position[1] = $y;
	}
	
	public function move($instruction) {
		list($operation, $parameter) = $this->decode($instruction);
		#		var_dump($operation, $parameter);die();
		
		switch($operation) {
			case 'N':
				$this->position[1] += $parameter;
				break;
			case 'S':
				$this->position[1] -= $parameter;
				break;
			case 'E':
				$this->position[0] += $parameter;
				break;
			case 'W':
				$this->position[0] -= $parameter;
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
		$turns = [
			'N' => [
				 90 => 'W',
				180 => 'S',
				270 => 'E',
			],
			'S' =>  [
				 90 => 'E',
				180 => 'N',
				270 => 'W',
			],
			'E' =>  [
				 90 => 'N',
				180 => 'W',
				270 => 'S',
			],
			'W' =>  [
				 90 => 'S',
				180 => 'E',
				270 => 'N',
			],
		];
		$this->direction = $turns[$this->direction][$angle];
	}

	private function turn_right($angle) {
		$turns = [
			'N' =>  [
				 90 => 'E',
				180 => 'S',
				270 => 'W',
			],
			'S' => [
				 90 => 'W',
				180 => 'N',
				270 => 'E',
			],
			'E' =>  [
				 90 => 'S',
				180 => 'W',
				270 => 'N',
			],
			'W' =>  [
				 90 => 'N',
				180 => 'E',
				270 => 'S',
			],
		];
		$this->direction = $turns[$this->direction][$angle];
	}

	private function move_forward($length) {
		switch($this->direction) {
			case 'N':
				$this->position[1] += $length;
				break;
			case 'S':
				$this->position[1] -= $length;
				break;
			case 'E':
				$this->position[0] += $length;
				break;
			case 'W':
				$this->position[0] -= $length;
				break;
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
