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
            "desc":{description}
        }
    */

    $data = json_decode(file_get_contents("php://input"));
    
    $name=isset($data->name)?$data->name:null;
    $desc=isset($data->desc)?$data->desc:null;

    if($name){
        if($desc){
            $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));
            
            $stmt=$conn->prepare("INSERT INTO model (name,desc,creation_date,last_train) VALUES (:name,:desc,CURRENT_TIMESTAMP,null)");
            $stmt->bindParam(":name",$name);
            $stmt->bindParam(":desc",$desc);

            if($stmt->execute()){
                if(mkdir("../../networks/$name/") && mkdir("../../networks/$name/sets/")){
                    $n=new Network($name,256,64);
                    $n->save();
                    http_response_code(200);
                    echo json_encode(array(
                        "model_id"=>$name,
                        "message"=>"Model has been created successfully",
                        "endpoints"=>array(
                            "create_class"=>"/nin/api/class/create.php",
                            "train"=>"/nin/api/model/train.php?model=".$name,
                            "respond"=>"/nin/api/model/respond.php?model=".$name
                        )
                    ));
                }else{
                    http_response_code(500);
                    echo json_encode(array("model_id"=>null,
                    "message"=>"Error while creating model, internal server error"));
                }
            }else{
                http_response_code(400);
                echo json_encode(array("model_id"=>null,
                "message"=>"Error while creating model, model name already exists"));
            }
        }else{
            http_response_code(400);
            echo json_encode(array("model_id"=>null,
            "message"=>"Error while creating model, use a valid description value"));
        }
    }else{
        http_response_code(400);
        echo json_encode(array("model_id"=>null,
        "message"=>"Error while creating model, use a valid name value"));
    }

?>