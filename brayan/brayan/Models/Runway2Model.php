<?php
    Class Runway2Model extends Mysql{
        private $intId,$strnombre_zona,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM zonas WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function list_runway2(){
			$sql = "SELECT * FROM zonas WHERE state != 0";
			$answer = $this->select_all($sql);
			return $answer;
		}
        public function create(string $nombre_zona,string $datetime,int $state){
            $this->strnombre_zona = $nombre_zona;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM zonas WHERE nombre_zona = '{$this->strnombre_zona}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO zonas(nombre_zona,registration_date,state) VALUES(?,?,?)";
                $data = array($this->strnombre_zona,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $nombre_zona,int $state){
            $this->intId = $id;
            $this->strnombre_zona = $nombre_zona;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM zonas WHERE nombre_zona = '$this->strnombre_zona' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE zonas SET nombre_zona=?,state=? WHERE id = $this->intId";
                $data = array($this->strnombre_zona,$this->intState);
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
            $sql = "SELECT *FROM zonas WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM zonas WHERE nombre_zona = '$this->strnombre_zona' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM zonas WHERE id = $this->intId";
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
