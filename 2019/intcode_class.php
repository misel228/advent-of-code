<?php

class IntCode {
	
	private $opcode_length = [
		1 => 4, # add
		2 => 4, # multiply
		3 => 2, # input
		4 => 2, # output
		5 => 0, # jump if true  - instruction pointer is set directly
		6 => 0, # jump if false - instruction pointer is set directly
		7 => 4, # less than
		8 => 4, # equals
	];

	private $instruction_pointer = 0;
	
	private $memory = [];

	private $debug = false;
	private $debug2 = false;

	public function __construct($memory, $id = 'intcode') {
		$this->setMemory($memory);
		$this->id = $id;
	}
	
	public function setMemory($memory) {
		$input_code = preg_replace('/\s/','',$memory);
		$input_code = explode(',',$input_code);
		$check = array_filter($input_code, function($item) { return !is_numeric($item);} );

		if(!empty($check)) {
			throw new Exception("non code elements in input found!");
		}
		$this->memory = $input_code;
	}

	public function run_code($input_params = []) {

		$profiler = array_combine(array_keys($this->opcode_length), array_fill(0, count($this->opcode_length), 0));

		while($this->instruction_pointer < count($this->memory)) {
			$instruction = $this->memory[$this->instruction_pointer];
			if($this->debug) echo $this->id." ".$instruction."\n";
			if($instruction == 99) {
				throw new ExitException("end of program for ".$this->id);
				
			}

			$mode_par = [0,0,0];

			//check for immediate mode
			if($instruction > 100) {
				$temp        = intval($instruction / 100);
				$instruction = intval($instruction % 100);

				$mode_par[0] = intval($temp % 10);

				$temp        = intval($temp / 10);
				$mode_par[1] = intval($temp % 10);

				$temp        = intval($temp / 10);
				$mode_par[2] = intval($temp % 10);
			}

			$profiler[$instruction] += 1;
			if($this->debug) echo $this->id." ".$this->instruction_pointer.":".$instruction.' mode: '.implode('', $mode_par)." len: ".$this->opcode_length[$instruction]."\n";

			if($this->debug2) readline('continue');
			$pos_op = [];
			$op = [];
			switch($instruction) {
				case 1: #add
					$pos_op = $this-> load_addresses($mode_par, 2);

					$pos_res = $this->memory[$this->instruction_pointer + 3];
					$op[0]   = $this->memory[$pos_op[0]];
					$op[1]   = $this->memory[$pos_op[1]];

					$this->memory[$pos_res] = $op[0] + $op[1];
					break;
				case 2: #multiply
					$pos_op = $this-> load_addresses($mode_par, 2);

					$pos_res = $this->memory[$this->instruction_pointer + 3];
					$op[0]   = $this->memory[$pos_op[0]];
					$op[1]   = $this->memory[$pos_op[1]];
					$this->memory[$pos_res] = $op[0] * $op[1];
					break;

				case 3: #input
					$pos_op[0] = $this->memory[$this->instruction_pointer + 1];
					if(count($input_params) == 0) {
						if($this->debug) echo "Waiting\n";
						return -1; # waiting for input to continue;
					}
					$temp = array_shift($input_params);
					if($this->debug) {
						echo "read from memory: ".$temp."\n";
					}
					$this->memory[$pos_op[0]] = $temp;
					break;
				case 4: #output
					$pos_op[0] = $this->memory[$this->instruction_pointer + 1];
					if($mode_par[0]) {
						$pos_op[0] = $this->instruction_pointer + 1;
					}
					$op[0] = $this->memory[$pos_op[0]];
					if($this->debug) echo "Output Wait for restart\n";
					$this->instruction_pointer += $this->opcode_length[$instruction];
					return $op[0];
					break;
				case 5: #jump if true
					$pos_op = $this-> load_addresses($mode_par, 2);

					if($this->memory[$pos_op[0]] > 0) {
						$this->instruction_pointer = $this->memory[$pos_op[1]];
						break;
					}
					$this->instruction_pointer += 3;
					break;

				case 6: #jump if false
					$pos_op = $this-> load_addresses($mode_par, 2);

					if($this->memory[$pos_op[0]] == 0) {
						$this->instruction_pointer = $this->memory[$pos_op[1]];
						break;
					}
					$this->instruction_pointer += 3;
					break;

				case 7: #less than
					$pos_op = $this-> load_addresses($mode_par, 2);

					$pos_res = $this->memory[$this->instruction_pointer + 3];
					$op[0]   = $this->memory[$pos_op[0]];
					$op[1]   = $this->memory[$pos_op[1]];

					$this->memory[$pos_res] = ($this->memory[$pos_op[0]] < $this->memory[$pos_op[1]]) ? 1 : 0;
				break;

				case 8: # equals
					$pos_op = $this-> load_addresses($mode_par, 2);

					$pos_res = $this->memory[$this->instruction_pointer + 3];
					$op[0]   = $this->memory[$pos_op[0]];
					$op[1]   = $this->memory[$pos_op[1]];

					$this->memory[$pos_res] = ($this->memory[$pos_op[0]] == $this->memory[$pos_op[1]]) ? 1 : 0;
					break;

				default:
					echo "Unknown instruction: ".$instruction."\n";
					echo "something went wrong!\n";
					die();


			}
			$this->instruction_pointer += $this->opcode_length[$instruction];
		}
		return "WHAAAT!\n";
	}

	public static function leading_spaces($number) {
		return str_pad($number, 8, ' ', STR_PAD_LEFT);
	}

	public function dump_memory() {
		$beau = array_map('static::leading_spaces', $this->memory);
		$return = '';
		while($beau) {
			$temp = [];
			for($i = 0; $i<4; $i++) {
				$temp[$i] = array_shift($beau);
			}
			$return .= implode(',',$temp) ."\n";
		}
		return $return;
	}

	private function load_addresses($modes, $num_addresses) {
		$return = [];
		for($i = 0; $i<$num_addresses; $i++) {
			$return[$i] = @$this->memory[$this->instruction_pointer + $i + 1];
			if($modes[$i]) {
				$return[$i] = $this->instruction_pointer + $i + 1;
			}
		}
		return $return;

	}
}

class ExitException extends Exception {};