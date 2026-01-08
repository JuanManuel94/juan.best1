<?php
	class ProvidersModel extends Mysql{
		private $intId,$strRazon,$intType,$strDocument,$strMobile,$strEmail,$strAddress,$strDatetime,$intState;
		public function __construct(){
			parent::__construct();
		}
		public function list_records(){
			$sql = "SELECT p.id,p.provider,d.document AS name_doc,p.document,p.mobile,p.email,p.address,p.registration_date,p.state
			FROM providers p
			JOIN document_type d ON p.documentid = d.id
			WHERE p.state != 0 ORDER BY p.id DESC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function export(){
			$sql = "SELECT p.id,p.provider,d.document AS name_doc,p.document,p.mobile,p.email,p.address,p.registration_date,p.state
			FROM providers p
			JOIN document_type d ON p.documentid = d.id
			WHERE p.state != 0 ORDER BY p.id ASC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function list_providers(){
			$sql = "SELECT *FROM providers WHERE state != 0 ORDER BY id ASC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function create(string $razon,int $type,string $document,string $mobile,string $email,string $address,string $datetime,int $state){
            $this->strRazon = $razon;
            $this->intType = $type;
            $this->strDocument = $document;
            $this->strMobile = $mobile;
            $this->strEmail = $email;
            $this->strAddress = $address;
						$this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT * FROM providers WHERE document = '{$this->strDocument}'";
			$request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO providers(provider,documentid,document,mobile,email,address,registration_date,state) VALUES(?,?,?,?,?,?,?,?)";
				$data = array($this->strRazon,$this->intType,$this->strDocument,$this->strMobile,$this->strEmail,$this->strAddress,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $razon,int $type,string $document,string $mobile,string $email,string $address,int $state){
            $this->intId = $id;
			$this->strRazon = $razon;
            $this->intType = $type;
            $this->strDocument = $document;
            $this->strMobile = $mobile;
            $this->strEmail = $email;
            $this->strAddress = $address;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM providers WHERE document = '{$this->strDocument}' AND id != $this->intId";
			$request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE providers SET provider=?,documentid=?,document=?,mobile=?,email=?,address=?,state=? WHERE id = $this->intId";
                $data = array($this->strRazon,$this->intType,$this->strDocument,$this->strMobile,$this->strEmail,$this->strAddress,$this->intState);
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
		public function list_documents(){
			$sql = "SELECT *FROM document_type";
                   //$sql = "SELECT *FROM document_type WHERE id IN(2,3)";
			$request = $this->select_all($sql);
			return $request;
		}
		public function associates(int $id){
			$this->intId = $id;
			$sql = "SELECT COUNT(*) AS total FROM products WHERE providerid = $this->intId";
			$answer = $this->select($sql);
			$total = $answer['total'];
			return $total;
		}
		public function select_record(int $id){
			$this->intId = $id;
			$sql = "SELECT *FROM providers WHERE id = $this->intId";
			$asnwer = $this->select($sql);
			return $asnwer;
		}
		public function remove(int $id){
			$this->intId = $id;
			$answer = "";
			$sql = "SELECT *FROM products WHERE providerid = $this->intId";
			$request = $this->select_all($sql);
			if(empty($request)){
				$sql = "DELETE FROM providers WHERE id = $this->intId";
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
		public function import(string $razon,string $type,string $document,string $mobile,string $email,string $address,string $datetime){
						$this->strRazon = $razon;
						$this->intType = $type;
						$this->strDocument = $document;
						$this->strMobile = $mobile;
						$this->strEmail = $email;
						$this->strAddress = $address;
						$this->strDatetime = $datetime;
						$answer = 0;
						$sql = "SELECT * FROM providers WHERE document = '{$this->strDocument}'";
			$request = $this->select_all($sql);
						if(empty($request)){
								$query = "INSERT INTO providers(provider,documentid,document,mobile,email,address,registration_date) VALUES(?,?,?,?,?,?,?)";
				$data = array($this->strRazon,$this->intType,$this->strDocument,$this->strMobile,$this->strEmail,$this->strAddress,$this->strDatetime);
				$insert = $this->insert($query,$data);
				if($insert){
					$answer = $insert;
				}else{
					$answer = 0;
				}
						}else{
				$answer = 0;
			}
						return $answer;
				}
    }
