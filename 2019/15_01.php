<?php

require('intcode_class_09.php');
$input_code = file_get_contents('15_input.txt');




try {
    $robot = new IntCode($input_code);
    $robot->echo_and_continue = false;
    #$robot->debug = true;

    $maze = new Maze();
    $block_counter = 0;
	$step = 0;
	$direction = 1;
    while(true) {

		$ret = $robot->run_code([$direction]);
		$position = $maze->step($direction, $ret);
		$block_counter += 1;
		$s = $maze->getSurroundings();
		$s = array_filter($s, function($field) {return ($field == -1);});
		if(empty($s)) {
			$s = $maze->getSurroundings();
			$s = array_filter($s, function($field) {return ($field != 1);});
			asort($s);
			if(in_array(200, $s)) {
				throw new ExitException("too many backsteps");
			}
		}
		$directions = array_keys($s);
		$direction = $directions[0];

		#/*
		if($ret == 2) {
			break;
		}#*/
		//clear screen and paint again
		echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
		echo $maze->draw();
		echo implode(":",$position)." ".$ret." ".$direction."\n";
		#readline('continue');

    }
}  catch (ExitException $e) {
    #echo $maze->draw();
	echo $e->getMessage()."\n";
    var_dump($block_counter);
    die("END OF PROGRAM\n");
}


class Maze {
    private const BLACK = '.';
    private const WHITE = '#';
	
	private const X = 1;
	private const Y = 0;

	private const NORTH = 1;
	private const SOUTH = 2;
	private const WEST  = 3;
	private const EAST  = 4;

    private $field_types = [
	   -1 => '.', #unknown
        0 => ' ', #nothing
        1 => '#', #wall
        2 => '+', #block
        3 => '_', #paddle
        4 => 'o', #ball
		'D' => 'D',
		'%' => '%',
		'v' => '_',
    ];

    private $maze;

    public $position = [0,0];
    public $direction = '^'; //  ^< > v

    public function __construct() {
		
		$rows = [];
		for($row = -25; $row < 25; $row++){
			$rows[$row] = [];
			for($column = -25; $column < 25; $column++) {
				$rows[$row][$column] = -1;
			}
		}
		
		$this->maze = $rows;
    }

	public function step($direction, $return_code) {
		echo __FUNCTION__.":".$direction.":".$return_code."\n";
		#readline("step");
		switch($return_code) {
			case 0:
				$wall = [
					$this->position[static::Y],
					$this->position[static::X],
				];
				switch($direction) {
					case 1: # north
						$wall[static::Y]--;
						break;
					case 2: # south
						$wall[static::Y]++;
						break;
					case 3: # west
						$wall[static::X]--;
						break;
					case 4: # east
						$wall[static::X]++;
						break;
				}
				echo "wall at ".implode(":",$wall)."\n";
				$this->setField($wall[static::Y],$wall[static::X], 1);
				return $wall;
				break;
			case 2:
				$this->setField($this->position[static::Y],$this->position[static::X], 4);
				throw new ExitException("Oxygen container at ".implode(':',$this->position));
				break;#*/			case 2:
			case 1:
				switch($direction) {
					case 1: # north
						$this->position[static::Y] -= 1;
						break;
					case 2: # south
						$this->position[static::Y] += 1;
						break;
					case 3: # west
						$this->position[static::X] -= 1;
						break;
					case 4: # east
						$this->position[static::X] += 1;
						break;
				}
				$current_field_value = $this->maze[$this->position[static::Y]][$this->position[static::X]];
				
				$new_field_value = 0;
				if($current_field_value != -1) {
					#var_dump($current_field_value);die();
					$new_field_value = $current_field_value + 10;
				}
				$this->setField($this->position[static::Y],$this->position[static::X], $new_field_value);

				return $this->position;
				break;
			default: throw new Exception("WHAT!");
		}
	}

    public function setField($y,$x,$tile_id) {
        $this->maze[$y][$x] = $tile_id;
    }

    public function draw() {
        $output = '';
        foreach($this->maze as $y => $row) {
			#var_dump($y,$row);die();
            foreach($row as $x => $column) {
				if($y == 0 && $x == 0) {
					$column = '%';
				}
				if($y == $this->position[static::Y] && $x == $this->position[static::X]) {
					$column = 'D';
				}
				if($column >= 10) {
					$column = 'v';
				}
                $output .= $this->field_types[$column];
            }
            $output .= "\n";
        }
        return $output;
    }
	
	
	public function getSurroundings() {
		$surroundings = [];
		#north
		$surroundings[static::NORTH] = $this->maze[$this->position[static::Y] - 1][$this->position[static::X]];
		#south
		$surroundings[static::SOUTH] = $this->maze[$this->position[static::Y] + 1][$this->position[static::X]];
		#west
		$surroundings[static::WEST]  = $this->maze[$this->position[static::Y]][$this->position[static::X] - 1];
		#east
		$surroundings[static::EAST]  = $this->maze[$this->position[static::Y]][$this->position[static::X] + 1];
		
		return $surroundings;
	}
}
