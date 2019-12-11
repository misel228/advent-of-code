<?php

$test_maps = [
    /*[
        'map' => ".#..#\n.....\n#####\n....#\n...##",
        'best_coordinates' => [3,7],
        'visible_asteroids' => 7,
    ],
    [
        'map' => "......#.#.\n#..#.#....\n..#######.\n.#.#.###..\n.#..#.....\n..#....#.#\n#..#....#.\n.##.#..###\n##...#..#.\n.#....####",
        'best_coordinates' => [5,8],
        'visible_asteroids' => 33,
    ],
    [
        'map' => "#.#...#.#.\n.###....#.\n.#....#...\n##.#.#.#.#\n....#.#.#.\n.##..###.#\n..#...##..\n..##....##\n......#...\n.####.###.",
        'best_coordinates' => [1,2],
        'visible_asteroids' => 35,
    ],
    [
        'map' => ".#..#..###\n####.###.#\n....###.#.\n..###.##.#\n##.##.#.#.\n....###..#\n..#.#..#.#\n#..#.#.###\n.##...##.#\n.....#.#..",
        'best_coordinates' => [6,3],
        'visible_asteroids' => 41,
    ],
    [
        'map' => ".#..##.###...#######\n##.############..##.\n.#.######.########.#\n.###.#######.####.#.\n#####.##.#.##.###.##\n..#####..#.#########\n####################\n#.####....###.#.#.##\n##.#################\n#####.##.###..####..\n..######..##.#######\n####.##.####...##..#\n.#####..#.######.###\n##...#.##########...\n#.##########.#######\n.####.#.###.###.#.##\n....##.##.###..#####\n.#.#.###########.###\n#.#.#.#####.####.###\n###.##.####.##.#..##",
        'best_coordinates' => [11,13],
        'visible_asteroids' => 210,
    ],#*/
    /*
    [
        'map' => "......................\n......................\n......................\n......................\n......................\n......................\n......................\n...............#......\n..............#.......\n......................\n......................\n...........X..........\n......................\n......................\n......................\n......................\n......................\n......................\n......................\n......................\n......................\n......................",
    ],#*/
    [
        'map' => "###..#########.#####.\n.####.#####..####.#.#\n.###.#.#.#####.##..##\n##.####.#.###########\n###...#.####.#.#.####\n#.##..###.########...\n#.#######.##.#######.\n.#..#.#..###...####.#\n#######.##.##.###..##\n#.#......#....#.#.#..\n######.###.#.#.##...#\n####.#...#.#######.#.\n.######.#####.#######\n##.##.##.#####.##.#.#\n###.#######..##.#....\n###.##.##..##.#####.#\n##.########.#.#.#####\n.##....##..###.#...#.\n#..#.####.######..###\n..#.####.############\n..##...###..#########",
        'best_coordinates' => [11,11],
        'visible_asteroids' => 221,
    ],
];




foreach($test_maps as $test) {
    #list($best_coordinates, $seen_asteroids) = $map->calculateBestCoordinates();
    #var_dump($best_coordinates, $seen_asteroids);

    $laser_coordinates = [11,11];
    $map = new Map($test['map'],$laser_coordinates);
    #map::$debug = true;

    $list = $map->determineDestructionOrder($laser_coordinates);
    $map->dumpListForExcel($list);
    die();
    #var_dump($list);die();
    $element_nr = 199;
    var_dump($list[$element_nr]);
    echo $list[$element_nr]['x'] * 100 + $list[$element_nr]['y'] . "\n";
}

class Map {
    public  static  $debug = false;
    private $map_string;
    private $map;
    private $map_x = 0;
    private $map_y = 0;
    private $asteroid_coordinates = [];

    public function __construct($map_string, $exclude_point = false) {
        $this->map_string = $map_string;
        $this->map = $this->parseMapString($exclude_point);
    }

    public function dumpListForExcel($list) {
        foreach($list as $row) {
            foreach($row as $x => $column) {
                if(is_float($column)) {
                    $column = number_format($column, 4, ',', '');
                }
                echo $column."\t";
            }
            echo "\r\n";
        }
    }

    public function dumpMapForExcel() {
        foreach($this->map as $y => $row) {
            foreach($row as $x => $column) {
                echo $column."\t";
            }
            echo "\r\n";
        }
    }

    private function parseMapString($exclude_point = false) {
        $rows = explode("\n", $this->map_string);
        $rows = array_map("str_split",$rows);
        $this->map = $rows;
        $this->map_y = count($rows);
        $this->map_x = count($rows[0]);

        foreach($this->map as $y => $row) {
            foreach($row as $x => $column) {
                if($column == '#') {
                    if($exclude_point) {
                        if(($exclude_point[0] == $x) && ($exclude_point[1]==$y)) {
                            continue;
                        }
                    }
                    $this->asteroid_coordinates[] = ['x' => $x, 'y' => $y];
                }
            }
        }
        #var_dump($this->asteroid_coordinates);die();
        $this->dumpMapForExcel();
    }

    public function determineDestructionOrder($laser_coordinates) {
        $asteroid_details = array_map('static::calculateDetails', $this->asteroid_coordinates);
        usort($asteroid_details, 'static::orderAsteroid');
        $asteroid_details = static::correctDuplicateAngles($asteroid_details);
        usort($asteroid_details, 'static::orderAsteroid');

        return $asteroid_details;

    }

    public static function calculateDetails($asteroid) {
        $origin = ['x' => 11,'y' => 11];

        $return = $asteroid;
        $return['distance'] = static::calculateDistance($origin, $asteroid);
        $return['angle'] = static::calculateAngle($origin, $asteroid, $return['distance']);
        return $return;
    }

    public static function angleEqual($angle1, $angle2) {
        //if the difference of angles is significantly low, treat them as equal
        return (abs($angle1 - $angle2) < 0.01);
    }

    public static function orderAsteroid($asteroid1, $asteroid2) {

        if(static::angleEqual($asteroid1['angle'], $asteroid2['angle'])) {
            //distance
            if($asteroid1['distance'] < $asteroid2['distance']) {
                return -1;
            }

            if($asteroid1['distance'] > $asteroid2['distance']) {
                return 1;
            }
        }

        //angle
        if($asteroid1['angle'] < $asteroid2['angle']) {
            return -1;
        }

        if($asteroid1['angle'] > $asteroid2['angle']) {
            return 1;
        }

        // $asteroid1['angle'] == $asteroid2['angle']


        echo "###########\n";
        var_dump($asteroid1, $asteroid2);die();
        throw new Exception("WHAAT?! You should never get here");
    }

    public static function correctDuplicateAngles($asteroids){
        //add 360° to each duplicate angle
        $current_angle = $asteroids[0]['angle'];
        $counter = 1;
        for($i = 1; $i < count($asteroids); $i++) {
            if(!static::angleEqual($current_angle, $asteroids[$i]['angle'])) {
            #if($current_angle != $asteroids[$i]['angle']) {
                $current_angle = $asteroids[$i]['angle'];
                $counter = 1;
                continue;
            }
            #var_dump($asteroids[$i-1]);
            #var_dump($asteroids[$i]);

            //add 360°
            #$asteroids[$i]['angle'] += (2 * pi()) * $counter;
            $asteroids[$i]['angle'] += 360 * $counter;
            $counter += 1;

            #var_dump($asteroids[$i]);
        }
        return $asteroids;
    }

    public function calculateBestCoordinates() {
        #assume everyone can see each other
        $a_can_see_b = array_fill(0, count($this->asteroid_coordinates), array_keys($this->asteroid_coordinates));
        #var_dump($a_can_see_b);die();
        #then check if there are asteroids in between

        foreach($a_can_see_b as $ka => $asteroids_to_check) {

            $a  = $this->asteroid_coordinates[$ka];
            if($this->debug) echo "check a ".$a['x'].':'.$a['y']."\n";
            $kb = 0;
            foreach($asteroids_to_check as $kb) {
                $b  = $this->asteroid_coordinates[$kb];
                if($this->debug) echo "b ".$b['x'].':'.$b['y']."\n";

                if(static::areIdentical($a,$b)) {
                    if($this->debug) echo "identical\n";
                    unset($a_can_see_b[$ka][$kb]);
                    continue;
                }

                foreach($this->asteroid_coordinates as $in_between) {
                    if($this->debug) echo "c ".$in_between['x'].':'.$in_between['y']." ";

                    if(static::areIdentical($a,$in_between)) {
                        if($this->debug) echo "identical\n";
                        continue;
                    }

                    if(static::areIdentical($b,$in_between)) {
                        if($this->debug) echo "identical\n";
                        continue;
                    }


                    if(min($a['x'],$b['x'],) > $in_between['x']) {
                        if($this->debug) echo "too left\n";
                        continue;
                    }
                    // too right, ignore
                    if(max($a['x'],$b['x'],) < $in_between['x']) {
                        if($this->debug) echo "too right\n";
                        continue;
                    }
                    // too top, ignore
                    if(min($a['y'],$b['y'],) > $in_between['y']) {
                        if($this->debug) echo "too top\n";
                        continue;
                    }
                    // too bottom, ignore
                    if(max($a['y'],$b['y'],) < $in_between['y']) {
                        if($this->debug) echo "too bottom\n";
                        continue;
                    }

                    // check if in_between is on a straight line between a and b
                    if(!$this->isInline($a,$b,$in_between)) {
                        if($this->debug) echo "not in line\n";
                        continue;
                    }

                    // if you got to here than in_between is in line of site
                    if($this->debug) echo "in line of sight!\n";
                    unset($a_can_see_b[$ka][$kb]);
                    #break;

                }
            }
            #break;
        }
        #die();
        $counts = array_map('count', $a_can_see_b);
        $most_visible = max($counts);
        $index = -1;
        foreach($counts as $key => $count) {
            if($count == $most_visible) {
                $index = $key;
                break;
            }
        }
        $asteroid = $this->asteroid_coordinates[$key];
        return [$asteroid, $most_visible];

    }

    private function isInLine($a,$b,$c) {
        //avoid division by zero
        if($a['x'] != $b['x']) {
            list($slope, $intercept) = $this->calculateStraightParameters($a, $b);
            if($this->debug) echo "m:".$slope . " n:".$intercept."\n";
            $temp = ($slope * $c['x']) + $intercept;
            // do not do an equal comparison because of floats
            if($this->debug) echo abs($temp - $c['y']);
            return (abs($temp - $c['y']) < 0.01);
        }

        //special case straight line up
        return $a['x'] == $c['x'];

    }


    private function calculateStraightParameters($point1, $point2) {
        $slope = ($point1['y'] - $point2['y']) / ($point1['x'] - $point2['x']);
        $intercept = $point1['y'] - ($point1['x'] * $slope);
        return [$slope, $intercept];
    }

    public static function areIdentical($point1, $point2) {
        return ($point1['y'] == $point2['y']) & ($point1['x'] == $point2['x']);
    }

    private static function calculateDistance($point1, $point2) {
        //Pythagoras
        $a2 = ($point1['y'] - $point2['y']) * ($point1['y'] - $point2['y']);
        $b2 = ($point1['x'] - $point2['x']) * ($point1['x'] - $point2['x']);
        return sqrt($a2 + $b2);
    }

    private static function calculateAngle($point1, $point2, $distance) {
        if(static::$debug) echo "point 2: ".$point2['x'].':'.$point2['y']. " distance:".$distance." ";

        //avoid div by zero
        if($point1['x'] == $point2['x']) {
            if($point1['y'] <= $point2['y']) {
                if(static::$debug) echo "angle 0\n";
                return 180;
            }
            #return pi();
            if(static::$debug) echo "angle 180\n";
            return 0;
        }

        /*
        //horizontal
        if($point1['y'] == $point2['y']) {
            if($point1['x'] <= $point2['x']) {
                if(static::$debug) echo "angle 90\n";
                return 90;
            }
            #return pi();
            if(static::$debug) echo "angle 270\n";
            return 270;
        }*/

        $gegen_kathete = $point2['x'] - $point1['x'];
        if(static::$debug) echo " Gegenkathete: $gegen_kathete ";

        /*
        var_dump($gegen_kathete);
        var_dump($distance);
        var_dump((($gegen_kathete / $distance)));
        var_dump((asin($gegen_kathete / $distance)));
        var_dump(abs(asin($gegen_kathete / $distance)));
        var_dump(rad2deg(abs(asin($gegen_kathete / $distance))));
        #die();*/
        $angle_rad = abs(asin($gegen_kathete / $distance));

        if(static::$debug) echo " Rad: $angle_rad ";
        $angle_deg = rad2deg($angle_rad);
        if(static::$debug) echo " Deg: $angle_deg ";



        //right half
        if(($point2['x'] - $point1['x']) > 0) {
            if(($point2['y'] - $point1['y']) > 0) {
                //bottom right
                $angle_deg = 90 + (90 - $angle_deg);
                if(static::$debug) echo "br angle: ".$angle_deg . "\n";
                return $angle_deg;
            }
            //top right
            if(static::$debug) echo "tr angle: ".$angle_deg . "\n";
            return $angle_deg;
        }

        //left half
        if(($point2['x'] - $point1['x']) < 0) {
            if(($point2['y'] - $point1['y']) < 0) {
                //top left
                if(static::$debug) echo "tl angle: ".$angle_deg . "\n";
                $angle_deg = 270 + (90 - $angle_deg);
                if(static::$debug) echo "tl angle: ".$angle_deg . "\n";
                return $angle_deg;
            }
            //bottom left

            if(static::$debug) echo "bl angle: ".$angle_deg . "\n";
            $angle_deg = 180 + $angle_deg;
            if(static::$debug) echo "bl angle: ".$angle_deg . "\n";
            return $angle_deg;
        }

            /*
        if($point1['y'] < $point2['y']) {
            $angle_rad += pi();
            $angle_deg += 180;
        }*/
        return $angle_deg;
    }
}
