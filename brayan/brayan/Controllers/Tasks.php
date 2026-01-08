<?php

class Tasks extends Controllers
{
  public function __construct()
  {
    parent::__construct();
  }
  public function invoice_receipts()
  {
    $month = date("m");
    $year = date("Y");
    $result = 0;
    $data = $this->model->customers_invoice($month, $year);
    for ($i = 0; $i < count($data); $i++) {
      $idclient = $data[$i]['clientid'];
      $issue = date("Y-m-d");
      $payday = str_pad($data[$i]['payday'], 2, "0", STR_PAD_LEFT);
      $current = date("Y-m-" . $payday);
      $expiration = date("Y-m-d", strtotime($current . " + 1 month"));
      $months = months();
      $month_letter = $months[date('n', strtotime($current)) - 1];
      $voucher = 1;
      $serie = 1;
      $row = $this->model->returnCode();
      if ($row == 0) {
        $code = "V00001";
      } else {
        $max = $this->model->generateCode();
        $code = "V" . substr((substr($max, 1) + 100001), 1);
      }
      $num_corre = $this->model->returnCorrelative($voucher, $serie);
      if (empty($num_corre)) {
        $correlative = 1;
      } else {
        $correlative = $this->model->returnUsed($voucher, $serie);
      }
      $total = $this->model->service_amount($data[$i]['id']);
      $request = $this->model->invoice_receipts(1, 1, $idclient, $voucher, $serie, $code, $correlative, $issue, $expiration, $current, $total, 0, $total, 2, 2, "", 2);
      if ($request > 0) {
        $idbill = $this->model->returnBill();
        $this->model->modify_available($voucher, $serie);
        $services = $this->model->select_detail_contract($data[$i]['id']);
        for ($d = 0; $d < count($services); $d++) {
          $description_service = "SERVICIO DE " . $services[$d]['service'] . ",MES DE " . strtoupper($month_letter);
          $this->model->create_datail($idbill, 2, $services[$d]['serviceid'], $description_service, 1, $services[$d]['price'], $services[$d]['price']);
        }
      }
      $result = $result + $request;
    }
    if ($result >= 1) {
      echo "Se facturo los servicios de este mes.";
    } else if ($result == 0) {
      echo "Las facturas de este mes ya fueron emitidas.";
    } else {
      echo "No se pudo realizar esta operación, intentelo nuevamente.";
    }
    die();
  }
  public function expired_bills()
  {
    $result = 0;
    $data = $this->model->pending_bills();
    for ($i = 0; $i < count($data); $i++) {
      $currentDate = date("Y-m-d");
      $expirationDate = $data[$i]['expiration_date'];
      if ($currentDate !== $expirationDate) {
        $current_date = new DateTime("now");
        $expiration = new DateTime($data[$i]['expiration_date']);
        $diff = $expiration->diff($current_date);
        $days = $diff->invert;
        if ($days <= 0) {
          // SEND BLOCK REQUEST TO ROUTER
          if ($data[$i]['promise_enabled'] == "1" && (new DateTime($data[$i]['promise_date']))->diff($current_date)->invert > 0) {
          } else {
            sqlUpdate("bills", "promise_enabled", "0", $data[$i]['id']);

            $clientid = $data[$i]["clientid"];
            $clientd = sqlObject("SELECT * FROM clients WHERE id = $clientid");
            $r = sqlObject("SELECT r.*, z.mode mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE r.id = " . $clientd->net_router);

            if (!is_null($r->id)) {

              $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));
              $flr = $router->APIGetFirewallAddress($clientd->net_ip, "moroso");
              if ($flr->success && count($flr->data) == 0) {
                $ares = $router->APIAddFirewallAddress($clientd->net_ip, "moroso", "");
              }
            }
          }
          //
          $request = $this->model->expired_bills($data[$i]['id']);
        }
      }
      $result = $result + $request;
    }
    if ($result >= 1) {
      echo "Operación exitosa.";
    } else {
      echo "No se pudo realizar esta operación, intentelo nuevamente.";
    }
    die();
  }
  public function expired_tickets()
  {
    $result = 0;
    $data = $this->model->pending_tickets();
    for ($i = 0; $i < count($data); $i++) {
      $currentDate = date("Y-m-d");
      $expirationDate = date("Y-m-d", strtotime($data[$i]['attention_date']));
      if ($currentDate !== $expirationDate) {
        $current_date = new DateTime("now");
        $expiration = new DateTime($data[$i]['attention_date']);
        $diff = $expiration->diff($current_date);
        $days = $diff->invert;
        if ($days <= 0) {
          $request = $this->model->expired_tickets($data[$i]['id']);
        }
      }
      $result = $result + $request;
    }
    if ($result >= 1) {
      echo "Operación exitosa.";
    } else {
      echo "No se pudo realizar esta operación, intentelo nuevamente.";
    }
    die();
  }

  public function deuda_send_whatsapp()
  {
    try {
      $notify = new PendingSendMessageWhatsapp();
      $isNotify = $notify->send();

      if (!$isNotify) {
        throw new Exception($notify->getMessageError());
      }

      return $this->json([
        "status" => true,
        "message" => "Notificación enviada correctamente!"
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "status" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

  public function invoice_send_email()
  {
    set_time_limit(0);
    try {
      $year = date('Y');
      $month = date('m');

      $errors = [];
      $data = $this->model->bill_voucheres([
        "year" => $year,
        "month" => $month,
        "email:required" => true
      ]);

      foreach ($data as $i => $item) {
        $email = new InvoiceEmailService($this->model);
        $email->send($item['business'], $item, "FACTURA PENDIENTE DE PAGO");
        if ($email->getErrorMessage())
          array_push($errors, $email->getErrorMessage());
      }

      $this->json([
        "status" => true,
        "msg" => "Correos enviados!!!",
        "errores" => $errors
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "status" => false,
        "msg" => $th->getMessage()
      ]);
    }
  }

  public function invoice_send_whatsapp()
  {
    $bill = new BillSendMessageWhatsapp();
    $data = $bill->send([
      "phone:required" => true,
    ]);
    $this->json($data);
  }

  public function invoice_send_payday_whatsapp()
  {
    $bill = new BillSendMessageWhatsapp();
    $data = $bill->send([
      "phone:required" => true,
      "payday" => date('d')
    ]);
    $this->json($data);
  }

  public function client_deuda_suspend()
  {
    try {
      $date = date('Y-m-d');
      $service = new ClientSuspendMassiveService();
      $response = $service->execute($date);
      return $this->json([
        "success" => true,
        "message" => "rutina completada",
        "clients" => $response["result"],
        "errors" => $response['error']
      ]);
    } catch (\Throwable $th) {
      return $this->json([
        "success" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

  public function client_deuda_suspend_ten_day()
  {
    try {
      $date = date('Y-m-d');
      $service = new ClientSuspendTenDayNotifyService();
      $response = $service->execute($date);
      return $this->json([
        "success" => true,
        "message" => "rutina completada",
        "clients" => $response["result"],
        "errors" => $response['error']
      ]);
    } catch (\Throwable $th) {
      return $this->json([
        "success" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

}
