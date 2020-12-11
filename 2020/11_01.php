<?php

//empty seat = L
//occupied seat = #
//floor = .

$input = 
'L.LL.LL.LL
LLLLLLL.LL
L.L.L..L..
LLLL.LL.LL
L.LL.LL.LL
L.LLLLL.LL
..L.L.....
LLLLLLLLLL
L.LLLLLL.L
L.LLLLL.LL'
;

$input 	 = file_get_contents('11_input.txt');

$airport = new \Airport($input);
$identical = false;
$counter = 0;
#die();
while(!$identical) {
	
	$identical = $airport->iterate();
	var_dump($identical);
	#$airport->draw_seats();
	$counter += 1;
}
var_dump($counter);

$seats = $airport->count_occupied_seats();
var_dump($seats);




Class Airport {
	private $seats;
	public function __construct($seat_layout) {
		$rows	= explode("\n", $seat_layout);
		//pad to avoid ifs on edges
		$rows[-1] = $rows[count($rows)] = str_repeat('.', strlen($rows[0]));
		ksort($rows);
		$this->seats  = array_map('static::explode_seats', $rows);
		
		$this->draw_seats();
	}

	private static function explode_seats($row) {
		$seats = str_split($row);
		//pad to avoid ifs on edges
		$seats[-1] = $seats[count($seats)] = '.';
		ksort($seats);
		return $seats; 
	}

	public function iterate() : bool
	{
		$new = $this->seats;
		for($i_r = 0; $i_r < count($this->seats) - 2; $i_r += 1) {
			for($i_s = 0; $i_s < count($this->seats[$i_r]) - 2; $i_s += 1) {
				if($this->is_floor($i_r, $i_s)) {
					continue;
				}
				$occupied_surroundings = $this->count_occupied_surrounding($i_r, $i_s);
				if($this->is_empty($i_r, $i_s) && ($occupied_surroundings == 0)) {
					$new[$i_r][$i_s] = '#';
				}

				if($this->is_occupied($i_r, $i_s) && ($occupied_surroundings >= 4)) {
					$new[$i_r][$i_s] = 'L';
				}
			}
		}
		$identical = $this->compare_seats($this->seats, $new);
		$this->seats = $new;
		
		return $identical;
	}

	public function compare_seats($one, $two): bool
	{
		for($i_r = 0; $i_r < count($one) - 2; $i_r += 1) {
			for($i_s = 0; $i_s < count($one[$i_r]) - 2; $i_s += 1) {
				if($one[$i_r][$i_s] != $two[$i_r][$i_s]) {
					return false;
				}
			}
		}
		return true;
	}

	public function count_occupied_seats() : int
	{
		$counter = 0;
		for($i_r = 0; $i_r < count($this->seats) - 2; $i_r += 1) {
			for($i_s = 0; $i_s < count($this->seats[$i_r]) - 2; $i_s += 1) {
				if($this->is_occupied($i_r, $i_s)) {
					$counter += 1;
				}
			}
		}
		return $counter;
	}

	public function count_occupied_surrounding(int $row, int $seat): int {
		$surroundings = [];
		for($i = $row-1; $i <= ($row + 1); $i += 1) {
			for($j = $seat-1; $j <= ($seat + 1); $j += 1) {
				$surroundings[] = $this->seats[$i][$j];
			}
		}
		//we don't want to count the actual seat itself
		unset($surroundings[4]);
		$foo = array_count_values($surroundings);
		if(!isset($foo['#'])) {
			return 0;
		}
		return $foo['#'];
	}

	public function draw_seats($seats = null) :void {
		if($seats == null) {
			$seats = $this->seats;
		}
		for($i_r = 0; $i_r < count($seats) - 2; $i_r += 1) {
			for($i_s = 0; $i_s < count($seats[$i_r]) - 2; $i_s += 1) {
				echo $seats[$i_r][$i_s];
			}
			echo "\n";
		}
		echo "==================\n";
	}

	public function empty_seat($row, $seat) :bool {
		return $this->seats[$row][$seat] = 'L';
	}

	public function occupy_seat($row, $seat):bool {
		return $this->seats[$row][$seat] = '#';
	}

	public function is_floor($row, $seat) :bool{
		return $this->seats[$row][$seat] == '.';
	}

	public function is_occupied($row, $seat) :bool{
		return $this->seats[$row][$seat] == '#';
	}

	public function is_empty($row, $seat) :bool{
		return $this->seats[$row][$seat] == 'L';
	}
}

/*

draw_seats($seats);


echo "######################\n";

draw_seats($seats);





*/
