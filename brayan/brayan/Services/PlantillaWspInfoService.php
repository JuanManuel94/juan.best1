<?php

class PlantillaWspInfoService
{
  private Mysql $mysql;
  private object $client;
  private object $business;
  private array $arrayBillId = [];
  private array $arrayPaymentId = [];
  private string $ticketId;
  private array $array_client = [];

  public function __construct(object $client, object $business)
  {
    $this->client = $client;
    $this->business = $business;
    $this->mysql = new Mysql("business_wsp");
  }

  public function execute(string $messageId)
  {
    $message = $this->findMessage($messageId);
    if (!$message) {
      throw new Exception("No se encontró el mensaje");
    }

    return $this->replaceMessage($message);
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function setArrayPaymentId(array $arrayPaymentId)
  {
    $this->arrayPaymentId = $arrayPaymentId;
  }

  public function setArrayBillId(array $arrayBillId)
  {
    $this->arrayBillId = $arrayBillId;
  }

  public function setTicketId(string $ticketId)
  {
    $this->ticketId = $ticketId;
  }

  private function replaceMessage(object $message)
  {
    $currency = $this->find_currency();
    $this->array_client = [
      "names" => strtoupper($this->client->names),
      "surnames" => strtoupper($this->client->surnames),
      "cliente" => strtoupper("{$this->client->names} {$this->client->surnames}"),
      "document" => $this->client->document,
      "mobile" => $this->client->mobile,
      "mobiledos" => $this->client->mobile_optional,
      "note" => $this->client->note,
      "email" => $this->client->email,
      "address" => $this->client->address,
      "latitud" => $this->client->latitud,
      "longitud" => $this->client->longitud,
      "reference" => $this->client->reference,
      "net_ip" => $this->client->net_ip,
      "debt_total_list" => $currency->symbol . $this->debtSum(),
      "debt_total_month_count" => $this->debtMonthSum(),
      "business_name" => $this->business->business_name,
    ];

    $this->pluginDebts($currency);
    $this->pluginPayments($currency);
    $this->pluginTicket();

    $str_message = "*$message->titulo* \n\n";
    $str_message .= $message->contenido;

    foreach ($this->array_client as $attr => $value) {
      $str_message = str_replace("{{$attr}}", $value, $str_message);
    }

    return $str_message;
  }

  private function findMessage(string $messageId)
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("business_wsp")
      ->where("id = '{$messageId}'")
      ->getOne();
  }

  private function find_currency()
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("currency")
      ->getOne();
  }

  private function debtSum()
  {
    $data = $this->mysql->createQueryBuilder()
      ->from("bills")
      ->select("IFNULL(SUM(remaining_amount), 0) total")
      ->where("state IN (2, 3)")
      ->andWhere("clientid = {$this->client->id}")
      ->getOne();
    $amount = (float) $data['total'];
    return format_money($amount);
  }

  private function debtMonthSum()
  {
    $data = $this->mysql->createQueryBuilder()
      ->from("bills")
      ->select("COUNT(*) count")
      ->where("state IN (2, 3)")
      ->andWhere("clientid = {$this->client->id}")
      ->getOne();

    $count = (int) $data['count'];

    if ($count == 0) {
      return "";
    } elseif ($count == 1) {
      return $count . " mes";
    } else {
      return $count . " meses";
    }
  }

  
  private function pluginDebts(object $currency)
  {
    $baseUrl = BASE_URL;
    $str_message = "";
    $array_months = [];
    $array_promise_date = [];
    $array_promise_comment = [];
    $debt_amount = 0;

    if (!count($this->arrayBillId)) {
      return $str_message;
    }

    $condition = implode(", ", $this->arrayBillId);
    $body = $this->mysql->createQueryBuilder()
      ->from("bills")
      ->where("clientid = {$this->client->id}")
      ->andWhere("id IN ({$condition})")
      ->getMany();

    foreach ($body as $item) {
      $billId = encrypt($item['id']);
      $link = "{$baseUrl}/invoice/document/{$billId}";

      if ($item['type'] == 1) {
        $monthYear = "SERVICIO DE INSTALACIÓN";
        $array_months[] = "INSTALACIÓN";
      } elseif ($item['type'] == 2 && DateTime::createFromFormat('Y-m-d', $item['billed_month'])) {
        $datetime = DateTime::createFromFormat('Y-m-d', $item['billed_month']);
        $month = MONTHS[$datetime->format("m")];
        $monthYear = "{$month}/*{$datetime->format("Y")}*";
        $array_months[] = $month;
      } else {
        $monthYear = "PERÍODO DESCONOCIDO";
        $array_months[] = "DESCONOCIDO";
      }

      $str_message .= "- {$monthYear}: {$link}\n";
      $debt_amount += (float) $item['remaining_amount'];
      $array_promise_date[] = $item['promise_date'];
      $array_promise_comment[] = $item['promise_comment'];
    }

    $this->array_client["debt_list"] = $str_message;
    $this->array_client["debt_amount"] = $currency->symbol . format_money($debt_amount);
    $this->array_client["debt_months"] = implode(", ", $array_months);
    $this->array_client["promise_date"] = implode(", ", $array_promise_date);
    $this->array_client["promise_comment"] = implode(", ", $array_promise_comment);
  }

 
  private function pluginPayments(object $currency)
  {
    $baseUrl = BASE_URL;
    $str_message = "";
    $array_links = [];
    $array_nums = [];
    $array_months = [];

    if (!count($this->arrayPaymentId)) {
      return $str_message;
    }

    $total = 0;
    $total_pending = 0;
    $condition = implode(", ", $this->arrayPaymentId);
    $body = $this->mysql->createQueryBuilder()
      ->from("payments p")
      ->innerJoin("bills b", "b.id = p.billid")
      ->innerJoin("voucher_series vs", "vs.id = b.serieid")
      ->where("b.clientid = {$this->client->id}")
      ->andWhere("p.id IN ({$condition})")
      ->select("b.*")
      ->addSelect("p.amount_paid", "current_paid")
      ->addSelect("vs.serie", "serie")
      ->getMany();

    foreach ($body as $item) {
      $billId = encrypt($item['id']);
      $link = "{$baseUrl}/invoice/document/{$billId}";

      if ($item['type'] == 1) {
        $monthYear = "SERVICIO DE INSTALACIÓN";
        $array_months[] = "INSTALACIÓN";
      } elseif ($item['type'] == 2 && DateTime::createFromFormat('Y-m-d', $item['billed_month'])) {
        $datetime = DateTime::createFromFormat('Y-m-d', $item['billed_month']);
        $month = MONTHS[$datetime->format("m")];
        $monthYear = "{$month}*{$datetime->format("Y")}*";
        $array_months[] = $month;
      } else {
        $monthYear = "PERÍODO DESCONOCIDO";
        $array_months[] = "DESCONOCIDO";
      }

      $str_message .= "- {$monthYear}: {$link}\n";

      $array_nums[] = $item['serie'] . "-" . str_pad($item["correlative"], 7, "0", STR_PAD_LEFT);
      $array_links[] = $link;

      $total += (float) $item['current_paid'];
      $total_pending += (float) $item['remaining_amount'];
    }

    $this->array_client["list_payments"] = $str_message;
    $this->array_client["payment_months"] = implode(", ", $array_months);
    $this->array_client["payment_total"] = $currency->symbol . format_money($total);
    $this->array_client["payment_links"] = implode("\n", $array_links);
    $this->array_client["payment_num"] = implode(", ", $array_nums);
    $this->array_client["payment_pending"] = $currency->symbol . format_money($total_pending);
  }

  private function pluginTicket()
  {
    if (empty($this->ticketId)) {
      return;
    }

    $ticket = (object) $this->mysql->createQueryBuilder()
      ->from("tickets")
      ->where("clientid = {$this->client->id}")
      ->andWhere("id = {$this->ticketId}")
      ->getOne();

    if (!$ticket) {
      return;
    }

    $this->array_client["ticket_num"] = str_pad($ticket->id, 7, "0", STR_PAD_LEFT);
  }
}
