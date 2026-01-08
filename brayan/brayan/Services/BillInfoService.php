<?php

class BillInfoService
{
  private $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql();
  }

  public function execute(int $clientId)
  {
    /* CONSULTA PARA OBTENER LA INFO DEL CLIENTE */
    $client = $this->findClient($clientId);
    if (!$client)
      throw new Exception("No se encontró al cliente");
    // obtener total de factura
    $validate = $this->getValidate($clientId);
    // nombre completo
    $full_name = $client['names'] . " " . $client['surnames'];
    // obtener contract
    $contract = $this->findContract($clientId);
    if (!$contract)
      throw new Exception("No se encontró el contrato");
    $contractId = $contract['id'];
    $details = $contract['details'];
    // validar descuento
    $discount = 0;
    if ($contract['discount'] == 1) {
      $discount = $contract['discount_price'];
    }
    // obtener total
    $total = $this->getSumTotal($contractId);
    // obtener total de servicios
    $totalServices = $this->getTotalServices($contractId);
    // validar servicios
    if ($validate >= 1) {
      // obtener facturas
      $bill = (Object) $this->findBill($clientId);
      $isset = date("d/m/Y", strtotime($bill->expiration_date . " + 1 day"));

      // generar fecha de expiración
      $dateExp = DateTime::createFromFormat('d/m/Y', $isset);
      $dateExp->modify('+1 month');
      $dateExp->setDate((int) $dateExp->format('Y'), (int) $dateExp->format('m'), $contract['payday']);
      $expiration = $dateExp->format('d/m/Y');

      /* MES DE FACTURACION */
      $dateBill = $dateExp = DateTime::createFromFormat('d/m/Y', $isset);
      $dateBill->setDate((int) $dateExp->format('Y'), (int) $dateExp->format('m'), 1);
      $billed_month = $dateExp->format('d/m/Y');

      $billed_month_str = (int) $dateBill->format('m');
      /* OBTENER MES EN LETRAS */
      $months = months();
      $month = $months[$billed_month_str - 1];

      /* MODIFICAR EL COSTO DEL SERVCIO POR LOS DIAS A UTILIZAR */
      if (count($details) > 0) {
        foreach ($details as $index => $detail) {
          $text = "SERVICIO DE {$detail['service']}, MES DE " . strtoupper($month);
          $details[$index]['service'] = $text;
        }
      }

      $invoice = array(
        "clientId" => $clientId,
        "clientFullname" => $full_name,
        "expiration" => $expiration,
        "billed_month" => $billed_month,
        "isset" => $isset,
        "discount" => $discount,
        "status" => $contract['state']
      );

      return [
        "invoice" => $invoice,
        "details" => $details,
        "service" => $totalServices,
        "total" => $total
      ];
    }
    // bill por primera vez
    $month_contract = date("m", strtotime($contract['contract_date']));
    $year_contract = date("Y", strtotime($contract['contract_date']));
    // obtener facility
    $facility = $this->findFacility($clientId);
    /* DIA DE PAGO */
    $payday = (int) str_pad($contract['payday'], 2, "0", STR_PAD_LEFT);
    /* FECHA DE VENCIMIENTO DE LA FACTURA */
    $date_payday = date("Y-m-" . $payday);
    $currentDay = (int) date("d");
    $billed_month = date("d/m/Y", strtotime($date_payday));

    if ($payday > $currentDay) {
      $date_exp = date("Y-m-d", strtotime($date_payday));
      $expiration = date("d/m/Y", strtotime($date_payday));
    } else {
      $date_exp = date("Y-m-d", strtotime($date_payday . " + 1 months"));
      $expiration = date("d/m/Y", strtotime($date_payday . " + 1 months"));
    }

    /* OBTENER MES EN LETRAS */
    $months = months();
    $month = $months[date('n', strtotime($date_payday)) - 1];
    // validar atención
    if (!empty($facility['attention_date'])) {
      /* FECHA DE INSTALACION */
      $date_facility = new DateTime($facility['attention_date']);
      /* OBTENER TOTAL DE DIAS DEL MES */
      $date_start = new DateTime(date("Y-m-d", strtotime($date_payday)));
      $date_over = new DateTime(date("Y-m-d", strtotime($date_payday . " + 1 months")));

      $diff_total = $date_over->diff($date_start);
      $total_day = ($diff_total->invert == 1) ? $diff_total->days : $diff_total->days;

      // validar 30 dias
      $total_day = $total_day > 30 ? 30 : $total_day;

      /* ULTIMO DIA DEL MES EN FORMATO FECHA */
      $last_day = new DateTime($date_exp);
      if ($payday > $currentDay) {
        $date_calc = $date_facility;
      } else if ($payday < $currentDay) {
        $date_calc = new DateTime(date("Y-m-d", strtotime($contract['contract_date'])));
      } else {
        $date_calc = $date_start;
      }

      /* OBETENR LOS DIAS DE DIERENCIA ENTRE EL CONTRATO Y ULTIMO DIA DEL MES */
      $diff = $last_day->diff($date_calc);
      $used_days = ($diff->invert == 1) ? $diff->days : $diff->days;
      $used_days = $used_days ? $used_days + 1 : 1;
    }

    /* MODIFICAR EL COSTO DEL SERVCIO POR LOS DIAS A UTILIZAR */
    if (count($details) > 0) {
      $total = 0;
      foreach ($details as $index => $detail) {
        $text = "SERVICIO DE " . $detail['service'] . ", MES DE " . strtoupper($month);
        if (!empty($facility['attention_date'])) {
          // validar prorroteo
          if ($total_day == 0) {
            $cost_day = 0;
          } else {
            $cost_day = $detail['price'] / $total_day;
          }

          $price_prorrateado = $cost_day * $used_days;
          $detail['price'] = format_money(round($price_prorrateado, 1));
          $detail['service'] = $text . " PRORRATEADO";
        } else {
          $detail['service'] = $text;
        }
        // actualizar details
        $total += (float) $detail['price'];
        $details[$index] = $detail;
      }
    }

    $invoice = array(
      "clientId" => $clientId,
      "clientFullname" => $full_name,
      "expiration" => $expiration,
      "billed_month" => $billed_month,
      "isset" => date("d/m/Y"),
      "discount" => $discount,
      "status" => $contract['state']
    );

    return [
      "invoice" => $invoice,
      "details" => $details,
      "service" => $totalServices,
      "total" => $total
    ];
  }

  private function findClient(int $id)
  {
    $sql = "SELECT * FROM clients WHERE id = $id";
    return $this->mysql->select($sql);
  }

  private function findContract(int $clientId)
  {
    $sql = "SELECT * FROM contracts WHERE clientid = {$clientId}";
    $contract = $this->mysql->select($sql);
    if (!$contract)
      throw new Exception("No se encontró el contrato");
    $contract['details'] = [];
    $sql = "SELECT s.id,s.service,s.price
          FROM detail_contracts dc
          JOIN services s ON dc.serviceid = s.id
          WHERE dc.contractid = {$contract['id']}";
    $details = $this->mysql->select_all($sql);
    if ($details)
      $contract['details'] = $details;
    return $contract;
  }

  private function getValidate(int $clientId)
  {
    $sql = "SELECT COUNT(*) AS total FROM bills WHERE clientid = {$clientId} AND state != 4 AND type = 2";
    $request = $this->mysql->select($sql);
    return $request["total"];
  }

  private function getTotalServices(int $contractId)
  {
    $sql = "SELECT COUNT(serviceid) AS services FROM detail_contracts WHERE contractid = $contractId";
    $data = $this->mysql->select($sql);
    return $data['services'];
  }

  private function getSumTotal(int $contractId)
  {
    $sql = "SELECT COALESCE(SUM(price),0) as total FROM detail_contracts WHERE contractid = {$contractId}";
    $answer = $this->mysql->select($sql);
    $total = $answer['total'];
    return $total;
  }

  private function findBill(int $clientId)
  {
    $sql = "SELECT * FROM bills WHERE clientid = {$clientId} AND state != 4 AND type = 2 ORDER BY id DESC LIMIT 1";
    return $this->mysql->select($sql);
  }

  private function findFacility(int $clientId)
  {
    $sql = "SELECT MAX(attention_date) AS attention_date FROM facility WHERE clientid = {$clientId} AND state != 5";
    return $this->mysql->select($sql);
  }
}