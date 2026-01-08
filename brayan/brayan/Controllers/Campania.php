<?php

class Campania extends Controllers
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

  public function whatsapp()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "WS Campaña";
    $data['page_title'] = "WS Campaña";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Campaña";
    $data['actual_page'] = "WS Campaña";
    $data['page_functions_js'] = "campania_whatsapp.js";
    $this->views->getView($this, "whatsapp", $data);
  }

  public function plantilla()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Plantilla WSP";
    $data['page_title'] = "Plantilla WSP";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Campaña";
    $data['actual_page'] = "Plantilla WSP";
    $data['page_functions_js'] = "campania_plantilla.js";

    $data['messages'] = $this->model->list_business_wsp();
    $this->views->getView($this, "plantilla", $data);
  }

  public function list_users_record()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $country_code = $_SESSION['businessData']['country_code'];
    $data = $this->model->list_users_record($_GET);
    foreach ($data as $key => $item) {
      $item['country_code'] = $country_code;
      $data[$key] = $item;
    }
    return $this->json($data);
  }

  public function find_business_wsp(string $id)
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json");

    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }

    $payload = json_decode(file_get_contents("php://input"), true);
    $service = new PlanillaWspSaveService();
    $response = $service->execute($id, $payload);

    if ($response) {
      return $this->json([
        "status" => "success",
        "message" => "Los datos se guardaron correctamente!!!"
      ]);
    }

    return $this->json([
      "status" => "error",
      "message" => "No se pudo guardar los datos"
    ]);
  }
}
