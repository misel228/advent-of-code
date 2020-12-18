<?php

$input = file_get_contents('17_input.txt');
//160 low
//320 high


/*
$input = '.#.
..#
###';
#*/


$cube = new \ConwayCubeOfLive($input);
$cube->draw();


for($i = 0; $i < 2; $i += 1) {
	$cube->iterate();
	$cube->draw();
	var_dump($cube->countActive());
}


var_dump($cube->countActive());
die("END OF PROGRAM");


class ConwayCubeOfLive {

	private $cube;
	
	
	
	public function __construct($input) {
		$rows = explode("\n", $input);
		$rows = array_map('trim', $rows);
		$rows = array_map('str_split', $rows);
		$this->cube[0] = $rows;
		
		//var_dump($this->getSurroundingCubes(0, 2, 1));die();
	}
	
	public function iterate() {
		list($z_min, $z_max) = $this->getCurrentDimensions('z');
		list($y_min, $y_max) = $this->getCurrentDimensions('y');
		list($x_min, $x_max) = $this->getCurrentDimensions('x');
	
		$new_cube = [];

		for($z = ($z_min - 1); $z <= ($z_max + 1); $z += 1) {
			#echo "z".$z."\n";
			$new_cube[$z] = [];
			for($y = ($y_min - 1); $y <= ($y_max + 1); $y += 1) {
				#echo "\ty".$y."\n";
				$new_cube[$z][$y] = [];
				for($x = ($x_min - 1) ; $x <= ($x_max + 1); $x += 1) {
					#echo "\t\tx".$x;

					$cube_value = $this->getCubeValue($z, $y, $x);
					#echo "\t".$cube_value."\n";
					
					$num_cubes = $this->getSurroundingCubes($z, $y, $x);
					$new_cube[$z][$y][$x] = $cube_value;

					if($cube_value == '#') {
						if(in_array($num_cubes,  [2,3])) {
							$new_cube[$z][$y][$x] = '#';
							continue;
						}
						$new_cube[$z][$y][$x] = '.';
						continue;
						
					}

					if($cube_value == '.') {
						if($num_cubes == 3) {
							$new_cube[$z][$y][$x] = '#';
							continue;
						}
						$new_cube[$z][$y][$x] = '.';
						continue;
					}
				}
			}
		}
		#var_dump($new_cube);#die();
		$this->cube = $new_cube;
	}

	private function getCubeValue($z, $y, $x) {
		if(!isset($this->cube[$z][$y][$x])) {
			return '.';
		}
		return $this->cube[$z][$y][$x];
	}

	private function getSurroundingCubes($z, $y, $x) {
		$count_active = 0;
		for($i_z = ($z - 1); $i_z <= ($z + 1); $i_z += 1) {
			#echo "z.".$i_z;
			$new_cube[$z] = [];
			for($i_y = ($y - 1); $i_y <= ($y + 1); $i_y += 1) {
				#echo "\ty.".$i_y."\n";
				$new_cube[$z][$y] = [];
				for($i_x = ($x - 1) ; $i_x <= ($x + 1); $i_x += 1) {
					#echo "\t\tx".$i_x . "\n";

					//skip actual cube
					if($i_z == $z && $i_y == $y && $i_x == $x) {
						#echo "HUZZA";
						continue;
					}
					$cube_value = $this->getCubeValue($i_z, $i_y, $i_x);
					if($cube_value == '#') {
						$count_active += 1;
					}
				}
			}
		}
		#var_dump($count_active);die();
		return $count_active;
		
	}

	public function countActive() {
		$count = 0;
		foreach($this->cube as $slice) {
			foreach($slice as $row) {
				$count = array_reduce($row, 'static::count_X', $count);
			}
		}
		return $count;
	}
	
	private static function count_X($count, $item) {
		if($item == '#') {
			return $count + 1;
		}
		return $count;
	}

	private function getCurrentDimensions($axis) {
		switch($axis) {
			case 'z':
				$min = min(array_keys($this->cube));
				$max = max(array_keys($this->cube));
				return [$min, $max];
			case 'y':
				$min = 1000;
				$max = -1000;
				foreach($this->cube as $slice) {
					$min = min($min, min(array_keys($slice)));
					$max = max($min, max(array_keys($slice)));
				}
				return [$min, $max];
			case 'x':
				$min = 1000;
				$max = -1000;
				foreach($this->cube as $slice) {
					foreach($slice as $row) {
						$min = min($min, min(array_keys($row)));
						$max = max($max, max(array_keys($row)));
					}
				}
				return [$min, $max];
			default:
				throw new Exception('invalid axis: '. $axis);
		}
	}
	
	
	public function draw() {
		$output = '';
		foreach($this->cube as $index => $slice) {
			$output .= "z=".$index."\n";
			foreach($slice as $row) {
				$output .= implode("",$row)."\n";
			}
			$output .= "\n\n";
		}
		
		echo $output . "\n";
	}
}

