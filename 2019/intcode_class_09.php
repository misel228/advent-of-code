<?php

class IntCode
{

    private $opcode_length = [
        1 => 4, # add
        2 => 4, # multiply
        3 => 2, # input
        4 => 2, # output
        5 => 0, # jump if true  - instruction pointer is set directly
        6 => 0, # jump if false - instruction pointer is set directly
        7 => 4, # less than
        8 => 4, # equals
        9 => 2, # change relative_base
    ];

    private $instruction_pointer = 0;
    private $relative_base = 0;

    private $memory = [];

    public $debug = false;
    public $debug2 = false;
    public $echo_and_continue = true;

    public function __construct($memory, $id = 'intcode')
    {
        $this->setMemory($memory);
        $this->id = $id;
    }

    public function setMemory($memory)
    {
        $input_code = preg_replace('/\s/', '', $memory);
        $input_code = explode(',', $input_code);
        $check = array_filter($input_code, function ($item) {
            return !is_numeric($item);
        });

        if (!empty($check)) {
            throw new Exception("non code elements in input found!");
        }
        $this->memory = $input_code;
    }

    private function expandMemory($numbers)
    {
        if ($this->debug) {
            echo "Expanding Memory by {$numbers} elements\n";
        }
        $extra_memory = array_fill(0, $numbers, '0');
        $this->memory = array_merge($this->memory, $extra_memory);
    }

    private function loadFromMemory($address)
    {
        if ($address < 0) {
            throw new Exception("Invalid Memory Read Access with negative address");
        }
        if ($address > count($this->memory)) {
            $this->expandMemory($address - count($this->memory)  + 1);
        }
        return $this->memory[$address];
    }

    private function storeInMemory($address, $value)
    {
        if ($address < 0) {
            throw new Exception("Invalid Memory Write Access with negative address");
        }
        if ($address > count($this->memory)) {
            $this->expandMemory($address - count($this->memory)  + 1);
        }
        $this->memory[$address] = $value;
    }

    private function decodeInstruction($instruction)
    {
        $mode_par = [0,0,0];

        //check for immediate or relative mode
        $temp        = intval($instruction / 100);
        $instruction = intval($instruction % 100);

        $mode_par[0] = intval($temp % 10);

        $temp        = intval($temp / 10);
        $mode_par[1] = intval($temp % 10);

        $temp        = intval($temp / 10);
        $mode_par[2] = intval($temp % 10);

        $r = [$instruction, $mode_par];
        return $r;
    }

    public function run_code($input_params = [])
    {

        $profiler = array_combine(array_keys($this->opcode_length), array_fill(0, count($this->opcode_length), 0));

        while ($this->instruction_pointer < count($this->memory)) {
            $instruction = $this->memory[$this->instruction_pointer];
            if ($this->debug) {
                echo $this->id." ".$instruction;
            }
            if ($instruction == 99) {
                throw new ExitException("end of program for ".$this->id);
            }
            list($instruction, $mode_par) = $this->decodeInstruction($instruction);
            if($this->debug) {
                for($i = 1; $i < $this->opcode_length[$instruction]; $i++) {
                    echo "\t".$this->memory[$this->instruction_pointer + $i];
                }
                echo "\n";
            }

            $profiler[$instruction] += 1;
            if ($this->debug) {
                echo $this->id." ".
                    $this->instruction_pointer.":"
                    .$instruction
                    .' mode: '.implode('', $mode_par)
                    ." len: ".$this->opcode_length[$instruction]
                    ." rb: ".$this->relative_base
                    ."\n";
            }

            if ($this->debug2) {
                readline('continue');
            }
            $pos_op = [];
            $op = [];
            switch ($instruction) {
                case 1: #add
                    $pos_op = $this->load_addresses($mode_par, 3);

                    $op[0]   = $this->loadFromMemory($pos_op[0]);
                    $op[1]   = $this->loadFromMemory($pos_op[1]);
                    #$pos_res = $this->memory[$this->instruction_pointer + 3];
                    $pos_res = $pos_op[2];

                    $this->storeInMemory($pos_res, $op[0] + $op[1]);
                    break;
                case 2: #multiply
                    $pos_op = $this->load_addresses($mode_par, 3);

                    $op[0]   = $this->loadFromMemory($pos_op[0]);
                    $op[1]   = $this->loadFromMemory($pos_op[1]);
                    #$pos_res = $this->memory[$this->instruction_pointer + 3];
                    $pos_res = $pos_op[2];
                    $this->storeInMemory($pos_res, $op[0] * $op[1]);
                    break;

                case 3: #input
                    $pos_op = $this->load_addresses($mode_par, 1);
                    if (count($input_params) == 0) {
                        if ($this->debug) {
                            echo "Waiting\n";
                        }
                        return -1; # waiting for input to continue;
                    }
                    $temp = array_shift($input_params);
                    if ($this->debug) {
                        echo "read from input: ".$temp."\n";
                    }
                    $this->storeInMemory($pos_op[0], $temp);
                    break;
                case 4: #output
                    $pos_op = $this->load_addresses($mode_par, 1);
                    $op[0] = $this->loadFromMemory($pos_op[0]);
                    if ($this->echo_and_continue) {
                        if ($this->debug) {
                            echo "Output: ";
                        }
                        echo $op[0] . "\n";
                        break;
                    }
                    if ($this->debug) {
                        echo "Output Wait for restart\n";
                    }
                    $this->instruction_pointer += $this->opcode_length[$instruction];
                    return $op[0];
                    break;
                case 5: #jump if true
                    $pos_op = $this->load_addresses($mode_par, 2);

                    if ($this->loadFromMemory($pos_op[0]) > 0) {
                        $this->instruction_pointer = $this->loadFromMemory($pos_op[1]);
                        break;
                    }
                    $this->instruction_pointer += 3;
                    break;

                case 6: #jump if false
                    $pos_op = $this->load_addresses($mode_par, 2);

                    if ($this->loadFromMemory($pos_op[0]) == 0) {
                        $this->instruction_pointer = $this->loadFromMemory($pos_op[1]);
                        break;
                    }
                    $this->instruction_pointer += 3;
                    break;

                case 7: #less than
                    $pos_op = $this->load_addresses($mode_par, 3);

                    $pos_res = $pos_op[2];
                    $op[0]   = $this->loadFromMemory($pos_op[0]);
                    $op[1]   = $this->loadFromMemory($pos_op[1]);

                    $this->storeInMemory($pos_res, ($op[0] < $op[1]) ? 1 : 0);
                    break;

                case 8: # equals
                    $pos_op = $this->load_addresses($mode_par, 3);

                    $pos_res = $this->memory[$this->instruction_pointer + 3];
                    $pos_res = $pos_op[2];
                    $op[0]   = $this->loadFromMemory($pos_op[0]);
                    $op[1]   = $this->loadFromMemory($pos_op[1]);

                    $this->storeInMemory($pos_res, ($op[0] == $op[1]) ? 1 : 0);
                    break;

                case 9: # change relative base
                    $pos_op  = $this->load_addresses($mode_par, 1);
                    $op[0]   = $this->loadFromMemory($pos_op[0]);
                    $this->relative_base += $op[0];
                    break;
                default:
                    throw new Exception("Unknown instruction: ".$instruction);
            }
            $this->instruction_pointer += $this->opcode_length[$instruction];
        }
        return "WHAAAT!\n";
    }

    public static function leading_spaces($number)
    {
        return str_pad($number, 8, ' ', STR_PAD_LEFT);
    }

    public function dump_memory()
    {
        $beau = array_map('static::leading_spaces', $this->memory);
        $return = "#########################################\n";
        $show_colums = 4;
        $address_counter = 0;
        while ($beau) {
            $return .= static::leading_spaces($address_counter) . "\t";
            $address_counter += $show_colums;
            $temp = [];
            for ($i = 0; $i<$show_colums; $i++) {
                $temp[$i] = array_shift($beau);
            }
            $return .= implode(',', $temp) ."\n";
        }
        return $return;
    }

    private function load_addresses($modes, $num_addresses)
    {
        $return = [];
        for ($i = 0; $i<$num_addresses; $i++) {
            switch ($modes[$i]) {
                case 1:
                    $return[$i] = $this->instruction_pointer + $i + 1;
                    break;
                case 2:
                    $return[$i] = $this->relative_base + $this->memory[$this->instruction_pointer + $i + 1];
                    break;
                case 0:
                    $return[$i] = $this->memory[$this->instruction_pointer + $i + 1];
                    break;
                default:
                    throw new Exception("Invalid Memory Access Mode");
            }
        }
        return $return;
    }
}

class ExitException extends Exception
{
}
