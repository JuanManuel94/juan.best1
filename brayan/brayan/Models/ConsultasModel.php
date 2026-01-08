<?php

class ConsultasModel extends Mysql
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_business()
  {
    return $this->createQueryBuilder()
      ->from("business b")
      ->innerJoin("currency c", "c.id = currencyid")
      ->select("b.*, c.symbol")
      ->setLimit(1)
      ->getOne();
  }

  public function find_client($filters = [])
  {
    $value = isset($filters['value']) ? $filters['value'] : null;
    $type = isset($filters['type']) ? $filters['type'] : null;

    $query = $this->createQueryBuilder()
      ->select("*")
      ->addSelect("CONCAT(names, CONCAT(' ', surnames))", "cliente")
      ->from("clients");
    // filter 
    if ($value && $type === 'phone') {
      $query->andWhere("mobile LIKE '{$value}'");
    }

    if ($value && $type === 'document') {
      $query->andWhere("document LIKE '{$value}'");
    }

    if (isset($filters['id'])) {
      $query->andWhere("id = {$filters['id']}");
    }

    return $query->getOne();
  }

  public function list_bills(int $clienteId)
  {
    return $this->createQueryBuilder()
      ->select(
        "b.id,b.clientid,b.internal_code, b.correlative,b.date_issue,
        b.expiration_date,b.billed_month,b.subtotal,b.discount,
        b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,
        vs.serie,v.voucher,b.observation"
      )
      ->from("bills b")
      ->innerJoin("vouchers v", " b.voucherid = v.id ")
      ->innerJoin("voucher_series vs", "b.serieid = vs.id")
      ->where("b.state != 0")
      ->andWhere("b.clientid = {$clienteId}")
      ->orderBy("b.billed_month", "DESC")
      ->getMany();
  }

  public function invoice_paid(int $billId)
  {
    return $this->createQueryBuilder()
      ->select(
        "p.id, p.billid, p.userid, p.clientid, p.internal_code, p.paytypeid,
        fp.payment_type, p.payment_date, p.comment, p.amount_paid,
        p.amount_total, p.remaining_credit"
      )
      ->from("payments p")
      ->innerJoin("forms_payment fp", "p.paytypeid = fp.id")
      ->where("p.billid = {$billId}")
      ->andWhere("p.state = 1")
      ->orderBy("p.id", "DESC")
      ->setLimit(1)
      ->getOne();
  }

  public function list_incidents($filters = [])
  {
    $query = $this->createQueryBuilder()
      ->from("incidents as i");
    return $query->getMany();
  }

  public function save_incident($data = [])
  {
    $columns = [
      "userid",
      "clientid",
      "technical",
      "incidentsid",
      "description",
      "priority",
      "attention_date",
      "registration_date",
      "state"
    ];
    $this->setTableName("tickets");
    return $this->insertObject($columns, $data);
  }
}