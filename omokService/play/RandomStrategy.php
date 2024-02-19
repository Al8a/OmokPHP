<?php
    require_once('MoveStrategy.php');

    class RandomStrategy extends MoveStrategy {
        function pickPlace() {
            $free_spaces = $this->get_free_spaces();
            return $free_spaces[rand(0, sizeof($free_spaces))];
        }


        function get_free_spaces() {
            echo "";
            $free_spaces = [];
            for ($i = 0; $i < $this->board->size; $i++) {
                for ($j = 0; $j < $this->board->size; $j++) {
                    if ($this->board->check_empty($i, $j)) {
                        array_push($free_spaces, [$i, $j]);
                    }
                }
            }
            return $free_spaces;
        }
    } // End RandomStrategy Class