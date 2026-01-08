<?php
    Class UsersModel extends Mysql{
        private $intId,$strNames,$strSurnames,$intType,$strDocument,$strMobile,$strEmail;
        private $intProfile,$strUsername,$strPassword,$strImage,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
            $where = ($_SESSION['idUser']!=1) ? "AND u.id != 1 ORDER BY u.id DESC" : "ORDER BY u.id DESC";
			$sql = "SELECT u.id,u.names,u.surnames,d.document AS name_doc,u.document,u.mobile,u.email,u.profileid,
			p.profile,u.username,u.password,u.image,u.registration_date,u.state
			FROM users u
            JOIN document_type d ON u.documentid = d.id
			JOIN profiles p ON u.profileid = p.id
			WHERE u.state != 0 ".$where;
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function create(string $names,string $surnames,int $type,string $document,string $mobile,string $email,int $profile,string $username,string $password,string $image,string $datetime,int $state){
            $this->strNames = $names;
            $this->strSurnames = $surnames;
            $this->intType = $type;
            $this->strDocument = $document;
            $this->strMobile = $mobile;
            $this->strEmail = $email;
            $this->intProfile = $profile;
            $this->strUsername = $username;
            $this->strPassword = $password;
            $this->strImage = $image;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT * FROM users WHERE document = '{$this->strDocument}'";
			$request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO users(names,surnames,documentid,document,mobile,email,profileid, username,password,image,registration_date,state) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
				$data = array($this->strNames,$this->strSurnames,$this->intType,$this->strDocument,$this->strMobile,$this->strEmail,$this->intProfile,$this->strUsername,$this->strPassword,$this->strImage,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $names,string $surnames,int $type,string $document,string $mobile,string $email,int $profile,string $username,string $password,int $state){
            $this->intId = $id;
            $this->strNames = $names;
            $this->strSurnames = $surnames;
            $this->intType = $type;
            $this->strDocument = $document;
            $this->strMobile = $mobile;
            $this->strEmail = $email;
            $this->intProfile = $profile;
            $this->strUsername = $username;
            $this->strPassword = $password;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM users WHERE document = '{$this->strDocument}' AND id != $this->intId";
			$request = $this->select_all($sql);
            if(empty($request)){
                if($this->strPassword != ""){
                    $query = "UPDATE users SET names=?,surnames=?,documentid=?,document=?,mobile=?,email=?,profileid=?,username=?,password=?,state=? WHERE id = $this->intId";
                    $data = array($this->strNames,$this->strSurnames,$this->intType,$this->strDocument,$this->strMobile,$this->strEmail,$this->intProfile,$this->strUsername,$this->strPassword,$this->intState);
                }else{
                    $query = "UPDATE users SET names=?,surnames=?,documentid=?,document=?,mobile=?,email=?,profileid=?,username=?,state=? WHERE id = $this->intId";
                    $data = array($this->strNames,$this->strSurnames,$this->intType,$this->strDocument,$this->strMobile,$this->strEmail,$this->intProfile,$this->strUsername,$this->intState);
                }
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
        public function modify_data(int $id,string $names,string $surnames,string $mobile,string $email){
            $this->intId = $id;
            $this->strNames = $names;
            $this->strSurnames = $surnames;
            $this->strMobile = $mobile;
            $this->strEmail = $email;
            $answer = "";
            $query = "UPDATE users SET names=?,surnames=?,mobile=?,email=? WHERE id = $this->intId";
            $data = array($this->strNames,$this->strSurnames,$this->strMobile,$this->strEmail);
            $update = $this->update($query,$data);
            if($update){
                $answer = 'success';
            }else{
                $answer = 'error';
            }
            return $answer;
        }
        public function modify_password(int $id,string $password){
            $this->intId = $id;
            $this->strPassword = $password;
            $answer = "";
            $query = "UPDATE users SET password=? WHERE id = $this->intId";
            $data = array($this->strPassword);
            $update = $this->update($query,$data);
            if($update){
                $answer = 'success';
            }else{
                $answer = 'error';
            }
            return $answer;
        }
        public function select_record(int $id){
            $this->intId = $id;
            $sql = "SELECT * FROM users WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function list_documents(){
			$sql = "SELECT *FROM document_type WHERE id IN(2,4,5)";
			$request = $this->select_all($sql);
			return $request;
        }

        public function list_users(){
            $sql = "SELECT *FROM users WHERE state != 0";
            $request = $this->select_all($sql);
            return $request;
        }
        public function remove(int $id){
			$this->intId = $id;
            $answer = "";
			$sql = "DELETE FROM users WHERE id = $this->intId";
			$delete = $this->delete($sql);
			if($delete){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
    public function change_profile(int $id,string $image){
      $this->intId = $id;
      $this->strImage = $image;
      $answer = "";
      $query = "UPDATE users SET image = ? WHERE id = $this->intId";
      $data = array($this->strImage);
      $request = $this->update($query,$data);
      if($request){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
    }
    }
