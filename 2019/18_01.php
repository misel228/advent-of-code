<?php

require('intcode_class_09.php');
$maze_string = file_get_contents('18_input.txt');


$test_mazes = [
	[
		'maze' => '#########
#b.A.@.a#
#########',
		'steps' => 8
	],
	[
		'maze' => '########################
#f.D.E.e.C.b.A.@.a.B.c.#
######################.#
#d.....................#
########################',
		'steps' => 86
	],
	[
		'maze' => '########################
#...............b.C.D.f#
#.######################
#.....@.a.B.c.d.A.e.F.g#
########################',
		'steps' => 132
	],
	[
		'maze' => '#################
#i.G..c...e..H.p#
########.########
#j.A..b...f..D.o#
########@########
#k.E..a...g..B.n#
########.########
#l.F..d...h..C.m#
#################',
		'steps' => 136
	],
	[
		'maze' => '########################
#@..............ac.GI.b#
###d#e#f################
###A#B#C################
###g#h#i################
########################',
		'steps' => 81
	],
];

foreach($test_mazes as $test) {
	$steps = solve($test['maze']);
	if($steps == $test['steps']) {
		echo "SUCCESS\n";
		continue;
	}
	echo "FAILED\n";
	die();
	
}

solve($maze_string);

function solve($maze_string) {
	$maze = new Maze3($maze_string);

	echo $maze->draw();

	$entrance = $maze->getEntrancePosition();
	var_dump($entrance);

	$key_positions = $maze->getKeyPositions();
	ksort($key_positions);

	$keys_collected = array_map(function ($key) {return false;}, $key_positions);
	var_dump($keys_collected);

	$door_positions = $maze->getDoorPositions();
	#var_dump($door_positions);die();
	
	$maze->position = $entrance['@'];

	$steps = 0;
	do {
		$keys = array_search(false, $keys_collected);
		$steps += $maze->findPath($keys[0]);
	} while (count($keys) > 0);

	return $steps;
}


class Maze3 {
	private $maze = '';
	private const NORTH = 1;
	private const SOUTH = 2;
	private const WEST  = 3;
	private const EAST  = 4;
	private const X = 1;
	private const Y = 0;

    public function __construct($input_maze) {
		
		$rows = explode("\n",$input_maze);
		$this->maze = array_map("str_split", $rows);

    }
	
	public $position = [];

    public function draw() {
        $output = '';
        foreach($this->maze as $y => $row) {
            foreach($row as $x => $column) {
				$column = str_replace(['#','.'],['_', ' '], $column);
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
	
	public function setPixel($coordinates, $value) {
		$this->maze[$coordinates[static::Y]][$coordinates[static::X]] = $value;
	}

	public function getEntrancePosition() {
		$function = function($field) {return (preg_match('#@#', $field));};
		return $this->getPositions($function);
	}

	public function getKeyPositions() {
		$function = function($field) {return (preg_match('#[a-z]#', $field));};
		return $this->getPositions($function);
	}

	public function getDoorPositions() {
		$function = function($field) {return (preg_match('#[A-Z]#', $field));};
		return $this->getPositions($function);
	}

	private function getPositions($function) {
		$coordinates = [];
		foreach($this->maze as $y => $row) {
			$key_at = array_filter($row, $function);
			foreach($key_at as $x => $key) {
				$coordinates[$key] = [$y,$x];
			}
		}
		return $coordinates;
	}
	
	public function findPath($key) {
		$my_path = $this->calculatePath($this->position, $this->key_positions[$key]);
		$doors = $this->closedDoorsOnPath($path);
		
		$keys_required = array_map('strtolower', $doors);
		$steps = 0;
		do {
			$key = array_shift($keys_required);
			$steps += $maze->findPath($key);
		} while (count($keys_required) > 0);

		return $steps;
	}

	private $open_nodes = []; # set of nodes to be evaluated
	private $closed_nodes = []; # set of nodes already evaluated

	public function calculatePath($start, $end) {
		throw new Exception("TODO: Implement Path finding algorithm");
		#add start node to OPEN
		$current_node = new Node($start);
		$open_nodes[] = $current_node;
		
		#loop
		while(true) {
			$current_node = $this->getLowestCostOpenNode();
			#remove current from open
			#add current to closed

			#path has been found
			if($current_node == $end) {
				return $current_node->getPath();
			}
			
			
			#foreach neighbour
			foreach($current_node->getNeighbours() as $neighbour) {
				#if not traversable skip
				#if() continue
				
				#set f_cost
				$neighbour = new Node();
				$neighbour->calc($start, $end);
				$neighbour->setParent($current);
				
				#if neighbour not in OPEN, add to Open
				
			}
		}
	}
}

class Node {
	private $h_cost = 0; #
	private $f_cost = 0;
	private $cost = 0;
	private $position = [];
	
	public function __construct($position) {
		$this->position = $position;
	}
	
	public function calc($start, $end) {
		
	}
}