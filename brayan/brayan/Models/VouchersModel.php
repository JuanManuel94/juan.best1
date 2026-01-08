<?php
    Class VouchersModel extends Mysql{
        private $intId,$strVoucher,$strDatetime,$intState;
        private $intIdSerie,$strDate,$strSerie,$strFromc,$strUntil,$strAvailable;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM vouchers WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function create(string $voucher,string $datetime,int $state){
            $this->strVoucher = $voucher;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM vouchers WHERE voucher = '{$this->strVoucher}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO vouchers(voucher,registration_date,state) VALUES(?,?,?)";
                $data = array($this->strVoucher,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $voucher,int $state){
            $this->intId = $id;
            $this->strVoucher = $voucher;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM vouchers WHERE voucher = '$this->strVoucher' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE vouchers SET voucher=?,state=? WHERE id = $this->intId";
                $data = array($this->strVoucher,$this->intState);
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
            $sql = "SELECT *FROM vouchers WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function select_serie(int $id){
			$this->intIdSerie = $id;
			$sql = "SELECT vs.id,vs.date,vs.serie,vs.fromc,vs.until,vs.voucherid,v.voucher,vs.available
			FROM voucher_series vs
			JOIN vouchers v ON vs.voucherid = v.id
			WHERE vs.id = $this->intIdSerie";
			$asnwer = $this->select($sql);
			return $asnwer;
		}
        public function return_serie(int $id){
            $this->intId = $id;
            $sql = "SELECT MAX(available) AS total FROM voucher_series WHERE voucherid = $this->intId";
            $answer = $this->select($sql);
            $total = $answer['total'];
            return $total;
        }
        public function return_id(int $id){
            $this->intId = $id;
            $sql = "SELECT MAX(id) AS serie FROM voucher_series WHERE voucherid = $this->intId";
            $answer = $this->select($sql);
            $serie = $answer['serie'];
            return $serie;
        }
        public function list_series(int $id){
			$this->intId = $id;
            $sql = "SELECT * FROM voucher_series WHERE voucherid = $this->intId ORDER BY id DESC";
            $request = $this->select_all($sql);
            return $request;
        }
        public function serial_existence(int $id){
            $this->intId = $id;
            $sql = "SELECT COUNT(*) AS total FROM voucher_series WHERE voucherid = $this->intId";
            $request = $this->select($sql);
            $total = $request['total'];
            return $total;
        }
        public function return_series(int $id){
            $this->intId = $id;
            $sql = "SELECT MAX(available) AS available FROM voucher_series WHERE voucherid = $this->intId";
            $request = $this->select($sql);
            $available = $request['available'];
            return $available;
        }
        public function list_vouchers(){
          $sql = "SELECT *FROM vouchers WHERE state != 0 ORDER BY id ASC";
          $answer = $this->select_all($sql);
          return $answer;
        }
        public function series_vocuhers(int $id){
          $this->intId = $id;
          $sql = "SELECT * FROM voucher_series WHERE available >= 1 AND voucherid = $this->intId";
          $answer = $this->select_all($sql);
          return $answer;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM bills WHERE voucherid = $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM vouchers WHERE id = $this->intId";
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
        public function add_serie(string $date,string $serie,string $fromc,string $until,int $idvouchers,string $available){
            $this->strDate = $date;
            $this->strSerie = $serie;
            $this->strFromc = $fromc;
            $this->strUntil = $until;
            $this->intId = $idvouchers;
            $this->strAvailable = $available;
            $answer = "";
            $sql = "SELECT *FROM voucher_series WHERE serie = '{$this->strSerie}' ";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query  = "INSERT INTO voucher_series(date,serie,fromc,until,voucherid,available) VALUES(?,?,?,?,?,?)";
                $data = array($this->strDate,$this->strSerie,$this->strFromc,$this->strUntil,$this->intId,$this->strAvailable);
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
        public function edit_serie(int $id,string $date,string $serie,string $fromc,string $until,int $idvouchers,string $available){
            $this->intIdSerie = $id;
            $this->strDate = $date;
            $this->strSerie = $serie;
            $this->strFromc = $fromc;
            $this->strUntil = $until;
            $this->intId = $idvouchers;
            $this->strAvailable = $available;
            $answer = "";
            $sql = "SELECT * FROM voucher_series WHERE serie = '$this->strSerie' AND id != $this->intIdSerie";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE voucher_series SET date = ?,serie = ?,fromc = ?,until = ?,voucherid = ?,available = ?
                WHERE id = $this->intIdSerie ";
                $data = array($this->strDate,$this->strSerie,$this->strFromc,$this->strUntil,$this->intId,$this->strAvailable);
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
    }
