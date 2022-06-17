<?php 
header("Access-Control-Allow-Origin: *"); // allow any domain
header("Access-Control-Allow-Methods: GET"); // request type

include_once("../config/database.php");
include_once("../classes/user.php");
$db = new Database();
$connection = $db->connect();
$user = new User($connection);

if($_SERVER['REQUEST_METHOD']==="GET"){
    
    //GET ID FROM URI, NOT JSON OBJECT
    $param = isset($_GET['id']) ? intval($_GET['id']): "";
    
    
    if(!empty($param)){
        $user->id = $param;
        $user_data = $user -> get_single_user();
        //print_r($user_data);
        if(!empty($user_data)){
            http_response_code(200);
            echo json_encode(array(
                "status" => 1,
                "data" =>$user_data
            ));
        }else{
            http_response_code(404); 
            echo json_encode(array(
                "status"=>0,
                "message"=> " User not found "
            ));
        }
    }
    
}else {
    http_response_code(503);
    echo json_encode(array(
        "status" => 0,
        "message" => " Access Denied"
    ));
}



?>