<?php

$input = file_get_contents('16_input.txt');

$ts = new \TicketScanner($input);

$ts->validateTickets();


Class TicketScanner {
	private $ranges = [];
	private $my_ticket = [];
	private $nearby_tickets = [];
	private $valid_nearby_tickets = [];
	
	private $invalid_numbers = [];

	private const SECTION_RANGES = 0;
	private const SECTION_TICKET = 1;
	private const SECTION_OTHER_TICKETS = 2;

	public function __construct($input) {
		$this->scanTicket($input);
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
		$this->ticket = $this->readTicket($sections[static::SECTION_TICKET]);
		$this->nearby_tickets = $this->readNearbyTickets($sections[static::SECTION_OTHER_TICKETS]);
	}
	
	public function readRanges($ranges_string) {
		$lines = explode("\n", $ranges_string);
		$ranges = [];
		foreach($lines as $line) {
			$match = preg_match('#([0-9]+-[0-9]+) or ([0-9]+-[0-9]+)$#',$line, $matches);
			if($match) {
				$ranges[] = new \myRange($matches[1]);
				$ranges[] = new \myRange($matches[2]);
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
}

Class myRange {
	private $min, $max;
	public function __construct($range_string) {
		list($this->lower, $this->upper) = explode('-', $range_string);
	}
	
	public function contains($number) {
		return ($number >= $this->lower) && ($number <= $this->upper);
	}
	
	public function __toString() {
		return $this->lower . '-' . $this->upper;
	}
}