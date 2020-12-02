<?php

require('intcode_class_09.php');
$input_code = file_get_contents('17_input.txt');




try {
    $camera = new IntCode($input_code);
    $camera->echo_and_continue = false;
    #$camera->debug = true;

    $block_counter = 0;
	$step = 0;
	$direction = 1;
	$image_string = '';
    while(true) {

		$pixel = $camera->run_code();
		$image_string .= chr($pixel);

    }
}  catch (ExitException $e) {
	echo $e->getMessage()."\n";
    var_dump($block_counter);
}

$image = new Image($image_string);
/*$intersections = $image->findIntersections();

foreach($intersections as $int) {
	$image->setPixel($int, 'O');
}*/

echo $image->draw();

#var_dump($intersections);

$path = $image->traverseScaffold();
echo implode(',',$path)."\n";
die();
var_dump($path);

$commands = find_command_parts($path);

//wake up vacuum tube
$vacuum_code = '2' . substr($input_code, 1);
$vacuum = new IntCode($vacuum_code);

$dust_cleaned = $vacuum->run_code($commands);



$alignment_parameters = array_map('Image::calculate_alignment_parameters',$intersections);
var_dump(array_sum($alignment_parameters));
die("END OF PROGRAM\n");

class Image {
	private $image = '';
	private const NORTH = 1;
	private const SOUTH = 2;
	private const WEST  = 3;
	private const EAST  = 4;
	private const X = 1;
	private const Y = 0;

    public function __construct($input_maze) {

		$rows = explode("\n",$input_maze);
		$this->image = array_map("str_split", $rows);

    }

	public function findIntersections() {
		$coordinates = [];

		foreach($this->image as $y => $row) {
			foreach($row as $x => $pixel) {
				$pixel = $this->image[$y][$x];
				if($pixel != '#') {
					continue;
				}
				$s = $this->getSurroundings([$y,$x]);
				$s = array_filter($s, function($item) {return $item == '#';});
				if(count($s) != 4) {
					continue;
				}
				$coordinates[] = [$y, $x];
			}
		}
		return $coordinates;

	}

    public function draw() {
        $output = '';
        foreach($this->image as $y => $row) {
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
		$surroundings[static::NORTH] = @$this->image[$position[static::Y] - 1][$position[static::X]];
		#south
		$surroundings[static::SOUTH] = @$this->image[$position[static::Y] + 1][$position[static::X]];
		#west
		$surroundings[static::WEST]  = @$this->image[$position[static::Y]][$position[static::X] - 1];
		#east
		$surroundings[static::EAST]  = @$this->image[$position[static::Y]][$position[static::X] + 1];
		return $surroundings;
	}

	public function setPixel($coordinates, $value) {
		$this->image[$coordinates[static::Y]][$coordinates[static::X]] = $value;
	}

	public static function calculate_alignment_parameters($coordinates) {
		return $coordinates[0] * $coordinates[1];
	}

	private $vacuum_direction;
	private $vacuum_position = [0,0];

	public function traverseScaffold(){
        $this->getVacuumDetails();
        $path = [];
        while(true) {
            //if you can go forward and count steps
            $steps = 0;
            while($this->canGoForward()) {
                $this->goForward();
                $steps += 1;
            }
            if($steps > 0) {
                $path[] = $steps;
            }
            //you can't go forward any further
            $turn = $this->turnAround(); //every now and then I get a little bit lonely
            if($turn == false) {
                return $path;
            }
			$path[] = $turn;
        }
	}

	private function getVacuumDetails() {
        foreach($this->image as $y => $row) {
            if($x = array_search(('v'), $row)) {
                $this->vacuum_direction = static::SOUTH;
                $this->vacuum_position = [$y,$x];
                break;
            }
            if($x = array_search(('^'), $row)) {
                $this->vacuum_direction = static::NORTH;
                $this->vacuum_position = [$y,$x];
                break;
            }
            if($x = array_search(('<'), $row)) {
                $this->vacuum_direction = static::WEST;
                $this->vacuum_position = [$y,$x];
                break;
            }
            if($x = array_search(('>'), $row)) {
                $this->vacuum_direction = static::EAST;
                $this->vacuum_position = [$y,$x];
                break;
            }
        }
	}

	private function canGoForward() {
        switch($this->vacuum_direction) {
            case static::NORTH:
                $next_field = @$this->image[$this->vacuum_position[static::Y] - 1][$this->vacuum_position[static::X]];
                break;
            case static::SOUTH:
                $next_field = @$this->image[$this->vacuum_position[static::Y] + 1][$this->vacuum_position[static::X]];
                break;

            case static::WEST:
                $next_field = @$this->image[$this->vacuum_position[static::Y]][$this->vacuum_position[static::X] - 1];
                break;

            case static::EAST:
                $next_field = @$this->image[$this->vacuum_position[static::Y]][$this->vacuum_position[static::X] + 1];
                break;
        }
        return $next_field == '#'; //#
	}

    private function goForward() {
        switch($this->vacuum_direction) {
            case static::NORTH:
                $this->vacuum_position[static::Y] -= 1;
                break;
            case static::SOUTH:
                $this->vacuum_position[static::Y] += 1;
                break;

            case static::WEST:
                $this->vacuum_position[static::X] -= 1;
                break;

            case static::EAST:
                $this->vacuum_position[static::X] += 1;
                break;
        }
        return;
	}

	private function turnAround() {
		$turnable_fields = [
            static::NORTH => [
                static::WEST => 'L',
                static::EAST => 'R',
            ],
            static::SOUTH => [
                static::WEST => 'R',
                static::EAST => 'L',
            ],
            static::EAST => [
                static::NORTH => 'L',
                static::SOUTH => 'R',
            ],
            static::WEST => [
                static::NORTH => 'R',
                static::SOUTH => 'L',
            ],
		];
		$reversed_directions = [
            static::NORTH => static::SOUTH,
            static::SOUTH => static::NORTH,
            static::EAST => static::WEST,
            static::WEST => static::EAST,
		];
		$s = $this->getSurroundings($this->vacuum_position);
		//remove where we've come from
		unset($s[$reversed_directions[$this->vacuum_direction]]);

		$s = array_filter($s, function($item) {return $item == '#';});

		$directions = array_keys ( $s );
		//end of path
        if(!isset($turnable_fields[$this->vacuum_direction][$directions[0]])) {
			return false;
        }
		$turn =  $turnable_fields[$this->vacuum_direction][$directions[0]];
		$this->vacuum_direction = $directions[0];
		return $turn;
	}
}

