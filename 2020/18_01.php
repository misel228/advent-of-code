<?php

#$input = file_get_contents('18_input.txt');

$calculations = [
	[
		'input' 	=> '1 + 2 * 3 + 4 * 5 + 6',
		'result'	=> 71,
	],
	[
		'input' 	=> '1 + (2 * 3) + (4 * (5 + 6))',
		'result'	=> 51,
	],
	[
		'input' 	=> '2 * 3 + (4 * 5)',
		'result'	=> 26,
	],
	[
		'input' 	=> '5 + (8 * 3 + 9 + 3 * 4 * 3)',
		'result'	=> 437,
	],
	[
		'input' 	=> '5 * 9 * (7 * 3 * 3 + 9 * 3 + (8 + 6 * 4))',
		'result'	=> 12240,
	],
	[
		'input' 	=> '((2 + 4 * 9) * (6 + 9 * 8 + 6) + 6) + 2 + 4 * 2',
		'result'	=> 13632,
	],
];


$calculator = new \Calculator();
#$result = $calculator->compute($calculations[1]['input']);
#die();

foreach($calculations as $calc) {
	$result = $calculator->compute($calc['input']);
	if($result != $calc['result']) {
		die("nope");
	}
}


$equations = file('18_input.txt');
$equations = array_map('trim', $equations);
$equations = array_filter($equations);

$calculator = new \Calculator();
$sum = 0;
foreach($equations as $equation) {
	$sum += $calculator->compute($equation);
}

var_dump($sum);
die("END OF PROGRAM\n");


class Calculator {
	
	private $pointer = 0;
	
	public function compute($input) {
		// remove parenthesis iteratively
		
		var_dump($input);
		while(strpos($input, ')')!== false) {
			$input = $this->remove_parenthesis($input);
			var_dump($input);
		}
		
		$result = $this->solve($input);
		var_dump($result);
		return $result;
		
	}
	
	private function remove_parenthesis($parenthesised_code) {
		$first_closed_parenthesis = strpos($parenthesised_code, ')');
		$code = substr($parenthesised_code, 0 , $first_closed_parenthesis);
		$corresponding_opening = strrpos($code, '(');
		$code = substr($code, $corresponding_opening + 1);
		$result = $this->solve($code);
		
		$new_code = substr($parenthesised_code , 0, $corresponding_opening);
		$new_code .= $result;
		$new_code .= substr($parenthesised_code , $first_closed_parenthesis + 1);
		
		return $new_code;
	}
	
	private function solve($unparenthesised_code) {
		$code = $this->parse($unparenthesised_code);
		$accumulator = $code[0];
		$pointer = 1;
		
		$foo = 0;
		do {
			$operation   = $code[$pointer];
			$operand     = $code[$pointer + 1];

		
			#echo $accumulator . ' ' . $operation . ' ' . $operand. "\n";
			
			switch($operation) {
				case '+':
					$accumulator += $operand;
					break;
				case '*':
					$accumulator *= $operand;
					break;
			}
			
			$pointer += 2;
			
		} while(isset($code[$pointer]) && ($foo++ < 500));
		
		return $accumulator;
	}
	
	private function parse($input) {
		$input = $this->correctParenthesis($input);
		$code = explode(' ', $input);
		$code = array_filter($code);
		return $code;
	}

	private function correctParenthesis($input) {
		return str_replace(['(',')'],['( ',' )'], $input);
	}
}
