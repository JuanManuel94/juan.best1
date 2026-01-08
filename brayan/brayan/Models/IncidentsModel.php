<?php
    Class IncidentsModel extends Mysql{
        private $intId,$strIncident,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
    			$sql = "SELECT *FROM incidents WHERE state != 0 ORDER BY id DESC";
    			$answer = $this->select_all($sql);
    			return $answer;
        }
        public function list_incidents(){
            $sql = "SELECT *FROM incidents WHERE state != 0";
            $answer = $this->select_all($sql);
            return $answer;
        }
        public function create(string $incident,string $datetime,int $state){
            $this->strIncident = $incident;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM incidents WHERE incident = '{$this->strIncident}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO incidents(incident,registration_date,state) VALUES(?,?,?)";
                $data = array($this->strIncident,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $incident,int $state){
            $this->intId = $id;
            $this->strIncident = $incident;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM incidents WHERE incident = '$this->strIncident' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE incidents SET incident = ?,state = ? WHERE id = $this->intId";
                $data = array($this->strIncident,$this->intState);
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
            $sql = "SELECT *FROM incidents WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM tickets WHERE incidentsid = $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM incidents WHERE id = $this->intId";
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
