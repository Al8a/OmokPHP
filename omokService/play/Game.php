<?php
    require_once('SmartStrategy.php');
    require_once('RandomStrategy.php');

    class Game {
        public $board;
        public $strategies;

        function __construct($board) {
            $this->board = $board;
            $this->strategies = array('Smart' => 'SmartStrategy',
                                     'Random' => 'RandomStrategy');
        }

        /* Human */
        function make_client_move($x, $y) {
            $this->board->places[$x][$y] = 1;
            $this->board->update_file();
        }

        /* Computer */
        function get_server_move() {
            $strategy = new $this->strategies[$this->board->strategy]($this->board);
            return $strategy->pickPlace();
        }


        /* Player - 1 | Comp - 2 */
        function get_player1_returning_row() {
            if (!$this->board->player_won(1)) {
                return [];
            }
            if (count($this->board->winner_row) === 0) {
                return [];
            }
            return $this->board->winner_row;
        }

        function get_player2_returning_row() {
            if (!$this->board->player_won(2)) {
                return [];
            }
            if (count($this->board->winner_row) === 0) {
                return [];
            }
            return $this->board->winner_row;
        }
    } // End Game Class