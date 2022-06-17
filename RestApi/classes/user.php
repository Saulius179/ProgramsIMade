<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

class User {
    
    public $id;
    public $name;
    public $age;
    
    private $conn;
    private $table_name;
    
    public function __construct($db){
        $this->conn=$db;
        $this->table_name = 'users';
    }
    
    public function create_data(){
        $query =" INSERT INTO ".$this->table_name." (`id`, `name`, `age`) VALUES (?, ?, ?) ";
        $obj = $this->conn->prepare($query);
        // REMOVE EXTRA CHARACTERS
        $this->name = htmlspecialchars(strip_tags($this->name));   
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $obj->bind_param("isi", $this->id,$this->name, $this->age);
        if($obj->execute()){
            return true;
        }else {return false;}  
    }
    
    
    public function get_all_data(){
        
        $sql_query = "SELECT * from ".$this->table_name;
        $sql_obj = $this->conn-> prepare($sql_query);
        $sql_obj->execute();
        return $sql_obj->get_result();
    }
    
    public function get_single_user(){
        
        $sql_query = "SELECT * FROM ".$this->table_name." WHERE id=?" ;
        $obj =  $this->conn->prepare($sql_query);
        $obj->bind_param("i", $this->id);
        $obj ->execute();
        $data = $obj->get_result();
        return $data->fetch_assoc();
    }
    
    
    public function update_user(){
        $sql_query = "UPDATE ".$this->table_name." SET `name`=?, age = ? WHERE  `id`=?";
        $query_obj = $this->conn->prepare($sql_query);
        // SANITIZE INPUTS
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->age = htmlspecialchars(strip_tags($this->age));
        // change ? into variables in query
        $query_obj->bind_param("sii", $this->name, $this->age, $this->id);
        
        if($query_obj->execute()){
            return true;
        }else{return false;}
    }
    
    
    public function delete_user(){
        $sql_query = "DELETE FROM ".$this->table_name." WHERE  `id`=?;";
        $query_obj = $this->conn->prepare($sql_query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $query_obj->bind_param("i",$this->id);
        if($query_obj->execute()){
            return true;
        }else{
            return false;
        }
        
        
    }
    
}



?>