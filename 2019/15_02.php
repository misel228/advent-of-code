<?php

$input_maze = file_get_contents('15_02_input.txt');

$input_maze =' ##   
#  ## 
# #  #
# O # 
 ###  ';

$maze = new Maze2($input_maze);
$minute = 0;

echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
echo $maze->draw();
echo $minute."\n";
readline('begin');

try {
	while(($new_oxigen = $maze->moreOxigen()) > 0) {
		$minute += 1;
		echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
		echo $maze->draw();
		echo $minute."\n";
		readline('continue');
	}
}  catch (ExitException $e) {
    #echo $maze->draw();
	echo $e->getMessage()."\n";
    var_dump($minute);
    die("END OF PROGRAM\n");
}


class Maze2 {
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

    public function __construct($input_maze) {
		
		$rows = explode("\n",$input_maze);
		$this->maze = array_map("str_split", $rows);

    }

	public function moreOxigen() {
		$oxygen_at = $this->getAllOxygenPosition();
		$new_oxigen = 0;
		foreach($oxygen_at as $coordinates) {
			$s = $this->getSurroundings($coordinates);
			$to_be_filled = array_filter($s, function($field) {return ($field == ' ');});
			$to_be_filled = array_keys($to_be_filled);
			#readline('continue');
			foreach($to_be_filled as $direction) {
				$field = [
					$coordinates[static::Y],
					$coordinates[static::X],
				];
				switch($direction) {
					case 1: # north
						$field[static::Y]--;
						break;
					case 2: # south
						$field[static::Y]++;
						break;
					case 3: # west
						$field[static::X]--;
						break;
					case 4: # east
						$field[static::X]++;
						break;
				}
				$this->setField($field[static::Y],$field[static::X], 'O');
				$new_oxigen += 1;
			}
			
		}
		return $new_oxigen;
	}

	public function getAllOxygenPosition() {
		$oxygen_rows = array_filter($this->maze, 'static::row_contains_oxygen');
		$coordinates = [];
		foreach($oxygen_rows as $y => $row) {
			$oxygen_at = array_filter($row, function($field) {return ($field == 'O');});
			$oxygen_at = array_keys($oxygen_at);
			foreach($oxygen_at as $x) {
				$coordinates[] = [$y,$x];
			}
		}
		return $coordinates;
	}
	
	public static function row_contains_oxygen($row) {
		return in_array('O', $row);
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
            foreach($row as $x => $column) {
                $output .= $column;
            }
            $output .= "\n";
        }
        return $output;
    }
	
	
	public function getSurroundings($position) {
		$surroundings = [];
		#north
		$surroundings[static::NORTH] = @$this->maze[$position[static::Y] - 1][$position[static::X]];
		#south
		$surroundings[static::SOUTH] = @$this->maze[$position[static::Y] + 1][$position[static::X]];
		#west
		$surroundings[static::WEST]  = @$this->maze[$position[static::Y]][$position[static::X] - 1];
		#east
		$surroundings[static::EAST]  = @$this->maze[$position[static::Y]][$position[static::X] + 1];
		return $surroundings;
	}
}
