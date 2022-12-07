<?php

abstract class Node
{
    protected $name;

    public $parentDir;
    public const TYPE = 'node';

    public function __construct($name, $parentDir)
    {
        $this->name = $name;
        $this->parentDir = $parentDir;
    }

    abstract function getSize();
}

class elfDir extends Node
{
    private $nodes = [];
    public const TYPE = 'elfDir';

    function getSize()
    {
        $size = 0;
        foreach ($nodes as $node) {
            $size += $nodes->getSize();
        }
        return $size;
    }

    public function addNodeByLine($line)
    {
        // add dir
        $r = preg_match('#^dir (.+)$#', $line, $matches);
        if ($r == 1) {
            foreach($this->nodes as $node) {
                if($node->name == $matches[1]) {
                    echo 'already exists';die();
                }
            }
            $this->nodes[] = new elfDir($matches[1], $this);
            return;
        }

        //add file
        $r = preg_match('#^(\d+) (.+)+#', $line, $matches);
        if ($r) {
            foreach($this->nodes as $node) {
                if($node->name == $matches[1]) {
                    echo 'already exists';die();
                }
            }
            $this->nodes[] = new elfFile($matches[2], $matches[1]);
            return;
        }

        throw new Exception('error parsing line #' . $line . '#');
    }

    public function getDirList()
    {
        $name_list = [];
        foreach ($this->nodes as $node) {
            $name_list[] = $node->name;
        }
        return $name_list;
    }

    public function getDirListRec()
    {
        $name_list = [];
        foreach ($this->nodes as $node) {
            if ($node::TYPE == 'elfFile') {
                $name_list[] = $node->name . ' ('.$node->size.')';
                continue;
            }
            $name_list[] = $node->getDirListRec();
        }
        return $name_list;
    }

    public function printDirListRec($level = 0)
    {
        $name_list = [];
        echo str_pad('', $level, ' '). '- ' . $this->name . " (dir)\n";
        foreach ($this->nodes as $node) {
            if ($node::TYPE == 'elfFile') {
                echo str_pad('', $level, ' '). '- ' . $node->name . ' (file, size='.$node->size.')' . "\n";
                continue;
            }

            $name_list[] = $node->printDirListRec(($level + 1));
        }
        return $name_list;
    }


    public function printDirSizesRec($max_size = null, $min_size = null)
    {
        $size = 0;
        foreach ($this->nodes as $node) {
            if ($node::TYPE == 'elfFile') {
                $size += $node->size;
                continue;
            }
            $size +=  $node->printDirSizesRec($max_size, $min_size);
        }
        if(($max_size != null) && ($size <= $max_size ) && ($this::TYPE == 'elfDir')) {
            echo $this->name . ": ". $size . "\n";
        }

        if(($min_size != null) && ($size >= $min_size ) && ($this::TYPE == 'elfDir')) {
            echo $this->name . ": ". $size . "\n";
        }
        return $size;
    }

    public function __get($key)
    {
        switch ($key) {
            case 'nodes':
                return $this->nodes;
            case 'name':
                return $this->name;
        }
    }
}

class elfFile extends Node
{
    public const TYPE = 'elfFile';

    private $size;
    public function __construct($name, $size)
    {
        $this->name = $name;
        $this->size = intval($size);
    }

    function getSize()
    {
        return $this->size;
    }

    public function __get($key)
    {
        switch ($key) {
            case 'name':
                return $this->name;
            case 'size':
                return $this->size;
        }
    }
}
