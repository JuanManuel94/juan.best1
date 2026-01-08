<?php

class Consultas extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
  }

  public function consultas()
  {
    $data['page_name'] = "AP Clientes";
    $data['page_title'] = "AP Clientes";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Gestión de Red";
    $data['actual_page'] = "AP Clientes";
    $data['page_functions_js'] = "ap_clientes.js";
    $_SESSION['businessData'] = $this->model->get_business();
    $this->views->getView($this, "consultas", $data);
  }

  public function list_bills()
  {
    $filters = $_GET;
    if (empty($filters['type'])) {
      return $this->json([
        "success" => false,
        "message" => "No se encontró el modo de búsqueda",
      ]);
    }
    if (empty($filters['value'])) {
      return $this->json([
        "success" => false,
        "message" => "No se encontró el valor de búsqueda",
      ]);
    }
    try {
      // cliente
      $client = $this->model->find_client($filters);
      $business = $this->model->get_business();

      if (!$client) {
        return $this->json([
          "success" => false,
          "message" => "No se encontró al cliente!",
        ]);
      }
      // facturas
      $data = $this->model->list_bills($client['id']);
      if (!count($data)) {
        return $this->json([
          "success" => false,
          "message" => "No se encontrarón pagos",
        ]);
      }
      // each
      foreach ($data as $i => $item) {
        $payments = $this->model->invoice_paid($item['id']);
        $month_letter = "";
        if ($item['type'] == 2) {
          $months = months();
          $month = date('n', strtotime($data[$i]['billed_month']));
          $year = date('Y', strtotime($data[$i]['billed_month']));
          $month_letter = strtoupper($months[intval($month) - 1]) . "," . $year;
        }
        // encrypt
        $item['encrypt'] = encrypt($item['id']);
        $item['encrypt_client'] = encrypt($item['clientid']);
        // comprobante
        $correlative = str_pad($item['correlative'], 7, "0", STR_PAD_LEFT);
        $invoice = "#" . $correlative;
        $item['invoice'] = $correlative;
        $item['billing'] = $month_letter ? $month_letter : "OTRO SERVICIO";
        // fecha de expiración
        $item['payment_date'] = empty($payments['payment_date']) ? "00/00/0000" : date("d/m/Y", strtotime($payments['payment_date']));
        $item['waytopay'] = empty($payments['payment_type']) ? "" : $payments['payment_type'];
        // total pro factura
        $item['count_total'] = $data[$i]['total'];
        $item['count_subtotal'] = empty($payments['amount_total']) ? format_money(0) : $payments['amount_total'];
        // total pagado
        $item['total'] = $_SESSION['businessData']['symbol'] . format_money($item['total']);
        $item['balance'] = $_SESSION['businessData']['symbol'] . format_money($item['remaining_amount']);
        $item['subtotal'] = $_SESSION['businessData']['symbol'] . format_money($item['subtotal']);
        $item['discount'] = $_SESSION['businessData']['symbol'] . format_money($item['discount']);
        // estados de factura
        if ($item['state'] == 1) {
          $item['count_state'] = "PAGADO";
        } else if ($item['state'] == 2) {
          $item['count_state'] = "PENDIENTE";
        } else if ($item['state'] == 3) {
          $item['count_state'] = "VENCIDO";
        } else if ($item['state'] == 4) {
          $item['count_state'] = 'ANULADO';
        }
        // add 
        $data[$i] = $item;
      }
      // Ordenar las facturas por la fecha de emisión (de más reciente a más antiguo)
      usort($data, function ($a, $b) {
        // Asegúrate de que el campo `billed_month` esté bien formateado
        $dateA = strtotime($a['billed_month']);
        $dateB = strtotime($b['billed_month']);

        return $dateB - $dateA;  // Orden descendente (más reciente primero)
      });

      // habilitar sessión
      $_SESSION['consulta'] = true;
      $_SESSION['permits_module']['v'] = 1;
      // response 
      return $this->json([
        "success" => true,
        "message" => "Boletas encontradas",
        "client" => $client,
        "business" => $business,
        "data" => $data
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "success" => false,
        "message" => "Algo salió mal",
        "err" => $th->getMessage()
      ]);
    }
  }

  public function list_incidents()
  {
    $data = $this->model->list_incidents($_GET);
    return $this->json($data);
  }

  public function validation($phone)
  {
    try {
      if (!$phone) {
        throw new Exception("El número es invalido!!!");
      }

      $numero_aleatorio = generate_password(8);
      $_SESSION['validation'] = $numero_aleatorio;

      $business = $this->model->get_business();

      // Formatear el mensaje
      $message = "VERIFICACIÓN PARA GENERAR *TICKET DE SOPORTE*\n\n";
      $message .= "El código de verificación es: *{$numero_aleatorio}*, ";
      $message .= "no compartas este código con nadie.";

      $service = new SendWhatsapp($business);
      $isSend = $service->send($phone, $message);

      if (!$isSend) {
        throw new Exception("No se pudo enviar el código a {$phone}");
      }

      return $this->json([
        "success" => true,
        "message" => "El código se envió correctamente!",
      ]);
    } catch (\Throwable $th) {
      return $this->json([
        "success" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

  public function compareValidation($code)
  {
    if ($code !== $_SESSION['validation']) {
      return $this->json([
        "success" => false,
        "message" => "El código es incorrecto"
      ]);
    }

    return $this->json([
      "success" => true,
      "message" => "El código es correcto"
    ]);
  }

  public function save_incident()
  {
    try {
      $data = [
        "clientid" => $_POST['clientid'],
        "userid" => 1,
        "technical" => 0,
        "incidentsid" => $_POST['listAffairs'],
        "description" => $_POST['description'],
        "priority" => $_POST['listPriority'],
        "attention_date" => $_POST['attention_date'],
        "opening_date" => "0000-00-00 00:00:00",
        "closing_date" => "0000-00-00 00:00:00",
        "registration_date" => date("Y-m-d H:i:s"),
        "state" => 2
      ];
      // guardar
      $ticketId = $this->model->save_incident($data);
      // notificar
      $business = (Object) $_SESSION['businessData'];
      $client = (object) $this->model->find_client(["id" => $_POST['clientid']]);

      if ($client->mobile) {
        $messageWsp = new PlantillaWspInfoService($client, $business);
        $messageWsp->setTicketId($ticketId);
        $message = $messageWsp->execute("SUPPORT_TECNICO");
        $wsp = new SendWhatsapp($business);
        $number = "{$business->country_code}{$client->mobile}";
        $wsp->send($number, $message);
      }

      return $this->json([
        "success" => true,
        "message" => "El ticket se guardó correctamenete!!!"
      ]);
    } catch (\Throwable $th) {
      return $this->json([
        "success" => false,
        "message" => "No se pudo guardar el ticket",
        "err" => $th->getMessage()
      ]);
    }
  }
}