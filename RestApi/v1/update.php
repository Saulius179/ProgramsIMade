<?php 

header("Access-Control-Allow-Origin: *"); // allow any domain
header("Content-type: application/json; charset: UTF-8"); // data format for request
header("Access-Control-Allow-Methods: POST"); // request type

include_once("../config/database.php");
include_once("../classes/user.php");

$db = new Database();
$connection = $db->connect();
$user = new User($connection);


if($_SERVER['REQUEST_METHOD']==="POST"){
    
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->name)&&!empty($data->id)&&!empty($data->age)){
        
        $user->id = $data ->id;
        $user->name = $data ->name;
        $user->age = $data ->age;
        
        if($user->update_user()){
            
            http_response_code(200);
            echo json_encode(array(
                "status"=>0,
                "data"=>" Data updated"
            ));
            
        }else{
            http_response_code(500);
            echo json_encode(array(
                "status"=>0,
                "message"=>" Update failed "
                
            ));
        }
    }else{
        http_response_code(404);
        echo json_encode(array(
            "status"=>0,
            "message"=>" More data required. Required data: id , name, age "
            ));
    }
    
    
}else{
   http_response_code(403); 
   echo json_encode(array(
       "status" =>0,
       "message" =>"Access denied"
   ));
}

?>