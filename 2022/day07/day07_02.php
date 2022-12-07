<?php

require 'day07_classes.php';

$file_name = 'input.txt';
#$file_name = 'input_test.txt';
$input = fopen($file_name, 'r');

$mode = 'command';

$first_line = str_replace(["\r","\n"], '', fgets($input));

if ($first_line != '$ cd /') {
    throw new Exception('unknown begin');
}


$rootDir = new elfDir('/', null);
$cwd = $rootDir;

while (!feof($input)) {
    $line = str_replace(["\r","\n"], '', fgets($input));
    $command = parse_command($line);

    switch ($command['command']) {
        case 'ls':
            //read dir content
            while (!feof($input)) {
                //store position to rewind to beginning of following command
                $position = ftell($input);

                $line = str_replace(["\r","\n"], '', fgets($input));
                if (substr($line, 0, 1) == '$') {
                    fseek($input, $position);
                    //break ls and continue parsing commands
                    continue 3;
                }

                if ($line == '') {
                    echo "### BLIP ###\n";
                    continue 3;
                }

                $cwd->addNodeByLine($line);
            }
            break;
        case 'cd':
            if ($command['parameters'] == '..') {
                $cwd = $cwd->parentDir;
                break;
            }
            foreach ($cwd->nodes as $node) {
                if ($node::TYPE == 'elfFile') {
                    continue;
                }
                if ($node->name == $command['parameters']) {
                    $cwd = $node;
                    break 2;
                }
            }
            throw new Exception("wrench");
            break;
        default:
            throw new Exception('unknown command');
    }
}


#var_dump($rootDir->getDirListRec());
var_dump($rootDir->printDirSizesRec(null, 2476859));
#$rootDir->printDirListRec();


function parse_command($line)
{
    $matches = explode(' ', $line);
    $c = [
        'command' => $matches[1],
    ];
    if(isset($matches[2])) {
        $c['parameters'] = $matches[2];
    }
    return $c;
}
