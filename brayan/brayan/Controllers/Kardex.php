<?php
    class Kardex extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(PRODUCTS);
        }
        public function kardex(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Kardex Valorizado";
            $data['page_title'] = "Kardex Valorizado";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Inventario";
            $data['actual_page'] = "Kardex";
            $data['page_functions_js'] = "kardex.js";
            $this->views->getView($this,"kardex",$data);
        }
        public function detail(string $idproduct){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $idproduct = decrypt($idproduct);
            $idproduct = intval($idproduct);
            if($idproduct > 0){
                $detail = kardex_detail($idproduct); 
                $data['page_name'] = "Detalle de Kardex";
                $data['page_title'] = $detail['product']['product'];
                $data['home_page'] = "Dashboard";
                $data['previous_page'] = "Kadex";
                $data['actual_page'] = "Detalle de Kardex";
                if(empty($detail['detail'])){
                    header("Location:".base_url().'/kardex');
                }else{
                    $data['kardex_detail'] = $detail;
                    $this->views->getView($this,"detail",$data);
                }
            }else{
                header("Location:".base_url().'/kardex');
            }
            die();
        }
        public function list_records(){
            if($_SESSION['permits_module']['v']){
                $data = $this->model->list_records();
                for($i=0; $i < count($data); $i++){
                    $view = '';
                    $data[$i]['encrypt'] = encrypt($data[$i]['id']);
                    $data[$i]['number_income'] = $data[$i]['number_income'];
                    $data[$i]['cost_incomes'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['cost_incomes']);
                    $data[$i]['total_income'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['total_income']);

                    $data[$i]['number_departure'] = $data[$i]['number_departure'];
                    $data[$i]['cost_departures'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['cost_departures']);
                    $data[$i]['total_departure'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['total_departure']);

                    $data[$i]['balance_amount'] = $data[$i]['balance_amount'];
                    $data[$i]['cost_balance'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['cost_balance']);
                    $data[$i]['total_balance'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['total_balance']);
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }
