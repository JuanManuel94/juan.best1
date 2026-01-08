<?php
	class Logout{
		public function __construct(){
			session_start();
			session_unset();
			session_destroy();
			clearCookie();
			header('location: '.base_url().'/login');
			die();
		}
	}
