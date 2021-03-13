<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require "../../nn/model/Network.php";

    $data = json_decode(file_get_contents("php://input"));
    
    $name=isset($_GET["name"])?$_GET["name"]:null;

    $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));
            
    $query="SELECT id, name, desc, model_id FROM class ";
    if($name) $query=$query."WHERE id=:id";
    $stmt=$conn->prepare($query);
    if($name)$stmt->bindParam(":id",$name);
    $stmt->execute();
    $result=$stmt->fetchAll();
    $data=null;
    foreach($result as $r){
        
        $data[] = array(
            "id" => $r["id"],
            "name" => $r["name"],
            "description" => $r["desc"],
            "model_id" => $r["model_id"]
        );
    }
    http_response_code(200);
    echo json_encode(array("result"=>$data));
    
    
?>