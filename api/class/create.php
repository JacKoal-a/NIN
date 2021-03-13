<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    require "../../nn/model/Network.php";
    /*
        {
            "name":{name},
            "desc":{description},
            "model":{model}
        }
    */

    $data = json_decode(file_get_contents("php://input"));
    
    $name=isset($data->name)?$data->name:null;
    $desc=isset($data->desc)?$data->desc:null;
    $model=isset($data->model)?$data->model:null;
    $id=$model."-".$name;
    $data_path = "../../networks/$model/sets/".$id.".dat";

    if($name){
        if($desc){
            $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));

            $stmt=$conn->prepare("INSERT INTO class (id, name, data_path, model_id, desc) VALUES (:id, :name, :data_path, :model, :desc)");
            $stmt->bindParam(":id",$id);
            $stmt->bindParam(":name",$name);
            $stmt->bindParam(":data_path",$data_path);
            $stmt->bindParam(":model",$model);
            $stmt->bindParam(":desc",$desc);
            
            if($stmt->execute()){
                file_put_contents($data_path,null);
                $neuralnet=unserialize(file_get_contents("../../networks/$model/$model"));
                $neuralnet->addClass(new NNClass($id,$name,$data_path,$model,$desc));
                $neuralnet->save();
                http_response_code(200);
                echo json_encode(array(
                    "class_id"=>$id,
                    "message"=>"Class has been created successfully",
                    "endpoints"=>array(
                        "add_image"=>"/nin/api/class/add_image.php"
                    )
                ));

            }else{
                http_response_code(400);
                echo json_encode(array("class_id"=>null,
                "message"=>"Error while creating class, class name already exists"));
            }
        }else{
            http_response_code(400);
            echo json_encode(array("class_id"=>null,
            "message"=>"Error while creating class, use a valid description value"));
        }
    }else{
        http_response_code(400);
        echo json_encode(array("class_id"=>null,
        "message"=>"Error while creating class, use a valid name value"));
    }

?>