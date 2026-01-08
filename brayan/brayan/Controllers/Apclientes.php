<?php

class Apclientes extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION["login"])) {
      header('Location: ' . base_url() . '/login');
      die();
    } else {
      consent_permission(RED);
    }
  }

  public function apclientes()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['columns'] = $this->model->listMetaData();
    $data['page_name'] = "AP Clientes";
    $data['page_title'] = "AP Clientes";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "AP Clientes";
    $data['page_functions_js'] = "ap_clientes.js";
    $this->views->getView($this, "apclientes", $data);
  }

  public function list_records()
  {
    if (!$_SESSION['permits_module']['v'])
      return;
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

  public function save()
  {
    try {
      $this->model->create($_POST);
      echo json_encode([
        "status" => true,
        "message" => "AP Cliente creado!!!"
      ]);
    } catch (\Throwable $th) {
      echo json_encode([
        "status" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

  public function select_record(string $id)
  {
    if (!$_SESSION['permits_module']['v'])
      return;
    $data = $this->model->select_record($id);
    $this->json($data);
  }

  public function update_record(string $id)
  {
    if (!$_SESSION['permits_module']['v'])
      return;
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

  public function remove_record(string $id)
  {
    try {
      if (!$_SESSION['permits_module']['e'])
        throw new Exception("Forbbien");
      $result = $this->model->remove_record($id);
      if (!$result)
        throw new Exception("No se pudo eliminar");
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

  public function list_users(string $id)
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['apId'] = $id;
    $data['page_name'] = "Clientes";
    $data['page_title'] = "AP Clientes";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "AP Clientes";
    $data['page_functions_js'] = "ap_cliente_customer.js";
    $this->views->getView($this, "apclientes_customer", $data);
  }

  public function list_users_record(string $id)
  {
    $data = $this->model->list_users_record($id);
    foreach ($data as $key => $item) {
      $item['encrypt_client'] = encrypt($item['clientid']);
      $data[$key] = $item;
    }
    $this->json($data);
  }
}