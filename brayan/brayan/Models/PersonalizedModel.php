<?php
    Class PersonalizedModel extends Mysql{
        private $intId,$strCode,$strService,$strType,$strPrice,$strDetails,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM services WHERE state != 0 AND type = 2 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function create(string $code,string $service,int $type,string $price,string $details,string $datetime,int $state){
            $this->strCode = $code;
            $this->strService = $service;
            $this->strType = $type;
            $this->strPrice = $price;
            $this->strDetails = $details;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM services WHERE service = '{$this->strService}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO services(internal_code,service,type,price,details,registration_date,state) VALUES(?,?,?,?,?,?,?)";
                $data = array($this->strCode,$this->strService,$this->strType,$this->strPrice,$this->strDetails,$this->strDatetime,$this->intState);
                $insert = $this->insert($query,$data);
                if($insert){
                    $answer = 'success';
                }else{
                    $answer = 'error';
                }
            }else{
                $answer = "exists";
            }
            return $answer;
        }
        public function modify(int $id,string $service,string $price,string $details,int $state){
            $this->intId = $id;
            $this->strService = $service;
            $this->strPrice = $price;
            $this->strDetails = $details;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM services WHERE service = '$this->strService' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE services SET service=?,price=?,details=?,state=? WHERE id = $this->intId";
                $data = array($this->strService,$this->strPrice,$this->strDetails,$this->intState);
                $update = $this->update($query,$data);
                if($update){
                    $answer = 'success';
                }else{
                    $answer = 'error';
                }
            }else{
                $answer = "exists";
            }
            return $answer;
        }
        public function select_record(int $id){
            $this->intId = $id;
            $sql = "SELECT *FROM services WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function list_personalized(){
            $sql = "SELECT *FROM services WHERE state != 0 AND type = 2";
            $request = $this->select_all($sql);
            return $request;
        }
        public function clients(int $id){
            $this->intId = $id;
            $sql = "SELECT COUNT(*) AS total FROM detail_contracts WHERE serviceid = $this->intId";
            $answer = $this->select($sql);
            $total = $answer['total'];
            return $total;
        }
        public function returnCode(){
            $sql = "SELECT COUNT(internal_code) AS code FROM services";
            $answer = $this->select($sql);
            $code = $answer['code'];
            return $code;
        }
        public function generateCode(){
            $sql = "SELECT MAX(internal_code) AS code FROM services";
            $answer = $this->select($sql);
            $code = $answer['code'];
            return $code;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM detail_contracts WHERE serviceid = $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM services WHERE id = $this->intId";
                $delete = $this->delete($sql);
                if($delete){
                    $answer = 'success';
                }else{
                    $answer = 'error';
                }
            }else{
                $answer = 'exists';
            }
            return $answer;
        }
    }
