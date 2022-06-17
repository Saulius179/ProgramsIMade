<?php 

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset: UTF-8"); // data format for request
header("Access-Control-Allow-Methods: POST");

include_once("../config/database.php");
include_once("../classes/user.php");

$db = new Database();
$connection = $db->connect();
$user = new User($connection);


if($_SERVER['REQUEST_METHOD']==="POST"){
        
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->id)){
        
        $user->id = $data ->id;
        
        if($user->delete_user()){
            
            http_response_code(200);
            echo json_encode(array(
                "status"=>1,
                "message"=>" User ".$user->id." deleted "
            ));
            
        }else{
            
            http_response_code(404);
            echo json_encode(array(
                "status"=>0,
                "message"=>" Delete failed "
            ));
        }
        
    }else{
        http_response_code(404);
        echo json_encode(array(
            "status"=>0,
            "message"=>" More data required for this operation. Data required: id "
        ));
    }
    
}else{
    http_response_code(503);
    echo json_encode(array(
        "status"=>0,
        "message"=>" Only POST Access. "
    ));
}



?>