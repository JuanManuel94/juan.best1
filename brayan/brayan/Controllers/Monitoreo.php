<?php

class Monitoreo extends Controllers {
  public function __construct() {
    parent::__construct();
    session_start();
    if (empty($_SESSION["login"])) {
      header('Location: '.base_url().'/login');
			die();
    } else {
      consent_permission(RED);
    }
  }

  public function monitoreo() {
    if(empty($_SESSION['permits_module']['v'])){
      header("Location:".base_url().'/dashboard');
    }
    $data['page_name'] = "Monitoreo";
    $data['page_title'] = "Monitoreo";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "Monitoreo";
    $data['page_functions_js'] = "monitoreo.js";
    $this->views->getView($this, "monitoreo", $data);
  }

  public function list_records() {
    if (!$_SESSION['permits_module']['v']) return;
    $isEdit = $_SESSION['permits_module']['a'];
    $isRemove = $_SESSION['permits_module']['e'];
    try {
      $data = $this->model->listRecords($_GET);
      $this->json($data);
    } catch (\Throwable $th) {
      $this->json($th->getMessage());
    }
  }

  public function select_record(string $id) {
    if (!$_SESSION['permits_module']['v']) return;
    $data = $this->model->select_record($id);
    $this->json($data);
  }
}