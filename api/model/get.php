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
    
    $name=isset($_GET["name"])?$_GET["name"]:null;

    $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));
            
    $query="SELECT * FROM model ";
    if($name) $query=$query."WHERE name=:name";
    $stmt=$conn->prepare($query);
    if($name)$stmt->bindParam(":name",$name);
    $stmt->execute();
    $result=$stmt->fetchAll();
    $data=null;
    foreach($result as $r){
        $query="SELECT id, name, desc FROM class WHERE model_id=:id";
        $stmt=$conn->prepare($query);
        $stmt->bindParam(":id",$r["name"]);
        $stmt->execute();
        $res=$stmt->fetchAll();
        $dclass=null;
        foreach($res as $ri){
            $dclass[]=array(
                "id"=>$ri["id"],
                "name"=>$ri["name"],
                "description"=>$ri["desc"]
            );
        }

        $data[]= array(
            "name" => $r["name"],
            "description" => $r["desc"],
            "created" => $r["creation_date"],
            "last_train" => $r["last_train"],
            "classes" => $dclass
        );
    }
    http_response_code(200);
    echo json_encode(array("result"=>$data),true);
    
    
?>