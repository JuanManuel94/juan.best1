<?php

class Campos extends Controllers {
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

  public function campos() {
    if(empty($_SESSION['permits_module']['v'])){
      header("Location:".base_url().'/dashboard');
    }
    $data['page_name'] = "Campos Personalizados";
    $data['page_title'] = "Campos Personalizados";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "Campos Personalizados";
    $data['page_functions_js'] = "campos.js";
    $this->views->getView($this, "campos", $data);
  }

  public function list_records() {
    if (!$_SESSION['permits_module']['v']) return;
    $isEdit = $_SESSION['permits_module']['a'];
    $isRemove = $_SESSION['permits_module']['e'];
    try {
      $data = $this->model->listRecords($_GET);
      foreach ($data as $key => $item) {
        $item["n"] = $key + 1;
        $item["isEdit"] = $isEdit;
        $item["isRemove"] = $isRemove;
        $data[$key] = $item;
      }
      $this->json($data);
    } catch (\Throwable $th) {
      $this->json($th->getMessage());
    }
  }

  public function save() {
    try {
      $this->model->create($_POST);
      $this->json([
        "status" => true,
        "message" => "Campo creado!!!"
      ]);
    } catch (\Throwable $th) {
      $this->json([
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
      $payload = [
        "nombre" => $_POST['nombre'],
        "campo" => $_POST['campo'],
        "tipo" => $_POST['tipo'],
        "obligatorio" => $_POST['obligatorio'],
        "tablaId" => $_POST['tablaId']
      ];
      $data = $this->model->update_record($id, $payload);
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

  public function list_tablas() {
    $data = $this->model->list_tablas();
    $this->json($data);
  }

  public function select_tabla(string $id) {
    try {
      $data = $this->model->select_tabla($id);
      $this->json($data);
    } catch (\Throwable $th) {
      $this->json($th->getMessage()); 
    }
  }

  public function list_campos(string $id) {
    $data = $this->model->list_campos($id);
    $this->json($data);
  }
}