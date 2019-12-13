<?php

require('intcode_class_09.php');
$input_code = file_get_contents('13_input.txt');

$input_code = '2' . substr($input_code, 1);



try {
    $robot = new IntCode($input_code);
    $robot->echo_and_continue = false;
    #$robot->debug = true;

    $play_area = new PlayArea();

    $joystick_input = 0;
    $paddle_position = 0;
    $ball_position = 0;
    while(true) {
        $x = $robot->run_code([$joystick_input]);
        $y = $robot->run_code([$joystick_input]);
        $tile_id = $robot->run_code([$joystick_input]);

        if($tile_id == 3) {
            $paddle_position = $x;
        }

        if($tile_id == 4) {
            $ball_position = $x;
        }

        if($ball_position < $paddle_position) {
            $joystick_input = -1;
        } else if($ball_position > $paddle_position) {
            $joystick_input = 1;
        } else {
            $joystick_input = 0;
        }

        if($ball_position < $paddle_position) {
            $joystick_input = -1;
        }


        if(($x == -1) && ($y == 0)) {
            $score = $tile_id;
            echo $score;
            #continue;
        } else {
            $play_area->setTile($x,$y,$tile_id);
        }


        //clear screen and paint again
        echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
        echo $play_area->dump();
    }
}  catch (ExitException $e) {
    #echo $play_area->dump();
    var_dump($score);
    die("END OF PROGRAM\n");
}


class PlayArea {
    private const BLACK = '.';
    private const WHITE = '#';

    private $field_types = [
        0 => ' ', #nothing
        1 => '#', #wall
        2 => '+', #block
        3 => '_', #paddle
        4 => 'o', #ball
    ];

    private $play_area;

    public $position = [0,0];
    public $direction = '^'; //  ^< > v

    public function __construct() {
        $row = array_fill(0, 40, static::BLACK);
        $this->play_area = array_fill(0,30, $row);
    }


    public function setTile($x,$y,$tile_id) {
        $this->play_area[$y][$x] = $this->field_types[$tile_id];
    }

    public function dump() {
        $output = '';
        foreach($this->play_area as $y => $row) {
            foreach($row as $x => $column) {
                $output .= $column;
            }
            $output .= "\n";
        }
        return $output;
    }
}
