<?php

$input 	 = file('08_input.txt');
$input   = array_map('trim', $input);
$input   = array_filter($input);

//this is a brute force method
//the nops are replaced by a jmp (and vice versa) 
// and each time the code is run again to see if the program terminates properly


$nops    = array_keys(array_filter($input, 'is_nop'));

function is_nop($instruction) {
	return (substr($instruction,0,3) == 'nop');
}

foreach($nops as $nop) {
	$new_code = $input;
	$new_code[$nop] = str_replace('nop', 'jmp', $new_code[$nop]);
	$c = new Console($new_code);
	try {
		$c->run();
		echo "nop: ".$nop."\n";
	} catch(Exception $e) {
		
	}
}


$jumps   = array_keys(array_filter($input, 'is_jmp'));

function is_jmp($instruction) {
	return (substr($instruction,0,3) == 'jmp');
}

foreach($jumps as $jmp) {
	$new_code = $input;
	$new_code[$jmp] = str_replace('jmp', 'nop', $new_code[$jmp]);
	$c = new Console($new_code);
	try {
		$c->run();
		echo "jmp: ".$jmp."\n";
	} catch(Exception $e) {
		
	}
}



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
		die();
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
