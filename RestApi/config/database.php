<?php 

class Database{
    
    private $dbhost;
    private $dbuser;
    private $dbpass;
    private $dbname;
    private $conn;
    
    public function connect(){
        
        $this->dbhost = 'localhost';
        $this->dbuser = 'root';
        $this->dbpass = 'Razor/15';
        $this->dbname= 'api_tutorial';
        $this->conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        
        if($this->conn->connect_errno){
            print_r($this->conn->connect_error);
            exit;
        }else{
            return $this->conn;
            //print_r($this->conn);
        }
        
    }  
}


//$db= new Database();
//$db->connect();
?>