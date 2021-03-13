<?php

require "model/Network.php";
require "model/Card.php";
require "model/Sigmoid.php";
Sigmoid::setupSigmoid();
$neuralnet=unserialize(file_get_contents("../trained/nn1++"));
//$neuralnet = new Network(196, 49, 10);
$training_set = array();
$testing_set = array();
loadData();
/*
for($i=0;$i<count($training_set);$i++){
  $neuralnet->respond($training_set[$i]);
  $neuralnet->train($training_set[$i]->outputs);
}*/


//file_put_contents("trained/nn1++", serialize($neuralnet));
  
$v= rand(0, 200);
$neuralnet->respond($testing_set[$v]);
$respTotal=0.0;
for($k=0;$k<count($neuralnet->output_layer);$k++){
  $resp[$k]=$neuralnet->output_layer[$k]->output;
  $respTotal+=$resp[$k]+1;
}

for($k=0;$k<count($neuralnet->output_layer);$k++){
  echo $k ." => ". ($neuralnet->output_layer[$k]->output+1)/$respTotal *100 ."<br>" ;
}




echo "real ".$testing_set[$v]->output . " predict " . $neuralnet->bestIndex."<br>";

for($i=0;$i<196;$i++){
  if($i%14==0){
      echo "<br>";
    }
    echo "<i style='color:rgb(".(128*(1-$neuralnet->input_layer[$i]->output))." ".(128*(1-$neuralnet->input_layer[$i]->output))." ".(128*(1-$neuralnet->input_layer[$i]->output)).");'>0</i>" ;
  
}



/*
new Network(196, 49, 10);
file_put_contents("trained/nn1", serialize($neuralnet));
$neuralnet=unserialize(file_get_contents("trained/nn1"));
*/


function loadData(){ // In this function we initialise all out data in two seperate arrays, training[] and test[]
  global $training_set, $testing_set;

    $images = unpack("C*",file_get_contents("data/t10k-images-14x14.idx3-ubyte"));
    $labels = unpack("C*",file_get_contents("data/t10k-labels.idx1-ubyte"));
    //print_r($labels);
    $tr_pos = 0;
    $te_pos = 0;
    for ($i = 0; $i < 10000; $i++) {
      if ($i % 5 != 0) { 
        $training_set[$tr_pos] = new Card();
        $training_set[$tr_pos]->imageLoad($images, 16 + $i * 196); // There is an offset of 16 bytes
        $training_set[$tr_pos]->labelLoad($labels, 9 + $i);  // There is an offset of 8 bytes
        $tr_pos++;
      } else {
        $testing_set[$te_pos] = new Card();
        $testing_set[$te_pos]->imageLoad($images, 16 + $i * 196);  // There is an offset of 16 bytes 
        $testing_set[$te_pos]->labelLoad($labels, 9 + $i);  // There is an offset of 8 bytes
        $te_pos++;
      }
    }
  }
?>