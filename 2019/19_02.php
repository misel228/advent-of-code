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
$margin = 5;
$field_size_y = 1100;
$field_size_x = 1100;
#$field_size_y * 3 /4 + $margin;

$assumed_xc_min = 632;
$assumed_yc_min = 966;

$assumed_xc_max = 732;
$assumed_yc_max = 1066;

$assumed_xc_min = 628;
$assumed_yc_min = 962;

$assumed_xc_max = 728;
$assumed_yc_max = 1062;

for($y = 600; $y < $field_size_y; $y++) {
	#$y = 1000;
	
	for($x = 500; $x < $field_size_x; $x++){
		//determined by rendering the entire 1000th row
		#/*
		$y_l = 1000/593 * $x;
		$y_u = 1000/758 * $x;

		$image_value = 0;

		if((abs($y - $y_l) <= $margin) || (abs($y_u - $y) <= $margin) ) {
			try {
				#$camera->int_code_reset();
				$camera = new IntCode($input_code);
				$camera->echo_and_continue = false;
				$pixel = $camera->run_code([$x,$y]);
			}  catch (ExitException $e) {
				echo $e->getMessage()."\n";
			} finally {
				$image_value += $pixel;
			}

		}#*/

		if(($x >= $assumed_xc_min) &&($y >= $assumed_yc_min) &&($x < $assumed_xc_max) &&($y < $assumed_yc_max) ) {
			$image_value += 2;
		}

		if(($y < $y_l) || ($y_u < $y)) {
			$image_string .= '.';
			continue;
		}

		$image_string .= $image_value;
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