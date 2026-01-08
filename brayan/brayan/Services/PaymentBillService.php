<?php

class PaymentBillService
{
  private Mysql $mysql;
  private object $business;
  private string $datetime;
  private string $userId;
  private object $client;
  private string $payTypeId;
  private string $comment = "";
  private bool $canActive = true;

  public function __construct(object $business, object $client, string $userId, string $payTypeId)
  {
    $this->business = $business;
    $this->mysql = new Mysql("bills");
    $this->datetime = date('Y-m-d H:i:s');
    $this->client = $client;
    $this->userId = $userId;
    $this->payTypeId = $payTypeId;
  }

  public function execute(string $billId, float $amountPayment, float $discount = 0)
  {
    // validar
    $bill = (object) $this->findBill($billId);
    // validar contracto
    $contract = (Object) $this->findContract($this->client->id);
    if (empty($contract)) {
      throw new Exception("No existe contrato activo");
    }
    // validar si está disponible el bill
    if (empty($bill)) {
      throw new Exception("La facturá no está disponible!!!");
    }
    // variables
    $state = $bill->state;
    $remainingAmount = (float) $bill->remaining_amount;
    $subtotal = (float) $bill->subtotal;
    $paymentDiscount = (float) $bill->discount;
    $amountPaid = (float) $bill->amount_paid;
    $total = 0;
    // validar descuento
    if ($discount == 0) {
      $total = $subtotal - $paymentDiscount;
    } elseif ($discount > $remainingAmount) {
      $paymentDiscount += $remainingAmount;
      $discount -= $remainingAmount;
      $remainingAmount = 0;
      $total = 0;
    } else {
      $paymentDiscount = $paymentDiscount + $discount;
      $total = $subtotal - $paymentDiscount;
      $remainingAmount -= $discount;
      $discount = 0;
    }

    $amount = 0;

    // validar a pagar
    if ($remainingAmount == 0) {
      // actualizar estado a pagado
      $state = 1;
    } elseif ($remainingAmount >= $amountPayment) {
      // pago cancelado
      $amount = $amountPayment;
      $amountPaid += $amountPayment;
      $remainingAmount -= $amountPayment;
      $amountPayment -= $amountPaid;
      // actualizar estado a pagado
      $state = $remainingAmount > 0 ? 2 : 1;
    } elseif ($amountPaid < $amountPayment) {
      // pago sin deuda
      $amount = $remainingAmount;
      $amountPaid += $remainingAmount;
      $amountPayment -= $remainingAmount;
      $remainingAmount -= $remainingAmount;
      $state = 1;
    }

    // actualizar bill
    $this->updateBill($billId, [
      "discount" => $paymentDiscount,
      "total" => $total,
      "amount_paid" => $amountPaid,
      "remaining_amount" => $remainingAmount,
      "state" => $state
    ]);

    // generar código pago
    $code = $this->getPaymentCode();

    // crear pago
    $paymentId = $this->createPayment(
      [
        "billid" => $billId,
        "userid" => $this->userId,
        "clientid" => $bill->clientid,
        "internal_code" => $code,
        "paytypeid" => $this->payTypeId,
        "payment_date" => $this->datetime,
        "comment" => $this->comment,
        "amount_paid" => $amount,
        "amount_total" => $total,
        "remaining_credit" => 0,
      ]
    );

    // activar solo si está suspendido
    if ($this->canActive && $contract->state == 3) {
      $serviceActived = new ClientActivedService($this->business);
      $serviceActived->setCanTransaction(false);
      $result = $serviceActived->execute($contract->clientid);
      if (!$result['success']) {
        throw new Exception($result['message']);
      }
    }

    // response
    return [
      "paymentId" => $paymentId,
      "billId" => $billId,
      "bill" => $bill,
      "amountPayment" => $amountPayment,
      "discount" => $discount
    ];
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function setDatetime(string $datetime)
  {
    $this->datetime = $datetime;
  }

  public function setComment(string $comment)
  {
    $this->comment = $comment;
  }

  public function setDiscount(float $discount)
  {
    $this->discount = $discount;
  }

  public function setCanActive(bool $canActive)
  {
    $this->canActive = $canActive;
  }

  private function findBill(string $billId)
  {
    return $this->mysql->createQueryBuilder()
      ->from("bills", "b")
      ->innerJoin("clients c", "b.clientid = c.id")
      ->innerJoin("vouchers v", "b.voucherid = v.id")
      ->innerJoin("voucher_series vs", "b.serieid = vs.id")
      ->andWhere("b.id = {$billId}")
      ->andWhere("b.state NOT IN (0, 1, 4)")
      ->select("b.*")
      ->addSelect("CONCAT_WS(' ', c.names, c.surnames)", "client_name")
      ->addSelect("c.mobile", "client_mobile")
      ->addSelect("vs.serie", "serie")
      ->addSelect("v.voucher", "voucher")
      ->getOne();
  }

  private function getPaymentCode()
  {
    $data = $this->mysql->createQueryBuilder()
      ->from("payments", "p")
      ->select("MAX(internal_code) AS code")
      ->getOne();
    // payment
    if (empty($data)) {
      return sprintf("T%05d", 1);
    } else {
      $code = (int) substr($data['code'], 1);
      return sprintf("T%05d", $code + 1);
    }
  }

  private function updateBill(string $billId, array $payload)
  {
    return $this->mysql->createQueryBuilder()
      ->update()
      ->from("bills")
      ->andWhere("id = {$billId}")
      ->set($payload)
      ->execute();
  }

  private function createPayment($payload)
  {
    $this->mysql->setTableName("payments");
    return $this->mysql->insertObject([
      "billid",
      "userid",
      "clientid",
      "internal_code",
      "paytypeid",
      "payment_date",
      "comment",
      "amount_paid",
      "amount_total",
      "remaining_credit",
    ], $payload);
  }

  private function findContract(string $clientId)
  {
    return $this->mysql->createQueryBuilder()
      ->from("contracts")
      ->where("clientid = {$clientId}")
      ->getOne();
  }
}