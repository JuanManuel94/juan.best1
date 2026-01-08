<?php

class OtrosPagos extends Controllers
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

  public function otrosPagos()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['columns'] = $this->model->listMetaData();
    $data['page_name'] = "Otros Ingresos & Egresos";
    $data['page_title'] = "Otros Ingresos & Egresos";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Finanzas";
    $data['actual_page'] = "Otros Ingresos & Egresos";
    $data['page_functions_js'] = "otros_pagos.js";
    $this->views->getView($this, "otrosPagos", $data);
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

  public function resumen()
  {
    $this->json([
      "ingreso" => $this->model->resumeIngresos(),
      "egreso" => $this->model->resumeEgresos()
    ]);
  }

  public function save()
  {
    try {
      $data = $_POST;
      $data['userId'] = $_SESSION['userData']['id'];
      $this->model->create($data);
      echo json_encode([
        "status" => true,
        "message" => "Registro creado!!!"
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
      $payload = $_POST;
      $payload['userId'] = $_SESSION['userData']['id'];
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
}