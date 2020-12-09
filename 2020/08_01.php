<?php

$input 	 = file('08_input_test.txt');
$input   = array_map('trim', $input);
$input   = array_filter($input);

#var_dump($input);die();


$c = new Console($input);

$c->run();


class Console {
	private $code;
	private $pointer = 0;
	private $accumulator = 0;
	private $executed_instructions = [];
	private $eof;

	public function __construct($code, $pointer = 0) {
		$this->code = $code;
		$this->eof = count($code);
	}
	

	public function run() {
		while($this->pointer < count($this->code)) {
			$this->step();
		}
		echo "END OF PROGRAM\n";
		echo 'accumulator '.$this->accumulator."\n";
		
	}

	private function step() {
		if(in_array($this->pointer, $this->executed_instructions)) {
			throw new Exception("\nalready been there\n"
			.'pointer     '.$this->pointer."\n"
			.'accumulator '.$this->accumulator."\n"
			);
		}
		$this->executed_instructions[] = $this->pointer;
		$instruction = $this->code[$this->pointer];
		list($operation, $parameter) = $this->decode($instruction);
		
		switch($operation) {
			case 'nop':
				$this->pointer += 1;
				break;
			case 'jmp':
				$this->pointer += $parameter;
				break;

			case 'acc':
				$this->pointer += 1;
				$this->accumulator += $parameter;
				break;
		}
		
	}

	private function decode($instruction) {
		$decoded = explode(" ", $instruction);
		$decoded[1] = str_replace('+', '', $decoded[1]);
		return $decoded;
	}
}


