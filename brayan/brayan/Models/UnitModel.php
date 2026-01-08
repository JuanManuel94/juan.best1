<?php
    Class UnitModel extends Mysql{
        private $intId,$strCode,$strUnited,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM unit WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function list_units(){
            $sql = "SELECT *FROM unit WHERE state != 0 ORDER BY id ASC";
            $answer = $this->select_all($sql);
            return $answer;
        }
        public function create(string $code,string $united,string $datetime,int $state){
            $this->strCode = $code;
            $this->strUnited = $united;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM unit WHERE united = '{$this->strUnited}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO unit(code,united,registration_date,state) VALUES(?,?,?,?)";
                $data = array($this->strCode,$this->strUnited,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $code,string $united,int $state){
            $this->intId = $id;
            $this->strCode = $code;
            $this->strUnited = $united;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM unit WHERE united = '$this->strUnited' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE unit SET code = ?,united = ?,state = ? WHERE id = $this->intId";
                $data = array($this->strCode,$this->strUnited,$this->intState);
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
            $sql = "SELECT *FROM unit WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM products WHERE unitid = $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM unit WHERE id = $this->intId";
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
