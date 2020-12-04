<?php

$input 	= file_get_contents('04_input.txt');
$ids   	= explode("\n\n", $input);
$ids 	= array_map('trim', $ids);
$ids 	= array_map('clean_up_id', $ids);

$valid_ids = array_filter($ids, 'validate_ids_complex');

echo "Number of valid IDs: ".count($valid_ids)."\n";
exit;




function clean_up_id($id_string) {
	$id_string = str_replace(' ', "\n", $id_string);
	$id_fields = explode("\n", $id_string);
	$id_fields = parse_fields($id_fields);
	return $id_fields;
}

function parse_fields($id_fields) {
	$new = [];
	foreach($id_fields as $field) {
		$foo = explode(':', $field);
		$foo = array_map('trim', $foo);
		$new[$foo[0]] = $foo[1];
	}
	$new = check_optional_cid($new);
	return $new;
}

function check_optional_cid($new) {
	if(isset($new['cid'])) {
		return $new;
	}
	//automatically add CID here to avoid further if branches. :D
	$new['cid'] = "0";
	return $new;
}

//this is the simple validation for part 1
function validate_ids_simple($id) {
	return 8 == count($id);
}

function validate_ids_complex($id) {
	//if it can't survive the simple validation don't even try the complex one
	if(!validate_ids_simple($id)) {
		return false;
	}
	$valid = [];
	$valid['cid'] = true;
	$valid['byr'] = validate_birth_year($id['byr']);
	$valid['iyr'] = validate_issue_year($id['iyr']);
	$valid['eyr'] = validate_expiration_year($id['eyr']);
	$valid['hgt'] = validate_height($id['hgt']);
	$valid['hcl'] = validate_hair_color($id['hcl']);
	$valid['ecl'] = validate_eye_color($id['ecl']);
	$valid['pid'] = validate_passport_id($id['pid']);
	
	//remove all false from array if 8 values remain everything's okay
	$filtered = array_filter($valid);
	if(count($filtered) == 8) {
		return true;
	}
	return false;
	
}

function validate_birth_year($year) {
	#byr (Birth Year) - four digits; at least 1920 and at most 2002.
	if($year < 1920 || $year > 2002) {
		return false;
	}
	return true;
}

function validate_issue_year($year) {
	#iyr (Issue Year) - four digits; at least 2010 and at most 2020.
	if($year < 2010 || $year > 2020) {
		return false;
	}
	return true;
}

function validate_expiration_year($year) {
	#eyr (Expiration Year) - four digits; at least 2020 and at most 2030.
	if($year < 2020 || $year > 2030) {
		return false;
	}
	return true;
}

function validate_height($height) {
	#hgt (Height) - a number followed by either cm or in:
	#	If cm, the number must be at least 150 and at most 193.
	#	If in, the number must be at least 59 and at most 76.

	$unit = substr($height, -2);
	$value = substr($height, 0, -2);

	switch($unit) {
		case 'cm':
			$min = 150;
			$max = 193;
			break;
		case 'in':
			$min = 59;
			$max = 76;
			break;
		default:
			return false;
	}
	if($value < $min || $value > $max) {
		return false;
	}
	return true;
}

function validate_hair_color($hair_color) {
	#hcl (Hair Color) - a # followed by exactly six characters 0-9 or a-f.

	$match = preg_match('/^#[0-9a-f]{6}$/', $hair_color);
	return ($match == 1); # convert to bool
	// use complex match only if requirements for color detection change
	/*if(strlen($hair_color) != 7) {
		return false;
	}
	if(substr($hair_color, 0, 1) != '#') {
		return false;
	}*/
}
function validate_eye_color($eye_color) {
	#ecl (Eye Color) - exactly one of: amb blu brn gry grn hzl oth.
	$legal_eye_colors = explode(' ', 'amb blu brn gry grn hzl oth');
	return in_array($eye_color, $legal_eye_colors);
}

function validate_passport_id($passport_id) {
	#pid (Passport ID) - a nine-digit number, including leading zeroes.
	$match = preg_match('/^[0-9]{9}$/', $passport_id);
	return ($match == 1); # convert to bool
}