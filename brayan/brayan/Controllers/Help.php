<?php
  class Help extends Controllers{
    public function __construct(){
      parent::__construct();
			session_start();
    	if(empty($_SESSION['login'])){
    		header('Location: '.base_url().'/login');
    		die();
    	}
    }
    public function help(){
      $data['page_name'] = "Acerca de";
      $data['page_title'] = "Soporte técnico";
      $data['home_page'] = "Dashboard";
      $data['actual_page'] = "Acerca de";
      $this->views->getView($this,"help",$data);
    }
  }
