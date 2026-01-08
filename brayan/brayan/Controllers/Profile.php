<?php
    class Profile extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
        }
        public function profile(){
            $data['page_name'] = "Mi perfil";
            $data['page_title'] = "Configuración de la cuenta";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Perfil";
            $data['actual_page'] = ucwords(strtolower($_SESSION['userData']['names']." ".$_SESSION['userData']['surnames']));
            $data['page_functions_js'] = "profile.js";
            $this->views->getView($this,"profile",$data);
        }
      }
