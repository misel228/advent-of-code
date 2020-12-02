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
		echo chr($pixel);
		$image_string .= chr($pixel);

    }
}  catch (ExitException $e) {
	echo $e->getMessage()."\n";
    var_dump($block_counter);
}

$image = new Image($image_string);
$intersections = $image->findIntersections();

foreach($intersections as $int) {
	$image->setPixel($int, 'O');
}

echo $image->draw();

#var_dump($intersections);

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
}