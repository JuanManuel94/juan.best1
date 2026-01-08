<?php
    Class RunwayModel extends Mysql{
        private $intId,$strType,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM forms_payment WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function list_runway(){
			$sql = "SELECT * FROM forms_payment WHERE state != 0";
			$answer = $this->select_all($sql);
			return $answer;
		}
        public function create(string $type,string $datetime,int $state){
            $this->strType = $type;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM forms_payment WHERE payment_type = '{$this->strType}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO forms_payment(payment_type,registration_date,state) VALUES(?,?,?)";
                $data = array($this->strType,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $type,int $state){
            $this->intId = $id;
            $this->strType = $type;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM forms_payment WHERE payment_type = '$this->strType' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE forms_payment SET payment_type=?,state=? WHERE id = $this->intId";
                $data = array($this->strType,$this->intState);
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
            $sql = "SELECT *FROM forms_payment WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM payments WHERE paytypeid = $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM forms_payment WHERE id = $this->intId";
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
