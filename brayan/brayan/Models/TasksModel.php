<?php
class TasksModel extends Mysql
{
  public function __construct()
  {
    parent::__construct();
  }
  public function customers_invoice(string $month, string $year)
  {
    $sql = "SELECT c.id,c.clientid,c.payday,cl.names,cl.surnames
			FROM contracts c
			JOIN clients cl ON c.clientid = cl.id
			WHERE c.state NOT IN(1,3,4,5) AND c.clientid NOT IN(SELECT b.clientid FROM bills b WHERE MONTH(b.billed_month) = $month AND YEAR(b.billed_month) = $year AND b.state != 4 AND b.type = 2)";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function service_amount(int $contract)
  {
    $sql = "SELECT COALESCE(SUM(price),0) as total FROM detail_contracts WHERE contractid = $contract";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function returnCode()
  {
    $sql = "SELECT COUNT(internal_code) AS code FROM bills";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function generateCode()
  {
    $sql = "SELECT MAX(internal_code) AS code FROM bills";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function returnCorrelative(int $idvoucher, int $idserie)
  {
    $sql = "SELECT MAX(correlative) as correlative FROM bills WHERE serieid = $idserie AND voucherid = $idvoucher";
    $answer = $this->select($sql);
    $correlative = $answer['correlative'];
    return $correlative;
  }
  public function returnUsed(int $idvoucher, int $idserie)
  {
    $sql = "SELECT until - available + 1 AS used FROM voucher_series WHERE id = $idserie AND voucherid = $idvoucher";
    $answer = $this->select($sql);
    $used = $answer['used'];
    return $used;
  }
  public function returnBill()
  {
    $sql = "SELECT MAX(id) AS id FROM bills";
    $answer = $this->select($sql);
    $bill = $answer['id'];
    return $bill;
  }
  public function invoice_receipts(int $business, int $user, int $client, int $voucher, int $serie, string $code, string $correlative, string $issue, string $expiration, string $current, string $subtotal, string $discount, string $total, int $type, int $method, string $observation, string $state)
  {
    $answer = 0;
    $query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation,state, amount_paid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $data = array($user, $client, $voucher, $serie, $code, $correlative, $issue, $expiration, $current, $subtotal, $discount, $total, $total, $type, $method, $observation, $state, $discount);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = $insert;
    } else {
      $answer = 0;
    }
    return $answer;
  }
  public function modify_available(int $idvoucher, int $idserie)
  {
    $answer = "";
    $query = "UPDATE voucher_series SET available = available - ? WHERE id = $idserie AND voucherid = $idvoucher";
    $data = array(1);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function create_datail(int $id, int $type, int $serpro, string $description, string $quantity, string $price, string $total)
  {
    $answer = "";
    $query = "INSERT INTO detail_bills(billid,type,serproid,description,quantity,price,total) VALUES(?,?,?,?,?,?,?)";
    $data = array($id, $type, $serpro, $description, $quantity, $price, $total);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function select_detail_contract(int $contract)
  {
    $sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,s.service
			FROM detail_contracts dc
			JOIN services s ON dc.serviceid = s.id
			WHERE dc.contractid = $contract";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function pending_bills()
  {
    $sql = "SELECT * FROM bills WHERE state = 2 ORDER BY id ASC";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function expired_bills(int $bill)
  {
    $answer = 0;
    $query = "UPDATE bills SET state = ? WHERE id = $bill";
    $data = array(3);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = $update;
    } else {
      $answer = 0;
    }
    return $answer;
  }
  public function pending_tickets()
  {
    $sql = "SELECT * FROM tickets WHERE state = 2 ORDER BY id ASC";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function expired_tickets(int $ticket)
  {
    $answer = 0;
    $query = "UPDATE tickets SET state = ? WHERE id = $ticket";
    $data = array(5);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = $update;
    } else {
      $answer = 0;
    }
    return $answer;
  }

  public function bill_voucher(int $id)
  {
    $request = array();
    $sql_bill = "SELECT b.id,b.clientid,b.voucherid,b.serieid,c.names,c.surnames,
        dt.document AS type_doc,c.document,c.mobile,c.address,c.email,v.voucher,
        vs.serie,b.internal_code,b.observation,b.date_issue,b.billed_month,
        b.correlative,b.expiration_date,b.subtotal,b.discount,b.total,b.amount_paid,
        b.remaining_amount,b.type,b.sales_method,b.state
        FROM bills b JOIN clients c ON b.clientid = c.id 
        JOIN document_type dt ON c.documentid = dt.id 
        JOIN users u ON b.userid = u.id 
        JOIN vouchers v ON b.voucherid = v.id 
        JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id = $id";
    $request_bill = $this->select($sql_bill);
    if (!empty($request_bill)) {
      $sql_business = "SELECT b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.logo_login,b.logotyope,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,c.symbol,c.money,c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id LIMIT 1";
      $request_business = $this->select($sql_business);
      $sql_atm = "SELECT u.names AS user FROM payments p JOIN users u ON p.userid = u.id WHERE p.billid = $id  ORDER BY p.id DESC LIMIT 1";
      $request_atm = $this->select($sql_atm);
      $sql_payment = "SELECT p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,u.names FROM payments p JOIN forms_payment fp ON p.paytypeid = fp.id JOIN users u ON p.userid = u.id WHERE p.billid = $id AND p.state = 1";
      $request_payment = $this->select_all($sql_payment);
      $sql_detail = "SELECT *FROM detail_bills WHERE billid = $id";
      $request_detail = $this->select_all($sql_detail);
      $request = array('bill' => $request_bill, 'detail' => $request_detail, 'business' => $request_business, 'payments' => $request_payment, 'atm' => $request_atm);
    }
    return $request;
  }

  public function bill_voucheres($filters = [])
  {
    $query = $this->createQueryBuilder()
      ->from("bills b")
      ->select(implode(", ", [
        "b.id",
        "b.clientid",
        "b.voucherid",
        "b.serieid",
        "c.names",
        "c.surnames",
        "dt.document AS type_doc",
        "c.document",
        "c.mobile",
        "c.address",
        "c.email",
        "v.voucher",
        "vs.serie",
        "b.internal_code",
        "b.observation",
        "b.date_issue",
        "b.billed_month",
        "b.correlative",
        "b.expiration_date",
        "b.subtotal",
        "b.discount",
        "b.total",
        "b.amount_paid",
        "b.remaining_amount",
        "b.type",
        "b.sales_method",
        "b.state",
      ]))
      ->innerJoin("clients c", "b.clientid = c.id")
      ->innerJoin("document_type dt", "c.documentid = dt.id")
      ->innerJoin("users u", "b.userid = u.id")
      ->innerJoin("vouchers v", "b.voucherid = v.id")
      ->innerJoin("voucher_series vs", "b.serieid = vs.id");

    if (isset($filters['year'])) {
      $condition = $filters['year'];
      $query->andWhere("YEAR(b.billed_month) = {$condition}");
    }

    if (isset($filters['month'])) {
      $condition = $filters['month'];
      $query->andWhere("MONTH(b.billed_month) = {$condition}");
    }

    if (isset($filters['email:required'])) {
      $query->andWhere("(TRIM(c.email) <> '')");
    }

    $bills = $query->getMany();
    $result = [];

    foreach ($bills as $i => $bill) {
      $business = $this->createQueryBuilder()
        ->select("b.*, c.symbol, c.money, c.money_plural")
        ->from("business b")
        ->innerJoin("currency c", "b.currencyid = c.id")
        ->getOne();
      $atm = $this->createQueryBuilder()
        ->select("u.names AS user")
        ->from("payments p")
        ->innerJoin("users u", "p.userid = u.id")
        ->andWhere("p.billid = {$bill['id']}")
        ->orderBy('p.id', 'DESC')
        ->getOne();
      $payments = $this->createQueryBuilder()
        ->select(implode(", ", [
          "p.internal_code",
          "fp.payment_type",
          "p.payment_date",
          "p.comment",
          "p.amount_paid",
          "p.amount_total",
          "p.remaining_credit",
          "u.names"
        ]))
        ->from("payments p")
        ->innerJoin("forms_payment fp", "p.paytypeid = fp.id")
        ->innerJoin("users u", "p.userid = u.id")
        ->andWhere("p.billid = {$bill['id']}")
        ->andWhere("p.state = 1")
        ->getMany();
      $detail = $this->createQueryBuilder()
        ->from("detail_bills")
        ->where("billid = {$bill['id']}")
        ->getMany();

      array_push($result, [
        "bill" => $bill,
        "business" => $business,
        "atm" => $atm,
        "payments" => $payments,
        "detail" => $detail
      ]);
    }

    return $result;
  }
}
