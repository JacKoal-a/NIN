<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application(json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    $response = array();
    $uploadPath="uploads/";

    require "../../nn/model/Network.php";
    require "../class/ResizeImage.php";
    try {
        if (!isset($_FILES['image']['error']) || is_array($_FILES['image']['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }

        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_OK: break;
            case UPLOAD_ERR_NO_FILE: throw new RuntimeException('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE: throw new RuntimeException('Exceeded filesize limit');
            default: throw new RuntimeException('Unknown errors');
        }

        if ($_FILES['image']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search( $finfo->file($_FILES['image']['tmp_name']),array('jpg' => 'image/jpeg','png' => 'image/png'),true)) {
            throw new RuntimeException('Invalid file format');
        }
        $fname=sha1_file($_FILES['image']['tmp_name']);
        if (!move_uploaded_file( $_FILES['image']['tmp_name'], 'uploads/'.$fname.".". $ext)) {
            throw new RuntimeException('Failed to save uploaded file');
        }

        $resize = new ResizeImage('uploads/'.$fname.".". $ext);
        $resize->resizeTo(16, 16, 'exact');
        $out=$uploadPath. hash('sha256',$fname.$_FILES['image']['tmp_name']). ".jpg";
        $resize->saveImage($out);
        if (file_exists('uploads/'.$fname.".". $ext)) 
            unlink('uploads/'.$fname.".". $ext);
        $model=isset($_GET["model"])?$_GET["model"]:null;
        if($model){
            $conn = new PDO("sqlite:".realpath("../config/db/nn.sqlite3"));
            $stmt=$conn->prepare("SELECT name FROM model WHERE name=:name");
            $stmt->bindParam(":name",$model);
            $stmt->execute();
            $d=$stmt->fetchAll();
            if(count($d)>0){
                $image_data = file_get_contents($out);
                try {
                    $im = imagecreatefromstring($image_data);
                } catch (Exception $ex) {
                    throw new RuntimeException('Failed to process file');
                }
                $data=null;
                for($j=0;$j<16;$j++)
                    for($i=0;$i<16;$i++){
                        $rgb = imagecolorat($im, $i, $j);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;

                        $data[] = ($r+$g+$b)/3 ;
                    }
                
                $neuralnet=unserialize(file_get_contents("../../networks/$model/$model"));
                $c=new Card();
                $c->imageLoad($data);

                try{
                    $neuralnet->respond($c);

                    $respTotal=0.0;

                    for($k=0;$k<count($neuralnet->output_layer);$k++){
                        $resp[$k]=$neuralnet->output_layer[$k]->output;
                        $respTotal+=$resp[$k]+1;
                    }
                    for($k=0;$k<count($neuralnet->output_layer);$k++){
                        $result[$k]=array('class'=>$neuralnet->classes[$k]->name,'score'=>($neuralnet->output_layer[$k]->output+1)/$respTotal *100);
                    }

                    usort($result, function($a, $b) {return $a["score"] < $b["score"];});

                    http_response_code(200);
                    echo json_encode(array(
                        "message"=> $model." response",
                        "result"=> $result,
                    ));
                    if (file_exists($out)) 
                        unlink($out);

                }catch(Exception $e){
                    http_response_code(400);
                    echo json_encode(array("message" => $e->getMessage()));
                }
            }else{
                http_response_code(400);
                echo json_encode(array("message"=>"Error, model doesn't exist"));
            }
        }else{
            http_response_code(400);
            echo json_encode(array("message"=>"Error, invalid model param"));
        }
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode(array("message" => $e->getMessage()));
    }

?>