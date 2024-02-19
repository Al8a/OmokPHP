<?php
    /*
        Alan Ochoa | 80639123 | aeochoa2@miners.utep.edu
        Diana Garcia | 88797336 | dagarcia88@miners.utep.edu
    */
    require_once("../play/Board.php");
    define('STRATEGY', 'strategy');     // Constant


    /* Check if strategy array exists */
    function check_strategy() {
        if (!array_key_exists(STRATEGY, $_GET)) {
            echo "{\"response\": false, \"reason\": \"Strategy Not Specified\"}";
            exit;
        }
    }

    /* Create and store a new Omok session/game pid into the writable directory */
    function store_game($path, $txt) {
        $file = fopen("../data/" . $path . ".txt", "w") or die("Unable to open file!");
        fwrite($file, $txt);
        fclose($file);
    }


    $strategies = ["Smart", "Random"];  // Supported Strategies
    check_strategy();                   // Check Key
    $strategy = $_GET[STRATEGY];        // ?[QUERY STRING INPUT]
    //echo $strategy;


    /*
        Determine if user input from $_GET is in strategies
        'Smart' => 'SmartStrategy'
        'Random' => 'RandomStrategy'
    */
    if (in_array($strategy, $strategies)) {
        /* Initialize and Store valid Omok board */
        $board = new Board(15, $_GET[STRATEGY]);
        store_game($board->pid, $board->toJson());
        echo "{\"response\":true, \"pid\":\"" . $board->pid . "\"}";
    } else {
        echo json_encode(array("response" => false , "reason" => "Unknown Strategy"));
    }
