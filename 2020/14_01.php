<?php

$tests = [
	"mask = XXXXXXXXXXXXXXXXXXXXXXXXXXXXX1XXXX0X
mem[8] = 11
mem[7] = 101
mem[8] = 0",
 file_get_contents('14_input.txt'),
];


$input = $tests[1];
$input = explode("\n", $input);
$input = array_map('trim', $input);
$input = array_filter($input);


$docker = new \Docker();

foreach($input as $instruction) {
	$docker->run($instruction);
}

var_dump($docker->getMemorySum());



class Docker {
	private $memory = [];
	const MEMORY_WIDTH = 36;
	public function run($instruction) {
		list($operation, $parameters) = $this->decode($instruction);
		switch($operation) {
			case 'mem':
				$this->setMemory($parameters[0], $parameters[1]);
				break;
			case 'mask':
				$this->setMask($parameters[0]);
				break;
			default:
				throw new \Exception('Unknown operation: '.$operation);
		}
	}
	
	private function decode($instruction) {
		var_dump($instruction);
		$temp = explode('=', $instruction);
		$temp = array_map('trim', $temp);
		if($temp[0] == 'mask') {
			return [$temp[0], [$temp[1]],];
		}
		
		$temp2 = explode("[", $temp[0]);
		$temp2[1] = str_replace("]", "", $temp2[1]);
		array_push($temp2, $temp[1]);

		$operation = $temp2[0];
		$parameters = [$temp2[1], $temp[1], ];

		return [$operation,$parameters];
	}
	
	private function setMask($mask) {
		$this->mask = $mask;
	}

	private function setMemory($offset, $value) {
		$bin_value = $this->convertBin($value);
		$new_bin_value = $this->applyMask($bin_value);
		
		$this->memory[$offset] = $new_bin_value;
	}
	
	private function convertBin($value) {
		$bin = decbin($value);
		
		//left pad expand string to memory width with zeros
		$bin = str_repeat("0", static::MEMORY_WIDTH).$bin;
		$bin = substr($bin, -1 * static::MEMORY_WIDTH);
		return $bin;
	}
	
	private function applyMask($bin_value) {
		$bin_value_a 	= str_split($bin_value);
		$mask_a 		= str_split($this->mask);
		
		//ignore all X
		$mask_a = array_filter($mask_a, 'is_numeric');
		foreach($mask_a as $position => $value) {
			$bin_value_a[$position] = $value;
		}
		
		return implode("",$bin_value_a);
		
	}

	public function getMemorySum() {
		$memory_dec = array_map('bindec', $this->memory);
		return array_sum($memory_dec);
	}
}
