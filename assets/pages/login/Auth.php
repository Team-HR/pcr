<?php

    include "../../libs/Db.php";


    class Auth extends Db {

        private $password;
        private $userAccount;

        public function __construct() 
        {
            parent::__construct();
        }
    
    
        public function setBasic($data,$password,$isRenew = false)
        {
            if($isRenew){
                $query = "SELECT * from spms_accounts where employees_id='$data'";
            }else{
                $query = "SELECT * from spms_accounts where username='$data'";
            }

            $this->userAccount = $this->send($query);

            $this->password = $password;

        }

        public function login(){
            if($this->verify()){
                return $this->getInfo();
            }else{
                return false;
            }
        }

        private function verify(){
            return password_verify($this->password, @$this->userAccount['password']);
        }   

        private function send($query){
            $query = $this->mysqli->query($query);
            $query = $query->fetch_assoc();
            return $query;
        }

        private function getInfo(){
            @$employees_id = $this->userAccount['employees_id'];

            $info = "SELECT * FROM `employees` where employees_id='$employees_id'";
            $info = $this->mysqli->query($info);
            $info = $info->fetch_assoc();
            return $info;
        }
    
    
    }








?>