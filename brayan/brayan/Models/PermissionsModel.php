<?php
	class PermissionsModel extends Mysql{
		public $intId,$intProfile,$intModule,$intR,$intA,$intE,$intV;
		public function __construct(){
			parent::__construct();
		}
        public function module_permissions(int $profile){
            $this->intProfile = $profile;
            $sql = "SELECT p.profileid,p.moduleid,m.module,p.r,p.a,p.e,p.v
            FROM permits p
            INNER JOIN modules m ON p.moduleid = m.id
            WHERE p.profileid = $this->intProfile";
            $request = $this->select_all($sql);
            $arrPermits = array();
            for($i=0; $i < count($request); $i++){
                $arrPermits[$request[$i]['moduleid']] = $request[$i];
            }
            return $arrPermits;
        }
		public function assign_permissions(int $perfil,int $module,int $r,int $a,int $e,int $v){
			$this->intProfile = $perfil;
			$this->intModule = $module;
			$this->intR = $r;
			$this->intA = $a;
			$this->intE = $e;
			$this->intV = $v;
			$answer = "";
			$query_insert = "INSERT INTO permits(profileid,moduleid,r,a,e,v) VALUES (?,?,?,?,?,?)";
			$arrData = array($this->intProfile,$this->intModule,$this->intR,$this->intA,$this->intE,$this->intV);
        	$insert = $this->insert($query_insert,$arrData);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
	        return $answer; 
		}
		public function remove(int $perfil){
			$this->intProfile = $perfil;
			$sql = "DELETE FROM permits WHERE profileid = $this->intProfile";
			$answer = $this->delete($sql);
			return $answer;
		}
	}
