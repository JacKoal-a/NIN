<?php

require "Neuron.php";

class Network{

    public $input_layer;
    public $hidden_layer;
    public $output_layer;
    public $bestIndex = 0;

    function __construct($input,$hidden,$output) {
        
        for ($i = 0; $i < $input; $i++) {
            $this->input_layer[$i] = new Neuron(array());
        }
        for ($j = 0; $j < $hidden; $j++) {
            $this->hidden_layer[$j] = new Neuron($this->input_layer);
        }
        for ($k = 0; $k < $output; $k++) {
            $this->output_layer[$k] = new Neuron($this->hidden_layer);
        }

    }

    function respond($matrix) {

        for ($i = 0; $i < count($this->input_layer); $i++) {
            $this->input_layer[$i]->output = $matrix->inputs[$i];
        }
        for ($j = 0; $j < count($this->hidden_layer); $j++) {
            $this->hidden_layer[$j]->respond();
        }
        for ($k = 0; $k < count($this->output_layer); $k++) {
            $this->output_layer[$k]->respond();
        }
        $this->bestIndex = 0;
        for($i=0;$i < count($this->output_layer);$i++){
            if($this->output_layer[$i]->output > $this->output_layer[$this->bestIndex]->output)
                $this->bestIndex=$i;
        }
    }

    function train($outputs) {

        for ($k = 0; $k < count($this->output_layer); $k++) {
            $this->output_layer[$k]->setError($outputs[$k]);
            $this->output_layer[$k]->train();
        }
        $best = -1.0;
        for ($i = 0; $i < count($this->output_layer); $i++) {
          if ( $this->output_layer[$i]->output > $best) $bestIndex = $i;
        }

        for ($j = 0; $j < count($this->hidden_layer); $j++) {
            $this->hidden_layer[$j]->train();
        }
    
    }

    
    


}

?>