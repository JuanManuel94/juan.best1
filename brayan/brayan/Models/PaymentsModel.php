<?php
class PaymentsModel extends Mysql
{
	private $intId, $intBusiness, $intBill, $intUser, $intClient, $strCode, $strType, $strDatetime, $strComment, $strTotal, $strSubscriber, $strRemaining, $intState, $strCodePayment, $strTypePayment;
	public function __construct()
	{
		parent::__construct();
	}

	public function list_zonas()
	{
		return $this->createQueryBuilder()
			->from("zonas")
			->getMany();
	}

	public function list_records(array $filter)
	{
		$query = $this->createQueryBuilder()
			->from("payments", "p")
			->innerJoin("forms_payment fp", "p.paytypeid = fp.id")
			->innerJoin("users u", "p.userid = u.id")
			->innerJoin("clients c", "p.clientid = c.id")
			->leftJoin("zonas z", "z.id = c.zonaid")
			->innerJoin("bills b", "p.billid = b.id")
			->innerJoin("vouchers v", "b.voucherid = v.id")
			->innerJoin("voucher_series vs", "b.serieid = vs.id")
			->select("p.clientid")
			->addSelect("p.billid", "billid")
			->addSelect("p.internal_code", "internal_code")
			->addSelect("fp.payment_type", "payment_type")
			->addSelect("p.payment_date", "payment_date")
			->addSelect("p.comment", "comment")
			->addSelect("p.amount_paid", "amount_paid")
			->addSelect("p.amount_total", "amount_total")
			->addSelect("p.remaining_credit", "remaining_credit")
			->addSelect("p.state", "state")
			->addSelect("p.id", "id")
			->addSelect("b.total", "bill_total")
			->addSelect("CONCAT_WS(' ', c.names, c.surnames)", "client")
			->addSelect("z.nombre_zona", "zona")
			->addSelect("u.names", "user")
			->addSelect("vs.serie", "serie")
			->addSelect("v.voucher", "voucher")
			->addSelect("b.correlative", "correlative");

		if (isset($filter['start'])) {
			$query->andWhere("DATE(p.payment_date) >= '{$filter['start']}'");
		}

		if (isset($filter['end'])) {
			$query->andWhere("DATE(p.payment_date) <= '{$filter['end']}'");
		}

		if (isset($filter['type'])) {
			$query->andWhere("p.paytypeid = '{$filter['type']}'");
		}

		if (isset($filter['user'])) {
			$query->andWhere("p.userid = {$filter['user']}");
		}

		if (isset($filter['state'])) {
			$query->andWhere("p.state = {$filter['state']}");
		}

		if (isset($filter['zonaId'])) {
			$query->andWhere("c.zonaid = {$filter['zonaId']}");
		}

		return $query->getMany();
	}

	public function modify(int $id, int $type, string $datetime, string $comment)
	{
		$this->intId = $id;
		$this->strType = $type;
		$this->strDatetime = $datetime;
		$this->strComment = $comment;
		$answer = "";
		$query = "UPDATE payments SET paytypeid=?,payment_date=?,comment=? WHERE id = $this->intId";
		$data = array($this->strType, $this->strDatetime, $this->strComment);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function modify_bill(int $bill)
	{
		$this->intBill = $bill;
		$answer = "";
		$query = "UPDATE bills SET state = ? WHERE id = $this->intBill";
		$data = array(1);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function contract_client(int $client)
	{
		$this->intClient = $client;
		$sql = "SELECT * FROM contracts WHERE clientid = $this->intClient";
		$answer = $this->select($sql);
		return $answer;
	}
	public function returnCode()
	{
		$sql = "SELECT COUNT(internal_code) AS code FROM payments";
		$answer = $this->select($sql);
		$code = $answer['code'];
		return $code;
	}
	public function generateCode()
	{
		$sql = "SELECT MAX(internal_code) AS code FROM payments";
		$answer = $this->select($sql);
		$code = $answer['code'];
		return $code;
	}
	public function cancel(int $id)
	{
		$this->intId = $id;
		$answer = "";
		$query = "UPDATE payments SET state = ? WHERE id = $this->intId";
		$data = array(2);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function cancel_massive(string $id)
	{
		$this->intId = $id;
		$answer = 0;
		$query = "UPDATE payments SET state = ? WHERE id = $this->intId";
		$data = array(2);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = $update;
		} else {
			$answer = 0;
		}
		return $answer;
	}
	public function pending_payments(int $id)
	{
		$this->intId = $id;
		$sql = "SELECT * FROM payments WHERE id = $this->intId AND state = 1";
		$asnwer = $this->select($sql);
		return $asnwer;
	}
	public function select_record(int $id)
	{
		$this->intId = $id;
		$sql = "SELECT p.id,p.billid,p.userid,p.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,p.internal_code,p.paytypeid,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,vs.serie,v.voucher,b.correlative
			FROM payments p
			JOIN clients c ON p.clientid = c.id
            JOIN bills b ON p.billid = b.id
            JOIN vouchers v ON b.voucherid = v.id
            JOIN voucher_series vs ON b.serieid = vs.id
			WHERE p.id = $this->intId";
		$asnwer = $this->select($sql);
		return $asnwer;
	}
	public function select_client(int $client)
	{
		$this->intClient = $client;
		$sql = "SELECT * FROM clients WHERE id = $this->intClient";
		$answer = $this->select($sql);
		return $answer;
	}
	public function payment_summary(int $year)
	{
		$arrPayments = array();
		$arrMonths = months();
		for ($i = 1; $i <= 12; $i++) {
			$data = array('year' => '', 'months' => '', 'month' => '', 'payment' => '', 'count_payment' => '', 'total' => '');
			$sql = "SELECT $year AS year, $i AS month, SUM(amount_paid) AS payment,COUNT(id) AS total
						FROM payments
						WHERE MONTH(payment_date)= $i AND YEAR(payment_date) = $year AND state = 1
						GROUP BY MONTH(payment_date) ";
			$answer = $this->select($sql);
			$data['month'] = $arrMonths[$i - 1];
			if (empty($answer)) {
				$data['year'] = $year;
				$data['months'] = $i;
				$data['payment'] = $_SESSION['businessData']['symbol'] . format_money(0);
				$data['count_payment'] = format_money(0);
				$data['total'] = 0;
			} else {
				$data['year'] = $answer['year'];
				$data['months'] = $answer['month'];
				$data['payment'] = $_SESSION['businessData']['symbol'] . format_money($answer['payment']);
				$data['count_payment'] = $answer['payment'];
				$data['total'] = $answer['total'];
			}
			array_push($arrPayments, $data);
		}
		return $arrPayments;
	}
	public function view_bill(int $bill)
	{
		$request = array();
		$sql_bill = "SELECT b.id,b.voucherid,c.names,c.surnames,dt.document AS type_doc,c.document,c.mobile,c.address,c.email,v.voucher,vs.serie,b.internal_code,b.observation,b.date_issue,b.correlative,b.expiration_date,b.subtotal,b.discount,b.total,b.type,b.sales_method,u.names AS user,b.state
			FROM bills b	JOIN clients c ON b.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN users u ON b.userid = u.id JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id = $bill";
		$request_bill = $this->select($sql_bill);
		if (!empty($request_bill)) {
			$idbill = $request_bill['id'];
			$sql_business = "SELECT b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.logo_login,b.logotyope,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,c.symbol,c.money,c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id LIMIT 0,1";
			$request_business = $this->select($sql_business);
			$sql_payment = "SELECT p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,u.names
				FROM payments p
				JOIN forms_payment fp ON p.paytypeid = fp.id
				JOIN users u ON p.userid = u.id
				WHERE p.billid = $idbill AND p.state = 1";
			$request_payment = $this->select_all($sql_payment);
			$sql_detail = "SELECT *FROM detail_bills WHERE billid = $idbill";
			$request_detail = $this->select_all($sql_detail);
			$request = array('bill' => $request_bill, 'detail' => $request_detail, 'business' => $request_business, 'payments' => $request_payment);
		}
		return $request;
	}
	public function select_bill(int $bill)
	{
		$this->intBill = $bill;
		$sql = "SELECT b.id,CONCAT_WS(' ', c.names, c.surnames) AS client,b.clientid,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.subtotal,b.discount,b.total,b.amount_paid,b.remaining_amount,b.type,b.sales_method,b.state,vs.serie,v.voucher,b.observation,b.voucherid,b.serieid
			FROM bills b
			JOIN clients c ON b.clientid = c.id
			JOIN vouchers v ON b.voucherid = v.id
			JOIN voucher_series vs ON b.serieid = vs.id
			WHERE b.id = $this->intBill";
		$asnwer = $this->select($sql);
		return $asnwer;
	}
	public function select_pending(int $bill)
	{
		$this->intBill = $bill;
		$sql = "SELECT * FROM bills WHERE id = $this->intBill";
		$asnwer = $this->select_all($sql);
		return $asnwer;
	}

	public function subtract_amounts(int $bill, string $subscriber, int $state)
	{
		$this->intBill = $bill;
		$this->strSubscriber = $subscriber;
		$this->intState = $state;
		$answer = "";
		if ($this->intState == 0) {
			$query = "UPDATE bills SET amount_paid = amount_paid - ?,remaining_amount = remaining_amount + ? WHERE id = $this->intBill";
			$data = array($this->strSubscriber, $this->strSubscriber);
		} else {
			$query = "UPDATE bills SET amount_paid = amount_paid - ?,remaining_amount = ?, state = ? WHERE id = $this->intBill";
			$data = array($this->strSubscriber, $this->strSubscriber, $this->intState);
		}
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}

	public function find_client_by_paymentIds(array $ids)
	{
		$condition = implode(", ", $ids);
		return (Object) $this->createQueryBuilder()
			->from("clients", "c")
			->innerJoin("bills b", "b.clientid = c.id")
			->innerJoin("payments p", "p.billid = b.id")
			->where("p.id IN ({$condition})")
			->select("c.id, c.names, c.surnames, c.documentid, c.document")
			->addSelect("c.mobile", "mobile")
			->addSelect("c.mobile_optional", "mobile_optional")
			->addSelect("c.email", "email")
			->addSelect("c.address", "address")
			->addSelect("c.reference", "reference")
			->addSelect("c.note", "note")
			->addSelect("c.latitud", "latitud")
			->addSelect("c.longitud", "longitud")
			->addSelect("c.state", "state")
			->addSelect("c.net_router", "net_router")
			->addSelect("c.net_name", "net_name")
			->addSelect("c.net_password", "net_password")
			->addSelect("c.net_localaddress", "net_localaddress")
			->addSelect("c.net_ip", "net_ip")
			->groupBy("c.id, c.names, c.surnames, c.documentid, c.document")
			->addGroupBy("c.mobile")
			->addGroupBy("c.mobile_optional")
			->addGroupBy("c.email")
			->addGroupBy("c.address")
			->addGroupBy("c.reference")
			->addGroupBy("c.note")
			->addGroupBy("c.latitud")
			->addGroupBy("c.longitud")
			->addGroupBy("c.state")
			->addGroupBy("c.net_router")
			->addGroupBy("c.net_name")
			->addGroupBy("c.net_password")
			->addGroupBy("c.net_localaddress")
			->addGroupBy("c.net_ip")
			->getOne();
	}

	public function search_clients(string $search)
{
	$sql = "
		SELECT cl.*
		FROM clients cl
		INNER JOIN bills b ON b.clientid = cl.id AND b.state IN (2, 3)
		WHERE CONCAT(cl.names, ' ', cl.surnames) LIKE '%$search%'
			OR cl.names LIKE '%$search%'
			OR cl.surnames LIKE '%$search%'
			OR cl.document LIKE '%$search%'
		GROUP BY cl.id
	";
	return $this->select_all($sql);
}


	public function pending_invoices(int $client)
	{
		$request = array();
		$sql_pending = "
        SELECT calc.discount - calc.last_discount AS discount_total,
        calc.* FROM (
        SELECT COUNT(*) AS total,
          COALESCE(SUM(remaining_amount),0) AS amount, 
          COALESCE(SUM(subtotal), 0) AS subtotal,
          COALESCE(SUM(discount),0) AS discount,
          COALESCE(SUM(amount_paid), 0) AS  amount_paid,
          (SELECT bb.discount
            FROM bills bb WHERE bb.clientid = b.clientid
            AND bb.state NOT IN (0,1,4) 
            order by bb.billed_month DESC
            limit 1
          ) AS last_discount
        FROM bills b WHERE state NOT IN(0,1,4)  
        AND b.clientid = $client
      ) calc";
		$request_pending = $this->select($sql_pending);
		$total = $request_pending['total'];
		if ($total >= 1) {
			$amount = $request_pending['amount'];
			$subtotal = $request_pending['subtotal'];
			$amount_paid = $request_pending['amount_paid'];
			$discount = $request_pending['discount_total'];
			$last_discount = $request_pending['last_discount'];
			$sql_client = "SELECT * FROM clients WHERE id = $client";
			$request_client = $this->select($sql_client);
			$request = [
				'pending' => $total,
				'amount' => $amount,
				'subtotal' => $subtotal,
				'amount_paid' => $amount_paid,
				'discount' => $discount,
				'last_discount' => $last_discount,
				'client' => $request_client
			];
		}
		return $request;
	}

	public function list_pendings(int $client, string $ordering)
	{
		$this->intClient = $client;
		$sql = "SELECT b.id,b.clientid,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,vs.serie,v.voucher,b.observation
      FROM bills b
      JOIN vouchers v ON b.voucherid = v.id
      JOIN voucher_series vs ON b.serieid = vs.id
      WHERE b.state NOT IN(0,1,4) AND b.clientid = $this->intClient ORDER BY b.billed_month " . $ordering;
		$asnwer = $this->select_all($sql);
		return $asnwer;
	}

	public function mass_payments(int $bill, int $user, int $client, string $code, int $type, string $datetime, string $comment, string $subscriber, string $total_paid, string $remaining, int $state)
	{
		$this->intBill = $bill;
		$this->intUser = $user;
		$this->intClient = $client;
		$this->strCodePayment = $code;
		$this->strTypePayment = $type;
		$this->strDatetime = $datetime;
		$this->strComment = $comment;
		$this->strSubscriber = $subscriber;
		$this->strTotal = $total_paid;
		$this->strRemaining = $remaining;
		$this->intState = $state;
		$answer = 0;
		$query = "INSERT INTO payments(billid,userid,clientid,internal_code,paytypeid,payment_date,comment,amount_paid,amount_total,remaining_credit, state) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
		$data = array($this->intBill, $this->intUser, $this->intClient, $this->strCodePayment, $this->strTypePayment, $this->strDatetime, $this->strComment, $this->strSubscriber, $this->strTotal, $this->strRemaining, $this->intState);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = $insert;
		} else {
			$answer = 0;
		}
		return $answer;
	}

	public function returnCodePayment()
	{
		$sql = "SELECT COUNT(internal_code) AS code FROM payments";
		$answer = $this->select($sql);
		$code = $answer['code'];
		return $code;
	}

	public function generateCodePayment()
	{
		$sql = "SELECT MAX(internal_code) AS code FROM payments";
		$answer = $this->select($sql);
		$code = $answer['code'];
		return $code;
	}

	public function amount_paid(int $bill)
	{
		$this->intBill = $bill;
		$sql = "SELECT amount_paid AS total FROM bills WHERE id = $this->intBill";
		$answer = $this->select($sql);
		$total = $answer['total'];
		return $total;
	}

	public function modify_amounts(string $id, string $subscriber, string $remaining, int $state, int $discount)
	{
		$this->intBill = $id;
		$this->strSubscriber = $subscriber;
		$this->strRemaining = $remaining;
		$this->intState = $state;
		$answer = "";

		if ($this->intState == 0) {
			$query = "UPDATE bills SET discount = ?, total = subtotal - ?, amount_paid = amount_paid + ?, remaining_amount = ? WHERE id = $this->intBill";
			$data = array($discount, $discount, $this->strSubscriber, $this->strRemaining);
		} else {
			$query = "UPDATE bills SET discount = ?, total = subtotal - ?, amount_paid = amount_paid + ?, remaining_amount = ?,state = ? WHERE id = $this->intBill";
			$data = array($discount, $discount, $this->strSubscriber, $this->strRemaining, $this->intState);
		}

		$update = $this->update($query, $data);

		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}

		return $answer;
	}

	public function massive_bills($bills)
	{
		$request = array();
		$listBills = "";
		$n = count($bills);
		for ($i = 0; $i < $n; $i++) {
			if ($i != ($n - 1)) {
				$listBills = $listBills . "'" . $bills[$i] . "',";
			} else {
				$listBills = $listBills . "'" . $bills[$i] . "'";
			}
		}
		$sql_bill = "SELECT b.id,b.clientid,b.voucherid,b.serieid,c.names,c.surnames,dt.document AS type_doc,c.document,c.mobile,c.mobile_optional,c.address,c.email,v.voucher,vs.serie,b.internal_code,b.observation,b.date_issue,b.billed_month,b.correlative,b.expiration_date,b.promise_date,b.subtotal,b.discount,b.total,b.amount_paid,b.remaining_amount,b.type,b.sales_method,b.state
			FROM bills b JOIN clients c ON b.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN users u ON b.userid = u.id JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id IN(" . $listBills . ")";
		$request_bill = $this->select_all($sql_bill);
		if (!empty($request_bill)) {
			$sql_business = "SELECT b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.logo_login,b.logotyope,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,c.symbol,c.money,c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id LIMIT 1";
			$request_business = $this->select($sql_business);
			for ($i = 0; $i < count($request_bill); $i++) {
				$idbill = $request_bill[$i]['id'];
				$sql_payment = "SELECT p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,u.names
					FROM payments p
					JOIN forms_payment fp ON p.paytypeid = fp.id
					JOIN users u ON p.userid = u.id
					WHERE p.billid = $idbill AND p.state = 1";
				$request_payment = $this->select_all($sql_payment);
				$sql_atm = "SELECT u.names AS user FROM payments p JOIN users u ON p.userid = u.id WHERE p.billid = $idbill ORDER BY p.id DESC LIMIT 1";
				$request_atm = $this->select($sql_atm);
				$sql_detail = "SELECT *FROM detail_bills WHERE billid = $idbill";
				$request_detail = $this->select_all($sql_detail);
				$request_bill[$i]['payments'] = $request_payment;
				$request_bill[$i]['details'] = $request_detail;
				$request_bill[$i]['atm'] = $request_atm;
			}
			$request = array('bills' => $request_bill, 'business' => $request_business);
		}
		return $request;
	}
}
