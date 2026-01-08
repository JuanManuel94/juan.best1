<?php

class CajaNap extends Controllers
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

  public function cajaNap()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Mufa y Caja Nap";
    $data['page_title'] = "Mufa y Caja Nap";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "Mufa y Caja Nap";
    $data['page_functions_js'] = "cajanap.js";
    $data['zonas'] = $this->model->createQueryBuilder()->from("zonas")->getMany();
    $this->views->getView($this, "cajanap", $data);
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
      $payload = $_POST;

      if ($payload['tipo'] == 'mufa') {
        $payload['puertos'] = 0;
      } else if ($payload['tipo'] == 'nap' && empty($payload['puertos'])) {
        $payload['puertps'] = 1;
      }

      $this->model->create($payload);

      $this->json([
        "status" => true,
        "message" => "Nap creado!!!"
      ]);
    } catch (\Throwable $th) {
      $this->json([
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

      if ($payload['tipo'] == "mufa") {
        $payload['puertos'] = 0;
      }

      $this->model->update_record($id, $payload);
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
  public function view_map()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Mapa de Caja Nap";
    $data['page_title'] = "Mapa de Caja Nap";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "Mapa de Caja Nap";
    $data['page_functions_js'] = "cajanap_map.js";
    $this->views->getView($this, "cajanap_map", $data);
  }

  public function view_location(string $id)
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $nap = $this->model->select_record($id);
    $data["nap"] = $nap;
    $data['page_name'] = "Mapa de Caja Nap";
    $data['page_title'] = "Mapa de Caja Nap";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "Mapa de Caja Nap";
    $data['page_functions_js'] = "cajanap_locacion.js";
    $this->views->getView($this, "cajanap_locacion", $data);
  }

  public function search_puertos()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data = $this->model->search_puertos($_GET);
    $this->json($data);
  }
}
