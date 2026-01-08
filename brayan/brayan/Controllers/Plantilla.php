<?php
class Plantilla extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    }
    consent_permission(RUNWAY);
  }

  public function plantilla()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Plantillas";
    $data['page_title'] = "Gestión de Plantillas";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Ajustes";
    $data['actual_page'] = "Zonas";
    $data['page_functions_js'] = "zonas.js";
    $this->views->getView($this, "runway2", $data);
  }
}
