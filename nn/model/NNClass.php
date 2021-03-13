<?php
    class NNClass{
        public $id;
        public $name;
        public $data_path;
        public $model;
        public $desc;
        
        function __construct($id,$name,$data_path,$model,$desc) {
            $this->id=$id;
            $this->name=$name;
            $this->data_path = $data_path;
            $this->model=$model;
            $this->desc=$desc;
        }
        

    }
?>