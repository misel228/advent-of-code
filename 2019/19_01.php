<?php

require('intcode_class_19.php');
$input_code = file_get_contents('19_input.txt');

$input_code = preg_replace('/\s/', '', $input_code);
$input_code = explode(',', $input_code);
$check = array_filter($input_code, function ($item) {
	return !is_numeric($item);
});

if (!empty($check)) {
	throw new Exception("non code elements in input found!");
}


$camera = new IntCode($input_code);
$camera->echo_and_continue = false;

$image_string = '';
$fields_affected = 0;
$field_size = 100;
for($x = 0; $x < $field_size; $x++) {
	for($y = 0; $y < $field_size; $y++){
		$pixel = $x+$y;
		try {
			#$camera->int_code_reset();
			$camera = new IntCode($input_code);
			$camera->echo_and_continue = false;
			$pixel = $camera->run_code([$x,$y]);
			$fields_affected += $pixel;
		}  catch (ExitException $e) {
			echo $e->getMessage()."\n";
		} finally {
			$image_string .= $pixel;
		}
		#echo $pixel;
	}
	$image_string .= "\n";

}

$image = new Image($image_string);
echo $image->draw();
var_dump($fields_affected);
die("END OF PROGRAM\n");

/*
$intersections = $image->findIntersections();

foreach($intersections as $int) {
	$image->setPixel($int, 'O');
}
*/

#var_dump($intersections);

#$alignment_parameters = array_map('Image::calculate_alignment_parameters',$intersections);
#var_dump(array_sum($alignment_parameters));

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