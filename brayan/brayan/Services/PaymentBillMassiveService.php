<?php

class PaymentBillMassiveService
{
  private Mysql $mysql;
  private string $orderBy = "ASC";
  private string $datetime;
  private string $userId;
  private string $payTypeId;
  private string $comment = "";

  public function __construct(string $userId, string $payTypeId)
  {
    $this->mysql = new Mysql("bills");
    $this->datetime = date('Y-m-d H:i:s');
    $this->userId = $userId;
    $this->payTypeId = $payTypeId;
  }

  public function execute(string $clientId, float $amountPayment, float $discount = 0)
  {
    $arrayBillId = [];
    $arrayPaymentId = [];
    // validar monto a pagar
    if ($amountPayment <= 0) {
      throw new Exception("El monto a pagar debe ser mayor a cero");
    }
    // validar client
    $client = (Object) $this->findClient($clientId);
    if (empty($client)) {
      throw new Exception("No se encontró el cliente");
    }
    // obtener deuda total  
    $debtCurrent = $this->getCountTotal($clientId);
    // validar monto limite de deuda
    if ($amountPayment > $debtCurrent) {
      throw new Exception("El monto a pagar debe ser menor/igual a {$debtCurrent}");
    }
    // validar deuda
    if ($debtCurrent <= 0) {
      throw new Exception("El cliente no tiene deuda");
    }
    // obtener todas las deudas
    $bills = $this->getListBills($clientId);
    $this->mysql->createQueryRunner();
    // process
    try {
      // validar activación
      $canActive = true;
      // iterar bills
      foreach ($bills as $bill) {
        if ($amountPayment > 0) {
          $business = $this->findBusiness();
          $paymentBill = new PaymentBillService($business, $client, $this->userId, $this->payTypeId);
          $paymentBill->setMysql($this->mysql);
          $paymentBill->setCanActive($canActive);
          $paymentBill->setComment($this->comment);
          $paymentBill->setDatetime($this->datetime);
          $result = $paymentBill->execute($bill['id'], $amountPayment, $discount);
          $discount = $result['discount'];
          $amountPayment = $result['amountPayment'];
          array_push($arrayBillId, $bill['id']);
          array_push($arrayPaymentId, $result['paymentId']);
          $canActive = false;
        }
      }

      $this->mysql->commit();
      // response
      return [
        "client" => $client,
        "currentPaid" => $amountPayment,
        "arrayBillId" => $arrayBillId,
        "arrayPaymentId" => $arrayPaymentId
      ];
    } catch (\Throwable $th) {
      $this->mysql->rollback();
      throw $th;
    }
  }

  public function setDatetime(string $datetime)
  {
    $this->datetime = $datetime;
  }

  public function setComment(string $comment)
  {
    $this->comment = $comment;
  }

  public function setOrderBy(string $orderBy)
  {
    $this->orderBy = $orderBy;
  }

  private function getCountTotal(string $clientId)
  {
    $data = $this->mysql->createQueryBuilder()
      ->from("bills", "b")
      ->andWhere("b.clientid = {$clientId}")
      ->andWhere("b.state NOT IN (0, 1, 4)")
      ->select("IFNULL(SUM(b.remaining_amount), 0) total")
      ->getOne();
    return (float) $data["total"];
  }

  private function getListBills(string $clientId)
  {
    return $this->mysql->createQueryBuilder()
      ->from("bills", "b")
      ->andWhere("b.clientid = {$clientId}")
      ->andWhere("b.state NOT IN (0, 1, 4)")
      ->select("b.*")
      ->orderBy("b.billed_month", $this->orderBy)
      ->getMany();
  }

  private function findClient(string $clientId)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients")
      ->where("id = {$clientId}")
      ->getOne();
  }

  private function findBusiness()
  {
    return (Object) $this->mysql->createQueryBuilder()
      ->from("business")
      ->getOne();
  }
}