<?php

class Apreceptor extends Controllers {
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

  public function apreceptor() {
    if(empty($_SESSION['permits_module']['v'])){
      header("Location:".base_url().'/dashboard');
    }
    $data['columns'] = $this->model->listMetaData();
    $data['page_name'] = "STA Receptor";
    $data['page_title'] = "STA Receptor";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "STA Receptor";
    $data['page_functions_js'] = "ap_receptor.js";
    $this->views->getView($this, "apreceptor", $data);
  }

  public function list_records() {
    if (!$_SESSION['permits_module']['v']) return;
    $isEdit = $_SESSION['permits_module']['a'];
    $isRemove = $_SESSION['permits_module']['e'];
    try {
      $data = $this->model->listRecords($_GET);
    } catch (\Throwable $th) {
      $this->json($th->getMessage());
    }
    foreach ($data as $key => $item) {
      $item["n"] = $key + 1;
      $item["isEdit"] = $isEdit;
      $item["isRemove"] = $isRemove;
      $data[$key] = $item;
    }
    $this->json($data);
  }

  public function save() {
    try {
      $this->model->create($_POST);
      echo json_encode([
        "status" => true,
        "message" => "STA Receptor creado!!!"
      ]);
    } catch (\Throwable $th) {
      echo json_encode([
        "status" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

  public function select_record(string $id) {
    if (!$_SESSION['permits_module']['v']) return;
    $data = $this->model->select_record($id);
    $this->json($data);
  }

  public function update_record(string $id) {
    if (!$_SESSION['permits_module']['v']) return;
    try {
      $data = $this->model->update_record($id, $_POST);
      $this->json([
        "status" => "success", 
        "msg" => "Los datos se actualizaron correctamente!",
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "status" => "error", 
        "msg" => "No se pudo actualizar los datos",
        "error" => $th->getMessage()
      ]);
    }
  }

  public function remove_record(string $id) {
    try {
      if (!$_SESSION['permits_module']['e']) throw new Exception("Forbbien");
      $result = $this->model->remove_record($id);
      if (!$result) throw new Exception("No se pudo eliminar");
      $this->json([
        "status" => true,
        "message" => "Registro eliminado correctamente!"
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "status" => false,
        "message" => $th->getMessage()
      ]);
    }
  }
}