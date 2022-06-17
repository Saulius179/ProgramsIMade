<?php 
ini_set("display_errors",1);
header("Access-Control-Allow-Origin: *"); // allow any domain
header("Access-Control-Allow-Methods: GET"); // request type

include_once("../config/database.php");
include_once("../classes/user.php");
$db = new Database();
$connection = $db->connect();
$user = new User($connection);

if($_SERVER['REQUEST_METHOD']==="GET"){
    
    $data = $user->get_all_data();    
    if($data->num_rows > 0){
        $rows["records"]= array();
        while($row = $data->fetch_assoc()){
            array_push($rows["records"], array(
                "id" => $row['id'],
                "name" => $row['name'],
                "age" => $row['age'],
            ));
        }
        
        http_response_code(200);
        echo json_encode(array(
            "status" => 1,
            "data" => $rows["records"]
        ));
    }
    
    
}else{
    http_response_code(503); // service unavailable
    echo json_encode( array(
        
        "status" => 0, 
        "message" => "Access Denied"
    ));
}



?>