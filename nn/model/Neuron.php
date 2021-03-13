<?php
    
    class Neuron{
        public $inputs; 
        public $weights;
        public $output;
        public $error;
      
        function __construct($p_inputs) {
            $this->error = 0.0;
            for ($i = 0; $i < count($p_inputs); $i++) {
                $this->inputs[$i] = $p_inputs[$i];
                $this->weights[$i] = rand(-10, 10)/10;
            }
        }

        function respond() {
            $input = 0.0;
            for ($i = 0; $i < count($this->inputs); $i++) {
              $input += $this->inputs[$i]->output * $this->weights[$i];
            }
            $this->output = Sigmoid::lookupSigmoid($input);
            $this->error = 0.0;
        }
          
        function setError($desired) {
            $this->error = $desired - $this->output;
        }

        function train() {
            $delta =(1.0 - $this->output) * (1.0 + $this->output) * $this->error * 0.01;
            for ($i = 0; $i < count($this->inputs); $i++) {
                $this->inputs[$i]->error += $this->weights[$i] * $this->error;
                $this->weights[$i] += $this->inputs[$i]->output * $delta;
            }
        }
      
    }
?>