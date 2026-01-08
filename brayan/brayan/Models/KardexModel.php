<?php
	class KardexModel extends Mysql{
		private $intId;
		public function __construct(){
			parent::__construct();
		}
		public function list_records(){
			$sql = "SELECT id
				, description
				, number_income
			    , cost_incomes
			    , (number_income * cost_incomes) AS total_income
			    , number_departure
			    , cost_departures
			    , (number_departure * cost_departures) AS total_departure
			    , (number_income - number_departure) AS balance_amount
			    , (cost_incomes - cost_departures) AS cost_balance
				, (number_income * cost_incomes) - (number_departure * cost_departures) AS total_balance
			FROM (SELECT p.id AS id,p.product AS description
				, (SELECT COALESCE(SUM(qi.quantity_income),0) FROM income qi WHERE p.id=qi.productid) AS number_income
			    , (SELECT COALESCE(SUM(ci.unit_price),0) FROM income ci WHERE p.id=ci.productid) AS cost_incomes
				, (SELECT COALESCE(SUM(qd.quantity_departures),0) FROM departures qd WHERE p.id=qd.productid) AS number_departure
			    , (SELECT COALESCE(SUM(cd.unit_price),0) FROM departures cd WHERE p.id=cd.productid) AS cost_departures 
			FROM products p) tp";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function returnIncome(int $id){
			$this->intId = $id;
			$sql = "SELECT SUM(quantity_income) AS input_amount,SUM(total_cost) AS total_input FROM income WHERE productid = $this->intId";
			$answer = $this->select($sql);
			return $answer;
		}
		public function returnDeparture(int $id){
			$this->intId = $id;
			$sql = "SELECT SUM(quantity_departures) AS output_quantity,SUM(total_cost) AS total_output FROM departures WHERE productid = $this->intId";
			$answer = $this->select($sql);
			return $answer;
		}
		public function returnCurrent(int $id){
			$this->intId = $id;
			$sql = "SELECT MIN(quantity_income) AS initial_inventory FROM income WHERE productid = $this->intId";
			$answer = $this->select($sql);
			$total = $answer['initial_inventory'];
			return $total;
		}
    }
