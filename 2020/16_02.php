<?php

$input = file_get_contents('16_input.txt');

$ts = new \TicketScanner($input);

$ts->validateTickets();

$ts->determinePossibleColumns();

$ts->determineColumns();

$ts->myTicketValue();

Class TicketScanner {
	private $ranges = [];
	private $my_ticket = [];
	private $nearby_tickets = [];
	private $valid_nearby_tickets = [];
	private $found_ranges = [];
	
	
	private $possible_column_names = [];
	
	private $invalid_numbers = [];

	private const SECTION_RANGES = 0;
	private const SECTION_TICKET = 1;
	private const SECTION_OTHER_TICKETS = 2;

	public function __construct($input) {
		$this->scanTicket($input);
	}
	
	public function myTicketValue() {
		$temp = 1;
		foreach($this->column_names as $key => $name) {
			if(substr($name, 0, 10) != 'departure ') {
				continue;
			}
			
			$temp *= $this->my_ticket[$key];
		}
		var_dump($temp);
	}

	public function determineColumns() {
		uasort($this->possible_column_names, 'static::sortByCount');
		array_map('sort', $this->possible_column_names);
		#var_dump($this->possible_column_names);
		
		$column_names = [];
		foreach($this->possible_column_names as $column => $possible_rows) {
			if(count($possible_rows) == 1) {
				$column_names[$column] = $possible_rows[0];
				continue;
			}
			$difference = array_diff($possible_rows, $column_names);
			/*if(!isset($difference[0])) {
				var_dump($possible_rows, $column_names, array_diff($possible_rows, $column_names));die();
			}*/
			$column_names[$column] = array_shift($difference);
		}
		$this->column_names = $column_names;
	}
	
	private static function sortByCount($a, $b) {
		return count($a) - count($b);
	}

	public function determinePossibleColumns() {
		$columns = count($this->my_ticket);
		#echo "columns: ".$columns."\n";
	
		for($column = 0; $column < $columns; $column += 1) {
			#echo ".".$column;
			foreach($this->ranges as $range_id => $range) {
				if(in_array($range_id, $this->found_ranges)) {
					continue;
				}
				#echo "#".$range->name."\n";
			
				foreach($this->valid_nearby_tickets as $ticket) {
					if(!$range->contains($ticket[$column])) {
						//next column
						continue 2;
					}
				}
				
				#echo "found\n";
				//if you got here it means all numbers are in range
				$this->possible_column_names[$column][] = $range->name;
			}
		}
		ksort($this->possible_column_names);
	}
	
	public function validateTickets() {
		$v = $this->validateTicket($this->my_ticket);
		foreach($this->nearby_tickets as $ticket) {
			$valid = $this->validateTicket($ticket);
			if($valid) {
				$this->valid_nearby_tickets[] = $ticket;
			}
		}
		#var_dump($this->invalid_numbers);
		var_dump(array_sum($this->invalid_numbers));
	}
	
	private function validateTicket($ticket) {
		$valid = true;
		foreach($ticket as $number) {
			$found = false;
			foreach($this->ranges as $range) {
				#echo $range . ":" . $number . ":" . ($range->contains($number) ? 'yes':'no') . "\n";
				if($range->contains($number)) {
					$found = true;
					continue;
				}
			}
			
			if($found == false) {
				$this->invalid_numbers[] = $number;
				$valid = false;
			}
		}
		if($valid == false) {
			return false;
		}
		return true;
	}
	
	public function scanTicket($input) {
		$sections = explode("\n\n", $input);
	
		$this->ranges = $this->readRanges($sections[static::SECTION_RANGES]);
		$this->my_ticket = $this->readTicket($sections[static::SECTION_TICKET]);
		$this->nearby_tickets = $this->readNearbyTickets($sections[static::SECTION_OTHER_TICKETS]);
	}
	
	public function readRanges($ranges_string) {
		$lines = explode("\n", $ranges_string);
		$ranges = [];
		foreach($lines as $line) {
			$match = preg_match('#^(.*)\: ([0-9]+-[0-9]+) or ([0-9]+-[0-9]+)$#',$line, $matches);
			if($match) {
				$ranges[] = new \myRange($matches[1], $matches[2], $matches[3]);
			}
		}
		return $ranges;
	}

	public function readTicket($ticket_string) {
		$lines = explode("\n", $ticket_string);
		if($lines[0] != 'your ticket:') {
			throw new Exception('This is not a ticket section!');
		}
		return static::explode_by_comma( $lines[1]);
	}
	
	public function readNearbyTickets($nearby_ticket_string) {
		$lines = explode("\n", $nearby_ticket_string);
		$lines = array_filter($lines);
		$first_line = array_shift($lines);
		if($first_line != 'nearby tickets:') {
			throw new Exception('This is not a nearby ticket section!');
		}

		$tickets = array_map('static::explode_by_comma', $lines);
		return $tickets;
		
	}
	
	private static function explode_by_comma($string) {
		return explode(',' , $string);
	}

	private static function combine($string) {
		return implode(',' , $string);
	}
}

Class myRange {
	private $lower1, $lower2, $upper1, $upper2;
	private $name;
	
	public function __get($key) {
		if($key == 'name') {
			return $this->name;
		}
		throw new Exception("Access denied for ".$key);
	}
	public function __construct($name, $range_string1, $range_string2 ) {
		$this->name = $name;
		list($this->lower1, $this->upper1) = explode('-', $range_string1);
		list($this->lower2, $this->upper2) = explode('-', $range_string2);
	}
	
	public function contains($number) {
		$in_range1 = ($number >= $this->lower1) && ($number <= $this->upper1);
		$in_range2 = ($number >= $this->lower2) && ($number <= $this->upper2);
		return $in_range1 || $in_range2;
	}
	
	public function __toString() {
		return $name.': ' . $this->lower . '-' . $this->upper;
	}
}