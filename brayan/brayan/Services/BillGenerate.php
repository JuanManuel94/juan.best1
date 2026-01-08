<?php

class BillGenerate
{
  private Mysql $mysql;
  private string $issue;

  public function __construct()
  {
    $this->mysql = new Mysql();
    $this->issue = date("Y-m-d");
  }

  public function generate($filters = [])
  {
    $result = 0;
    $data = $this->customers_invoice($filters);

    if (!count($data)) {
      throw new Exception("No se pudo generar");
    }

    // iterar bills
    foreach ($data as $item) {
      $idclient = $item['clientid'];
      $payday = str_pad($item['payday'], 2, "0", STR_PAD_LEFT);

      $current = date("Y-m-" . $payday);
      $current = new DateTime($this->issue);
      $current->setDate($current->format('Y'), $current->format("m"), $payday);
      $expiration = new DateTime($current->format("Y-m-d"));
      $expiration->modify("+1 month");

      $voucher = 1;
      $serie = 1;
      $row = $this->returnCode();
      // generar code
      if ($row == 0) {
        $code = "V00001";
      } else {
        $max = $this->generateCode();
        $code = "V" . substr((substr($max, 1) + 100001), 1);
      }
      // generar code relativo
      $num_corre = $this->returnCorrelative($voucher, $serie);
      if (empty($num_corre)) {
        $correlative = 1;
      } else {
        $correlative = $this->returnUsed($voucher, $serie);
      }
      // obtener inforación de servicios
      $service = new BillInfoService();
      $info = $service->execute($idclient);
      // obtener el total
      $total = $info['total'];
      $request = $this->invoice_receipts(1, 1, $idclient, $voucher, $serie, $code, $correlative, $this->issue, $expiration->format("Y-m-d"), $current->format("Y-m-d"), $total, 0, $total, 2, 2, "", 2);
      if ($request > 0) {
        $idbill = $this->returnBill();
        $this->modify_available($voucher, $serie);
        // obtener data
        $details = $info['details'];
        foreach ($details as $detail) {
          $this->create_datail(
            $idbill,
            2,
            $detail['id'],
            $detail['service'],
            1,
            $detail['price'],
            $detail['price']
          );
        }
      }
      // aumentar contador
      $result = $result + $request;
    }
    // validar result
    return $result;
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function getMysql()
  {
    return $this->mysql;
  }

  public function setIssue(string $issue)
  {
    $this->issue = $issue;
  }

  private function customers_invoice($filters = [])
  {
    $query = $this->mysql->createQueryBuilder()
      ->from("contracts c")
      ->innerJoin("clients cl", "c.clientid = cl.id")
      ->where("c.state NOT IN(4)")
      ->select("c.id, c.clientid, c.payday, cl.names, cl.surnames");

    // filter dates
    if (isset($filters['year']) && isset($filters['month'])) {
      $year = $filters['year'];
      $month = $filters['month'];
      $query->andWhere("c.clientid NOT IN(SELECT b.clientid FROM bills b WHERE MONTH(b.billed_month) = {$month} AND YEAR(b.billed_month) = {$year} AND b.state != 4 AND b.type = 2)");
    }

    // filter client
    if (isset($filters['clientId'])) {
      $query->andWhere("c.clientid = {$filters['clientId']}");
    }

    // response
    return $query->getMany();
  }

  private function returnCode()
  {
    $sql = "SELECT COUNT(internal_code) AS code FROM bills";
    $answer = $this->mysql->select($sql);
    $code = $answer['code'];
    return $code;
  }

  private function generateCode()
  {
    $sql = "SELECT MAX(internal_code) AS code FROM bills";
    $answer = $this->mysql->select($sql);
    $code = $answer['code'];
    return $code;
  }

  private function returnCorrelative(int $idvoucher, int $idserie)
  {
    $sql = "SELECT MAX(correlative) as correlative FROM bills WHERE serieid = $idserie AND voucherid = $idvoucher";
    $answer = $this->mysql->select($sql);
    $correlative = $answer['correlative'];
    return $correlative;
  }

  private function returnUsed(int $idvoucher, int $idserie)
  {
    $sql = "SELECT until - available + 1 AS used FROM voucher_series WHERE id = $idserie AND voucherid = $idvoucher";
    $answer = $this->mysql->select($sql);
    $used = $answer['used'];
    return $used;
  }

  private function invoice_receipts(int $business, int $user, int $client, int $voucher, int $serie, string $code, string $correlative, string $issue, string $expiration, string $current, string $subtotal, string $discount, string $total, int $type, int $method, string $observation, string $state)
  {
    $answer = 0;
    $query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation,state, amount_paid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $data = array($user, $client, $voucher, $serie, $code, $correlative, $issue, $expiration, $current, $subtotal, $discount, $total, $total, $type, $method, $observation, $state, $discount);
    $insert = $this->mysql->insert($query, $data);
    if ($insert) {
      $answer = $insert;
    } else {
      $answer = 0;
    }
    return $answer;
  }

  private function returnBill()
  {
    $sql = "SELECT MAX(id) AS id FROM bills";
    $answer = $this->mysql->select($sql);
    $bill = $answer['id'];
    return $bill;
  }

  private function modify_available(int $idvoucher, int $idserie)
  {
    $answer = "";
    $query = "UPDATE voucher_series SET available = available - ? WHERE id = $idserie AND voucherid = $idvoucher";
    $data = array(1);
    $update = $this->mysql->update($query, $data);
    return $update ? true : false;
  }

  private function select_detail_contract(int $contract)
  {
    $sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,s.service
    FROM detail_contracts dc
    JOIN services s ON dc.serviceid = s.id
    WHERE dc.contractid = $contract";
    $answer = $this->mysql->select_all($sql);
    return $answer;
  }

  private function create_datail(int $id, int $type, int $serpro, string $description, string $quantity, string $price, string $total)
  {
    $answer = "";
    $query = "INSERT INTO detail_bills(billid,type,serproid,description,quantity,price,total) VALUES(?,?,?,?,?,?,?)";
    $data = array($id, $type, $serpro, $description, $quantity, $price, $total);
    $insert = $this->mysql->insert($query, $data);
    return $insert ? true : false;
  }
}