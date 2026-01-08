<?php
	class CategoriesModel extends Mysql{
		private $intId,$strCategory,$strDescription,$strDatetime,$intState;
		public function __construct(){
			parent::__construct();
		}
		public function list_records(){
			$sql = "SELECT *FROM product_category WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function list_categories(){
			$sql = "SELECT *FROM product_category WHERE state != 0 ORDER BY id ASC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function create(string $category,string $description,string $datetime,int $state){
			$this->strCategory = $category;
			$this->strDescription = $description;
			$this->strDatetime = $datetime;
			$this->intState = $state;
			$answer = "";
			$sql = "SELECT *FROM product_category WHERE category = '{$this->strCategory}'";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query = "INSERT INTO product_category(category,description,registration_date,state) VALUES(?,?,?,?)";
				$data = array($this->strCategory,$this->strDescription,$this->strDatetime,$this->intState);
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
		public function modify(int $id,string $category,string $description,int $state){
			$this->intId = $id;
			$this->strCategory = $category;
			$this->strDescription = $description;
			$this->intState = $state;
			$answer = "";
			$sql = "SELECT *FROM product_category WHERE category = '$this->strCategory' AND id != $this->intId";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query = "UPDATE product_category SET category = ?,description = ?,state = ? WHERE id = $this->intId";
				$data = array($this->strCategory,$this->strDescription,$this->intState);
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
			$sql = "SELECT *FROM product_category WHERE id = $this->intId";
			$asnwer = $this->select($sql);
			return $asnwer;
		}
		public function associates(int $id){
            $this->intId = $id;
			$sql = "SELECT COUNT(*) AS total FROM products WHERE categoryid = $this->intId";
			$answer = $this->select($sql);
            $total = $answer['total'];
			return $total;
        }
		public function remove(int $id){
			$this->intId = $id;
			$answer = "";
			$sql = "SELECT *FROM products WHERE categoryid  = $this->intId";
			$request = $this->select_all($sql);
			if(empty($request)){
				$sql = "DELETE FROM product_category WHERE id = $this->intId";
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
		public function import(string $category,string $description,string $datetime){
			$this->strCategory = $category;
			$this->strDescription = $description;
			$this->strDatetime = $datetime;
			$answer = 0;
			$sql = "SELECT *FROM product_category WHERE category = '{$this->strCategory}'";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query = "INSERT INTO product_category(category,description,registration_date) VALUES(?,?,?)";
				$data = array($this->strCategory,$this->strDescription,$this->strDatetime);
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
