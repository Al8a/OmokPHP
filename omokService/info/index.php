<?php
    /*
        Alan Ochoa | 80639123 | aeochoa2@miners.utep.edu
        Diana Garcia | 88797336 | dagarcia88@miners.utep.edu
    */
    class GameInfo {
        public $size;
        public $strategies;

        function __construct($size, $strategies) {
            $this->size = $size;
            $this->strategies = $strategies;
        }
    }

    $strategies = array('Smart' => 'SmartStrategy', 'Random' => 'RandomStrategy');  // Available strategies
    $info = new GameInfo(15, array_keys($strategies));
    echo json_encode($info);                                                       // Echo available player options

