<?php
class Card { // This class contains all the functions to format and save the data

    public $inputs;
    public $outputs;
    public $output;

    function __constructor() {
        $this->inputs = array(); // the images are a grid of 14x14 pixels which makes for a total of 196
        $this->outputs = array(); // the number of possible outputs; from 0 to 9
    }

    function imageLoad($images, $offset) { // Images is an array of 1,960,000 bytes, each one representing a pixel (0-255) of the 10,000 * 14x14 (196) images
                                                // We know one image consists of 196 bytes so the location is: offset*196
        
        for ($i = 0; $i < 196; $i++) {
            $this->inputs[$i] = $images[$i+$offset] / 128.0 - 1.0; // We then store each pixel in the array inputs[] after converting it from (0 - 255) to (+1 - -1) as they vary on the greyscale 
        }
    }

    function labelLoad($labels, $offset) {
        $this->output = $labels[$offset];
        for ($i = 0; $i < 10; $i++) {
            if ($i == $this->output) {
                $this->outputs[$i] = 1.0;
            } else {
                $this->outputs[$i] = -1.0;
            }
        }
    }

}
?>