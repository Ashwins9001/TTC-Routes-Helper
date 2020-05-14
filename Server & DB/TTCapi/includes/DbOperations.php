<?php

/*
    Used to define implementation to POST/GET methods 
*/

Class DbOperations {
    private $connection_link;
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $database = new DbConnect;
        $this->connection_link = $database->connect(); //make link to db
    }
    public function createUser($emailIn, $passwordIn, $balanceIn, $first_nameIn, $last_nameIn) {
        if(!$this->doesEmailExist($emailIn)) {
            //Prepare used to exec SQL queries continuously by linked db, req name table & its col names, must bind params into query 
            $query = $this->connection_link->prepare("INSERT INTO users (email, password, balance, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
            //s = str, d = dbl; match data types to params, then check if successful 
            $query->bind_param("ssdss", $emailIn, $passwordIn, $balanceIn, $first_nameIn, $last_nameIn);
            if($query->execute()){
                return USER_CREATED;
            } else {
                return USER_FAILURE;
            }
        }
        return USER_EXISTS;
    }
    
    //authenticate user upon login 
    public function userLogin($email, $password){
        if($this->doesEmailExist($email)){ //Ensure email for user in db
            $hashed_password = $this->getPasswordForEmail($email); //Get password for unique user 
            if(password_verify($password, $hashed_password)){ //password_verify built-in func for php
                return USER_AUTHENTICATED;
            } else {
                return USER_NOT_AUTHENTICATED;
            } //Verify password user enters to match hashed password, decoded from db
            
        } else{
            return USER_NOT_FOUND;
        }
    }

    

    private function getPasswordForEmail($email){ //return hashed pass from db
        $query = $this->connection_link->prepare("SELECT password FROM users WHERE email = ?"); //exlude $ for existing var
        $query->bind_param("s", $email); //only req email to select table rows 
        $query->execute();
        $query->bind_result($password); //get matching rows 
        $query->fetch();
        return $password;
    }


    public function getUserByEmail($email){ //return user 
        $query = $this->connection_link->prepare("SELECT id, email, balance, first_name, last_name FROM users WHERE email = ?");
        $query->bind_param("s", $email); //only req email to select table rows 
        $query->execute();
        $query->bind_result($id, $email, $balance, $first_name, $last_name); //get matching rows 
        $query->fetch();
        $user = array();
        $user['id'] = $id;
        $user['email'] = $email;
        $user['balance'] = $balance;
        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;
        return $user;
    }

    public function currentBalanceChange($email, $balance){
        if($this->doesEmailExist($email)) {
            $temp = $balance;
            $query = $this->connection_link->prepare("UPDATE users SET balance = ? WHERE email = ?");
            $query->bind_param("ds", $balance, $email);
            $query->execute();

            $query = $this->connection_link->prepare("SELECT balance FROM users WHERE email = ?");
            $query->bind_param("s", $email); 
            $query->execute();
            $query->bind_result($balance);
            $query->fetch(); 
            if($temp == $balance){
                return BALANCE_FAILURE;
            } else {
                return BALANCE_UPDATE;
            } 
        } else {
            return USER_FAILURE;
        }
    }

    public function getError()
    {
        return ("Error description: " . $mysqli -> error);
    }

    private function doesEmailExist($emailIn){
        $query = $this->connection_link->prepare("SELECT id FROM users WHERE email = ?");
        $query->bind_param("s", $emailIn);  
        $query->execute();
        $query->store_result(); //if result contains any matching rows, email exists
        return $query->num_rows > 0;
    }
}
?>