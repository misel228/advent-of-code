<?php

$calculations = [
	[
		'input' 	=> '1 + 2 * 3 + 4 * 5 + 6',
		'result'	=> 231,
	],
	[
		'input' 	=> '1 + (2 * 3) + (4 * (5 + 6))',
		'result'	=> 51,
	],
	[
		'input' 	=> '2 * 3 + (4 * 5)',
		'result'	=> 46,
	],
	[
		'input' 	=> '5 + (8 * 3 + 9 + 3 * 4 * 3)',
		'result'	=> 1445,
	],
	[
		'input' 	=> '5 * 9 * (7 * 3 * 3 + 9 * 3 + (8 + 6 * 4))',
		'result'	=> 669060,
	],
	[
		'input' 	=> '((2 + 4 * 9) * (6 + 9 * 8 + 6) + 6) + 2 + 4 * 2',
		'result'	=> 23340,
	],
];


$calculator = new \Calculator();

foreach($calculations as $calc) {
	$result = $calculator->compute($calc['input']);
	if($result != $calc['result']) {
		die("NOPE\n");
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
		echo "==========================\n";
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
		
		// first find all additions and solve those
		// replace each addition with their result
		while(in_array('+', $code) !== false) {
			$plus_sign = array_search('+', $code);
			$result = $code[$plus_sign - 1] + $code[$plus_sign + 1];
			
			$new_code = array_slice($code, 0, $plus_sign - 1);
			$new_code[] = $result;
			
			$second_part = array_slice($code, $plus_sign + 2 );
			
			$new_code = array_merge($new_code, $second_part);
			$code = $new_code;
		}

		//now do all multiplications
		$numbers = array_filter($code, 'is_numeric');
		$result = array_product($numbers);
		return $result;
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
