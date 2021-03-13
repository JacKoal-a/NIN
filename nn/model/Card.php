<?php
class Card { // This class contains all the functions to format and save the data

    public $inputs;
    public $outputs;
    public $output;

    function __constructor() {
        $this->inputs = array(); // the images are a grid of 14x14 pixels which makes for a total of 196
        $this->outputs = array(); // the number of possible outputs; from 0 to 9
    }

    function imageLoad($image) {
        for ($i = 0; $i < 256; $i++) {
            $this->inputs[$i] = $image[$i] / 128.0 - 1.0; // We then store each pixel in the array inputs[] after converting it from (0 - 255) to (+1 - -1) as they vary on the greyscale 
        }
    }

    function labelLoad($label, $nout) {
        $this->output = $label;
        for ($i = 0; $i < $nout; $i++) {
            if ($i == $this->output) {
                $this->outputs[$i] = 1.0;
            } else {
                $this->outputs[$i] = -1.0;
            }
        }
    }

}
?>