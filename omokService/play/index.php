<?php
    /*
        Alan Ochoa | 80639123 | aeochoa2@miners.utep.edu
        Diana Garcia | 88797336 | dagarcia88@miners.utep.edu
    */
    require_once('Board.php');
    require_once('Game.php');
    require_once('Move.php');

    define('PID', 'pid');
    define('STRATEGY', 'strategy');
    define('MOVE', 'move');
    define('DATA_DIR', '../data/');
    define('DATA_EXT', '.txt');


    class Response {
        public $response;
        public $ack_move;
        public $move;

        function __construct($response, $ack_move, $move) {
            $this->response = $response;
            $this->ack_move = $ack_move;
            $this->move = $move;
        }
    } // End Response Class



    class Index {
        public $board;
        public $game;
        public $x_req;
        public $y_req;
        public $x_res;
        public $y_res;


        /* Check X Y values if within 15x15 array bound */
        function check_coordinate_ranges($x, $y) {
            if ($x > 14 || $x < 0) {
                echo "{\"response\": false, \"reason\": \"Invalid x coordinate,\"}";
                exit;
            }
            if ($y > 14 || $y < 0) {
                echo "{\"response\": false, \"reason\": \"Invalid y coordinate,\"}";
                exit;
            }
        }

        /*
            Check whether game being reference can be found in Data Directory.
            ../Data/- Contains saved pids - game sessions
        */
        function check_pid_validity() {
            if (!array_key_exists(PID, $_GET)) {
                echo "{\"response\": false, \"reason\": \"Pid not specified\"}";
                exit;
            }
            $files = scandir(DATA_DIR);
            if (!in_array($_GET[PID] . DATA_EXT, $files)) {
                echo "{\"response\": false, \"reason\": \"Pid not specified\"}";
                exit;
            }
        }


        public function check_move_validity() {
            if (!array_key_exists(MOVE, $_GET)) {
                echo "{\"response\": false, \"reason\": \"Move not well-formed\"}";
                exit;
            }
            $coordinates = Index::get_coordinates_from_string($_GET['move']);
            $this->x_req = intval($coordinates[0]);
            $this->y_req = intval($coordinates[1]);
        }


        /* If Stone placement at passed coordinates already taken echo error */
        public function check_stone_placement() {
            $this->board = Board::get_board($_GET[PID]);
            if ($this->board->places[$this->x_req][$this->y_req] != 0) {
                echo "{\"response\": false, \"reason\": \"Move not allowed! Coordinate contains Piece\"}";
                exit;
            }
        }

        /*
        A normal response will be a JSON string like:
             {"response": true,
              "ack_move": {
                "x": 4,
                "y": 5,
                "isWin": false,   // winning move?
                "isDraw": false,  // draw?
                "row": []},       // winning row if isWin is true
              "move": {
                "x": 4,
                "y": 6,
                "isWin": false,
                "isDraw": false,
                "row": []}}
        */
        function send_json_response() {
            /* Player */
            $ack_move = new Move(
                        $this->x_req,
                        $this->y_req,
                        $this->board->player_won(1),
                        $this->board->check_space(),
                        $this->game->get_player1_returning_row());

            /* Computer */
            $move = new Move(
                    $this->x_res,
                    $this->y_res,
                    $this->board->player_won(2),
                    $this->board->check_space(),
                    $this->game->get_player2_returning_row());

            $response = new Response(true, $ack_move, $move);
            echo json_encode($response);
        }


        function get_coordinates_from_string($move_string) {
            $move_list = explode(",", $move_string);
            if (sizeof($move_list) != 2) {
                echo "{\"response\": false, \"reason\": \"Invalid Move Format\"}";
                exit;
            }
            $this->check_coordinate_ranges($move_list[0], $move_list[1]);
            return $move_list;
        }

        function on_start() {
            $this->check_pid_validity();
            $this->check_move_validity();
            $this->check_stone_placement();

            $this->game = new Game($this->board);
            $this->game->make_client_move($this->x_req, $this->y_req);

            if ($this->board->check_space()) {
                Index::send_json_response();
                exit;
            }

            $move_coordinates = $this->game->get_server_move();
            $this->x_res = $move_coordinates[0];
            $this->y_res = $move_coordinates[1];
            $this->board->places[$this->x_res][$this->y_res] = 2;
            $this->board->update_file();
            Index::send_json_response();
        }
    } // End Index Class

    /* on play directory access */
    $index = new Index();
    $index->on_start();

