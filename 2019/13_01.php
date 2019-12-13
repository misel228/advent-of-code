<?php

require('intcode_class_09.php');
$input_code = file_get_contents('13_input.txt');




try {
    $robot = new IntCode($input_code);
    $robot->echo_and_continue = false;
    #$robot->debug = true;

    $play_area = new PlayArea();
    $block_counter = 0;
    while(true) {
        $x = $robot->run_code();
        $y = $robot->run_code();
        $tile_id = $robot->run_code();

        $play_area->setTile($x,$y,$tile_id);
        if($tile_id==2) {
            $block_counter += 1;
        }
    }
}  catch (ExitException $e) {
    echo $play_area->dump();
    var_dump($block_counter);
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
