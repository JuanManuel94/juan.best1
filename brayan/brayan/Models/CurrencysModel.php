<?php
    Class CurrencysModel extends Mysql{
        private $intId,$strIso,$strLanguage,$strCurrency,$strMoney,$strMoneyPlural,$strSimbol,$strDatetime,$intState;
        public function __construct(){
			parent::__construct();
		}
        public function list_records(){
			$sql = "SELECT *FROM currency WHERE state != 0 ORDER BY id DESC";
			$answer = $this->select_all($sql);
			return $answer;
        }
        public function create(string $iso,string $language,string $currency,string $money,string $money_plural,string $symbol,string $datetime,int $state){
            $this->strIso = $iso;
            $this->strLanguage = $language;
            $this->strCurrency = $currency;
            $this->strMoney = $money;
            $this->strMoneyPlural = $money_plural;
            $this->strSimbol = $symbol;
            $this->strDatetime = $datetime;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM currency WHERE currency_name = '{$this->strCurrency}' AND money = '{$this->strMoney}' AND money_plural = '{$this->strMoneyPlural}'";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "INSERT INTO currency(currency_iso,language,currency_name,money,money_plural,symbol,registration_date,state) VALUES(?,?,?,?,?,?,?,?)";
                $data = array($this->strIso,$this->strLanguage,$this->strCurrency,$this->strMoney,$this->strMoneyPlural,$this->strSimbol,$this->strDatetime,$this->intState);
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
        public function modify(int $id,string $iso,string $language,string $currency,string $money,string $money_plural,string $symbol,int $state){
            $this->intId = $id;
            $this->strIso = $iso;
            $this->strLanguage = $language;
            $this->strCurrency = $currency;
            $this->strMoney = $money;
            $this->strMoneyPlural = $money_plural;
            $this->strSimbol = $symbol;
            $this->intState = $state;
            $answer = "";
            $sql = "SELECT *FROM currency WHERE currency_name = '$this->strCurrency' AND money = '{$this->strMoney}' AND money_plural = '{$this->strMoneyPlural}' AND id != $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $query = "UPDATE currency SET currency_iso = ?,language = ?,currency_name = ?,money = ?,money_plural = ?,symbol = ?,state=? WHERE id = $this->intId";
                $data = array($this->strIso,$this->strLanguage,$this->strCurrency,$this->strMoney,$this->strMoneyPlural,$this->strSimbol,$this->intState);
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
            $sql = "SELECT *FROM currency WHERE id = $this->intId";
            $asnwer = $this->select($sql);
            return $asnwer;
        }
        public function remove(int $id){
            $this->intId = $id;
            $answer = "";
            $sql = "SELECT *FROM business WHERE currencyid = $this->intId";
            $request = $this->select_all($sql);
            if(empty($request)){
                $sql = "DELETE FROM currency WHERE id = $this->intId";
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
