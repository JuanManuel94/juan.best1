<?php

class Plans extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        consent_permission(SERVICES);
    }
    public function internet()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Perfiles de Internet";
        $data['page_title'] = "Servicios de Internet";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Planes";
        $data['actual_page'] = "Internet";
        $data['page_functions_js'] = "internet.js";

        //GET ROUTERS

        $result = sql("SELECT * FROM network_routers");

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            $data['routers'][] = $row;
        }

        $this->views->getView($this, "internet", $data);
    }
    public function personalized()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Otros servicios";
        $data['page_title'] = "Servicios Personalizados";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Planes";
        $data['actual_page'] = "Personalizado";
        $data['page_functions_js'] = "personalized.js";
        $this->views->getView($this, "personalized", $data);
    }
}
