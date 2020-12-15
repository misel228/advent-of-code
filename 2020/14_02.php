<?php

$tests = [
	"mask = 000000000000000000000000000000X1001X
mem[42] = 100
mask = 00000000000000000000000000000000X0XX
mem[26] = 1",
 file_get_contents('14_input.txt'),
];


$input = $tests[1];
$input = explode("\n", $input);
$input = array_map('trim', $input);
$input = array_filter($input);


$docker = new \Docker2();

foreach($input as $instruction) {
	$docker->run($instruction);
}
var_dump($docker->getMemorySum());



class Docker2 {
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

	private function setMemory($address, $value) {
		
		$address = $this->convertBin($address, static::MEMORY_WIDTH);
		$address_mask = $this->applyMask($address);
		
		$this->store($address_mask, $value);
	}

	private function store($address_mask, $value) {
		$count = $this->countX($address_mask);
		$interations = pow(2, $count);
		for($i = 0; $i < $interations; $i+=1) {
			$address = $this->calculateAddress($address_mask,$i);
			$this->memory[$address] = $value;
		}
	}

	private function calculateAddress($address_mask, $value) {

		$address = str_split($address_mask);
		$mask_a = array_filter($address, 'static::is_X');
		$mask_a_keys = array_keys($mask_a);

		$width = count($mask_a_keys);
		$bin_value = $this->convertBin($value, $width);
		$bin_value_a = str_split($bin_value);
		
		for($i = 0; $i < $width; $i += 1) {
			$address[$mask_a_keys[$i]] = $bin_value_a[$i];
		}
		$address_bin = implode('',$address);
		
		return bindec($address_bin);
	}

	private static function is_X($item) {
		return $item === 'X';
	}

	private function convertBin($value, $width) {
		$bin = decbin($value);
		
		//left pad expand string to width with zeros
		$bin = str_repeat("0", $width).$bin;
		$bin = substr($bin, -1 * $width);
		return $bin;
	}
	
	
	
	private function applyMask($bin_value) {
		$bin_value_a 	= str_split($bin_value);
		$mask_a 		= str_split($this->mask);
		
		foreach($mask_a as $position => $value) {
			if($value == '0') {
				continue;
			}
			$bin_value_a[$position] = $value;
		}
	
		return implode("",$bin_value_a);
		
	}
	
	private function countX($mask)
	{
		$mask_a = str_split($this->mask);
		$foo = array_count_values($mask_a);
		if(!isset($foo['X'])) {
			return 0;
		}
		return $foo['X'];
	}

	public function getMemorySum() {
		return array_sum($this->memory);
	}
}
