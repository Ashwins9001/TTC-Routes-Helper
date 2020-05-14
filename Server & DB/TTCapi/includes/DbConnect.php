<?php 

class DbConnect {
    
    private $connection_link;
    //Call to connect to DB
    function connect() {
        //Require const
        include_once dirname(__FILE__) . '/Constants.php'; //cwd & grab file
        $this->connection_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); //store instantiation, params derived from const file
        if(mysqli_connect_errno()) {
            echo "Failed to connect" . mysqli_connect_error(); //concat to str 
            return null;
        }
        return $this->connection_link;
    }
}


?>