<?php

require('intcode_class_09.php');
$input_code = file_get_contents('11_input.txt');


class Hull {
    private const BLACK = '.';
    private const WHITE = '#';
	
	private $colors = [
		0 => '.',
		1 => '#',
	];

    public $hull_area;
	
	public $painted_hull_plates = [];

    public function __construct() {
		$rows = [];
		for($row = -25; $row < 80; $row++){
			$rows[$row] = [];
			for($column = -50; $column < 50; $column++) {
				$rows[$row][$column] = static::BLACK;
			}
		}
        $this->hull_area = $rows;
    }

    public function dump() {
        $output = '';
        foreach($this->hull_area as $y => $row) {
            foreach($row as $x => $column) {
                $output .= $column;
            }
            $output .= "\n";
        }
        return $output;
    }
	
	public function getColorAt($position) {
		if(!isset($this->hull_area[$position[0]][$position[1]])) {
			var_dump($position);die();
		}
		$colors_flipped = array_flip($this->colors);
		return $colors_flipped[$this->hull_area[$position[0]][$position[1]]];
	}
	
	public function setColorAt($position, $color) {
		if(empty($position)) {
			var_dump($position);die();
		}
		$paint_color = $this->colors[$color];
		#echo "paint: ".$position[0].':'.$position[1].' '.$paint_color."\n";
		$this->hull_area[$position[0]][$position[1]] = $paint_color;
		$hash = $position[0] ."T". $position[1];
		if(!in_array($hash, $this->painted_hull_plates)) {
			$this->painted_hull_plates[] = $hash;
		}
	}
}

class Robot extends IntCode {
    public $position = [0,0];
    public $direction = '^'; //  ^< > v

	// 0 - left, 1 - right
	private $turns = [
		'^' => [
			0 => '<',
			1 => '>',
		],
		'<' => [
			0 => 'v',
			1 => '^',
		],
		'v' => [
			0 => '>',
			1 => '<',
		],
		'>' => [
			0 => '^',
			1 => 'v',
		],
	];

	public function updatePosition($new_direction) {
		$this->direction = $this->turns[$this->direction][$new_direction];
		$this->move();
	}
	
	private function move() {
		switch($this->direction) {
			case '^' :
				$x = $this->position[0];
				$y = $this->position[1] + 1;
				$this->position = [$x, $y];
				break;
			case '<' :
				$x = $this->position[0] - 1;
				$y = $this->position[1];
				$this->position = [$x, $y];
				break;
			case 'v' :
				$x = $this->position[0];
				$y = $this->position[1] - 1;
				$this->position = [$x, $y];
				break;
			case '>' :
				$x = $this->position[0] + 1;
				$y = $this->position[1];
				$this->position = [$x, $y];
				break;
			default:
				throw new Exception("invalid direction");
		}
	}
	
}

try {
    $robot = new Robot($input_code);
    $robot->echo_and_continue = false;
    #$robot->debug = true;

    $hull = new Hull();
	#$hull->setColorAt($robot->position, 1);
	#var_dump($hull->hull_area);

	while(true) {

		$color = $hull->getColorAt($robot->position);
		$painted_color = $robot->run_code([$color]);
		$new_direction = $robot->run_code();

		$hull->setColorAt($robot->position, $painted_color);
		$robot->updatePosition($new_direction);
		#var_dump($painted_color);
		#var_dump($new_direction);
		
	}

	
	
}  catch (ExitException $e) {
    echo $hull->dump();
	
#	var_dump($hull->painted_hull_plates);
	var_dump(count($hull->painted_hull_plates));
    #die();
}

