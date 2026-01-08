<?php
    class Errors extends Controllers{
        public function __construct(){
            parent::__construct();
        }
        public function notFound(){
            $data['page_name'] = "404 Not Found";
            $data['business'] = business_session();
            $this->views->getView($this,"404",$data);
        }
    }
    $notFound = new Errors();
    $notFound ->notFound();
