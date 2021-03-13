<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: PATCH");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
   
    require "../../nn/model/Network.php";

    $model=isset($_GET["model"])?$_GET["model"]:null;

    if($model){
        $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));

        $stmt=$conn->prepare("UPDATE model SET last_train = CURRENT_TIMESTAMP WHERE name = :name;");
        $stmt->bindParam(":name",$model);
        $stmt->execute();
        $count = $stmt->rowCount();

        if($count>0){
            $neuralnet=unserialize(file_get_contents("../../networks/$model/$model"));
            
            
            try{
                $neuralnet->train();
                http_response_code(200);
                echo json_encode(array(
                    "model_id"=>$model,
                    "message"=>"Model has been trained successfully",
                    "endpoints"=>array(
                        "respond"=>"/nin/api/model/respond.php?model=".$model
                    )
                ));
            }catch (Exception $e) {
                http_response_code(400);
                echo json_encode(array("message" => $e->getMessage()));
            }

        }else{
            http_response_code(400);
            echo json_encode(array(
                "model_id"=>null,
                "message"=>"Error while training model, model doesn't exist"
            ));
        }
    }else{
        http_response_code(400);
        echo json_encode(array(
            "model_id"=>null,
            "message"=>"Error while training model, use a valid model value"
        ));
    }

?>