<?php
    Class ProfilesModel extends Mysql{
        private $intId,$strProfile,$strDescription,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM profiles WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function create(string $profile,string $description,string $datetime,int $state){
            $this->strProfile = $profile;
            $this->strDescription = $description;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM profiles WHERE profile = '{$this->strProfile}'";
			$request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO profiles(profile,description,registration_date,state) VALUES(?,?,?,?)";
                $data = array($this->strProfile,$this->strDescription,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $profile,string $description,int $state){
            $this->intId = $id;
            $this->strProfile = $profile;
            $this->strDescription = $description;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM profiles WHERE profile = '$this->strProfile' AND id != $this->intId";
			$request = $this->select_all($sql);
			if(empty($request)){
                $query = "UPDATE profiles SET profile=?,description=?,state=? WHERE id = $this->intId";
                $data = array($this->strProfile,$this->strDescription,$this->intState);
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
            $sql = "SELECT *FROM profiles WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function list_profiles(){
            $where = ($_SESSION['idUser']!=1) ? "AND id != 1" : "";
      			$sql = "SELECT *FROM profiles WHERE state != 0 ".$where;
      			$request = $this->select_all($sql);
      			return $request;
        }
        public function associates(int $id){
            $this->intId = $id;
			$sql = "SELECT COUNT(*) AS total FROM users WHERE profileid = $this->intId";
			$answer = $this->select($sql);
            $total = $answer['total'];
			return $total;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
    		$sql = "SELECT *FROM users WHERE profileid = $this->intId";
    		$request = $this->select_all($sql);
    		if(empty($request)){
    			$sql_permits = "DELETE FROM permits WHERE profileid  = $this->intId";
    			$delete_permits = $this->delete($sql_permits);
    			$sql = "DELETE FROM profiles WHERE id = $this->intId";
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
        public function list_modules(){
            $sql = "SELECT * FROM modules WHERE state != 0";
            $request = $this->select_all($sql);
            return $request;
        }
        public function select_permissions(int $id){
            $this->intId = $id;
            $sql = "SELECT * FROM permits WHERE profileid = $this->intId";
            $request = $this->select_all($sql);
            return $request;
        }
    }
