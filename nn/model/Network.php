<?php

require "Neuron.php";
require "NNClass.php";
require "Card.php";
require "Sigmoid.php";

Sigmoid::setupSigmoid();

class Network{
    public $name;
    public $classes;

    public $input_layer;
    public $hidden_layer;
    public $output_layer;
    public $bestIndex;

    function __construct($name,$input,$hidden) {
        $this->name=$name;
        for ($i = 0; $i < $input; $i++) {
            $this->input_layer[$i] = new Neuron(array());
        }
        for ($j = 0; $j < $hidden; $j++) {
            $this->hidden_layer[$j] = new Neuron($this->input_layer);
        }
    }
    
    function save(){
        file_put_contents( "../../networks/$this->name/$this->name", serialize($this));
    }

    function addClass($class){
        $this->classes[]=$class;

        for ($k = 0; $k < count($this->classes); $k++) {
            $this->output_layer[$k] = new Neuron($this->hidden_layer);
        }
    }

    function respond($matrix) {
        if(isset($this->output_layer)){
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
        }else{
            throw new Exception("Error, this model doesn't have any class");
        }

    }

    function train() {
        $error=null;
        for ($i = 0; $i < count($this->input_layer); $i++) {
            $this->input_layer[$i] = new Neuron(array());
        }
        for ($j = 0; $j < count($this->hidden_layer); $j++) {
            $this->hidden_layer[$j] = new Neuron($this->input_layer);
        }
        for ($k = 0; $k < count($this->classes); $k++) {
            $this->output_layer[$k] = new Neuron($this->hidden_layer);
        }
        if(!isset($this->classes)){
            throw new Exception("Error, this model doesn't have any class");
        }else
        for($l=0; $l<count($this->classes); $l++){
            $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));
            $stmt=$conn->prepare("SELECT data_path FROM class WHERE id=:id");
            $stmt->bindParam(":id",$this->classes[$l]->id);
            $stmt->execute();
            $path=$stmt->fetchAll();
            if(count($path)>0){
                $fp = fopen($path[0][0],"rb");
                
                while (!feof($fp)) {
                    $image=null;
                    $data = fread($fp,256);    
                    $nbytes = strlen($data); 
                    for ($i = 0; $i < $nbytes; $i++) {
                        $subdata = substr($data,$i,1);
                        $arr = unpack("C*",$subdata);
                        
                        foreach ($arr as $key => $value) {
                            $image[]=$value;
                        }
                    }
                    if($image && count($image)==256){
                        $c=new Card();
                        $c->imageLoad($image);
                        $c->labelLoad($l, count($this->classes));
                        $cards[]=$c;
                        
                    }
                }
                fclose($fp);
            }else{
                throw new Exception("Error, this model's classes data are corrupted");
            }
        }
        if(isset($cards))
        foreach($cards as $c){
            $this->respond($c);
            $outputs=$c->outputs;
            for ($k = 0; $k < count($this->output_layer); $k++) {
                $this->output_layer[$k]->setError($outputs[$k]);
                $this->output_layer[$k]->train();
            }
            $best = -1.0;
            for ($k = 0; $k < count($this->output_layer); $k++) {
                if ( $this->output_layer[$k]->output > $best) $best = $k;
            }
                
            for ($k = 0; $k < count($this->hidden_layer); $k++) {
                $this->hidden_layer[$k]->train();
            }
        }
        $this->save();
    
    }
}

?>