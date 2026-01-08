<?php
class BillsModel extends Mysql
{
	private $intId, $intBusiness, $intUser, $intClient, $intVoucher, $intSerie, $strCode, $strCorrelative, $strIssue, $strExpiration, $strCurrent;
	private $strSubtotal, $strDiscount, $strTotal, $strType, $strObservation;
	private $intIdDetail, $strTypeD, $intSerpro, $strDescription, $strQuantity, $strPrice, $strTotalD, $strDate, $intState;
	private $intPayment, $strCodePayment, $strTypePayment, $strOperation, $strBank, $strComment, $strBalance, $intContract;
	private $strNames, $strSurnames, $intType, $strDocument, $strMobile, $strEmail, $strAddess, $strReference;
	private $intMonth, $intYear, $intMethod, $strMonth, $strSubscriber, $strTotalPaid, $strRemaining, $strTicketNumber, $strReferenceNumber;
	public function __construct()
	{
		parent::__construct();
	}

	function checkTicketNumber($ticket_number)
	{
		$sql = "SELECT * FROM payments WHERE ticket_number = '$ticket_number'";
		$exists = $this->select($sql);
		return $exists;
	}

	public function list_records(string $start, string $end, string $state)
	{
		if (isset($start) && isset($end) && isset($state) && !empty($start) && !empty($end) && !empty($state)) {
			$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client, z.nombre_zona zona, b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,vs.serie,v.voucher,b.observation,b.promise_enabled,b.promise_date,b.promise_set_date,b.promise_comment
						FROM bills b
						JOIN clients c ON b.clientid = c.id
						LEFT JOIN zonas z ON z.id = c.zonaid
						JOIN vouchers v ON b.voucherid = v.id
						JOIN voucher_series vs ON b.serieid = vs.id
						WHERE b.state != 0 AND DATE(b.date_issue) >= '$start' AND DATE(b.date_issue) <= '$end' AND b.state = $state ORDER BY b.id DESC";
		} elseif (isset($start) && isset($end) && empty($state)) {
			$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client, z.nombre_zona zona, b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,vs.serie,v.voucher,b.observation,b.promise_enabled,b.promise_date,b.promise_set_date,b.promise_comment
						FROM bills b
						JOIN clients c ON b.clientid = c.id
						LEFT JOIN zonas z ON z.id = c.zonaid
						JOIN vouchers v ON b.voucherid = v.id
						JOIN voucher_series vs ON b.serieid = vs.id
						WHERE b.state != 0 AND DATE(b.date_issue) >= '$start' AND DATE(b.date_issue) <= '$end' ORDER BY b.id DESC";
		} else {
			$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client, z.nombre_zona zona, b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,vs.serie,v.voucher,b.observation,b.promise_enabled,b.promise_date,b.promise_set_date,b.promise_comment
						FROM bills b
						JOIN clients c ON b.clientid = c.id
						LEFT JOIN zonas z ON z.id = c.zonaid
						JOIN vouchers v ON b.voucherid = v.id
						JOIN voucher_series vs ON b.serieid = vs.id
						WHERE b.state != 0 ORDER BY b.id DESC";
		}
		$asnwer = $this->select_all($sql);
		return $asnwer;
	}
	public function list_pendings()
	{
		$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.remaining_amount,b.amount_paid,b.type,b.sales_method,b.state,vs.serie,v.voucher,b.observation,b.promise_enabled,b.promise_date,b.promise_set_date,b.promise_comment
			FROM bills b
			JOIN clients c ON b.clientid = c.id
			JOIN vouchers v ON b.voucherid = v.id
			JOIN voucher_series vs ON b.serieid = vs.id
			WHERE b.state NOT IN(0,1,4) ORDER BY b.id DESC";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function list_clients_free()
	{
		$sql = "SELECT *FROM clients WHERE state != 0 ORDER BY id ASC";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function list_clients_contract()
	{
		$sql = "SELECT *FROM clients WHERE state != 0 AND id IN (SELECT clientid FROM contracts WHERE state NOT IN(4,5))";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function export()
	{
		$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.type,b.state,vs.serie,v.voucher,b.observation
			FROM bills b
			JOIN clients c ON b.clientid = c.id
			JOIN vouchers v ON b.voucherid = v.id
			JOIN voucher_series vs ON b.serieid = vs.id
			WHERE b.state != 0 ORDER BY b.id ASC";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function export_pendings()
	{
		$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.remaining_amount,b.amount_paid,b.type,b.sales_method,b.state,vs.serie,v.voucher,b.observation
			FROM bills b
			JOIN clients c ON b.clientid = c.id
			JOIN vouchers v ON b.voucherid = v.id
			JOIN voucher_series vs ON b.serieid = vs.id
			WHERE b.state NOT IN(0,1,4) ORDER BY b.id ASC";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function invoice_paid(int $bill)
	{
		$this->intId = $bill;
		$sql = "SELECT p.id,p.billid,p.userid,p.clientid,p.internal_code,p.paytypeid,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit
			FROM payments p
			JOIN forms_payment fp ON p.paytypeid = fp.id
			WHERE p.billid = $this->intId AND p.state = 1 ORDER BY p.id DESC LIMIT 1";
		$answer = $this->select($sql);
		return $answer;
	}
	public function register_email(int $idclient, int $idbill, string $affair, string $sender, string $files, string $type_file, string $template_email, string $datetime, int $state_email)
	{
		$answer = '';
		$query = "INSERT INTO emails(clientid,billid,affair,sender,files,type_file,template_email,registration_date,state) VALUES(?,?,?,?,?,?,?,?,?)";
		$data = array($idclient, $idbill, $affair, $sender, $files, $type_file, $template_email, $datetime, $state_email);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function select_invoice(int $client)
	{
		/* VARIABLE ARRAY  PARA ALMACENAR LA INF QUE SE MANDARA A LA VISTA */
		$request = array();
		/* CONSULTA PARA OBTENER LA INFO DEL CLIENTE */
		$sql_client = "SELECT * FROM clients WHERE id = $client";
		$request_client = $this->select($sql_client);
		if (!empty($request_client)) {
			/* TOTAL DE FACTURAS */
			$sql_validate = "SELECT COUNT(*) AS total FROM bills WHERE clientid = $client AND state != 4 AND type = 2";
			$request_validate = $this->select($sql_validate);
			/* NOMBRE DEL CLIENTE */
			$full_name = $request_client['names'] . " " . $request_client['surnames'];
			/* DOCUMENTO DE IDENTIDAD DEL CLIENTE */
			$document = $request_client['document'];
			/* DATOS DEL CONTRATO */
			$sql_contract = "SELECT * FROM contracts WHERE clientid = $client";
			$request_contract = $this->select($sql_contract);
			if (empty($request_contract)) {
				$request = array('invoice' => "", 'detail' => "", 'service' => "");
			} else {
				/* OBTENEMOS EL ID DEL CONTRATO */
				$idcontract = $request_contract['id'];
				/* VALIDAMOS SI EL CLIENTE TIENE DESCUETO */
				if ($request_contract['discount'] == 1) {
					$discount = $request_contract['discount_price'];
				} else {
					$discount = 0;
				}
				/* CONSULTA PARA OBTENER SI TIENE SERVIOS EL CLIENTE */
				$sql_services = "SELECT COUNT(serviceid) AS serivices FROM detail_contracts WHERE contractid = $idcontract";
				$request_services = $this->select($sql_services);
				$services = $request_services['serivices'];
				if ($request_validate['total'] >= 1) {
					/* CONSULTA PARA OBTENER LA INFORMACION DE LA FACTURA */
					$sql_bill = "SELECT * FROM bills WHERE clientid = $client AND state != 4 AND type = 2 ORDER BY id DESC LIMIT 1";
					$request_bill = $this->select($sql_bill);
					/* CONSULTA PARA OBTENER LOS SERVICOS DEL CLIENTE */
					$sql_detail = "SELECT s.id,s.service,s.price
				    FROM detail_contracts dc
				    JOIN services s ON dc.serviceid = s.id
				    WHERE dc.contractid = $idcontract";
					$request_detail = $this->select_all($sql_detail);
					/* FECHA DE VENCIMIENTO DE LA FACTURA */
					$expiration = date("d/m/Y", strtotime($request_bill['expiration_date'] . " + 1 month"));
					/* OBTENER MES EN LETRAS */
					$months = months();
					$month = $months[date('n', strtotime($request_bill['expiration_date'])) - 1];
					/* MES DE FACTURACION */
					$billed_month = date("d/m/Y", strtotime($request_bill['expiration_date']));
					/* MODIFICAR EL COSTO DEL SERVCIO POR LOS DIAS A UTILIZAR */
					if (count($request_detail) > 0) {
						for ($i = 0; $i < count($request_detail); $i++) {
							$request_detail[$i]['service'] = "SERVICIO DE " . $request_detail[$i]['service'] . ", MES DE " . strtoupper($month);
						}
					}
					/* ARRAY DE DATOS GENERALES */
					$invoice = array(
						"idclient" => $client,
						"client" => $full_name,
						"document" => $document,
						"expiration" => $expiration,
						"billed_month" => $billed_month,
						"discount" => $discount,
						"status" => $request_contract['state']
					);
					/* ARRAY CONTENIENDO TODA LA INFO PARA LA VISTA */
					$request = array('invoice' => $invoice, 'detail' => $request_detail, 'service' => $services);
				} else {
					/* CONSULTA PARA OBTENER LOS SERVICOS DEL CLIENTE */
					$sql_detail = "SELECT s.id,s.service,s.price
				     FROM detail_contracts dc
				     JOIN services s ON dc.serviceid = s.id
				     WHERE dc.contractid = $idcontract";
					$request_detail = $this->select_all($sql_detail);
					/* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL MES */
					$month_contract = date("m", strtotime($request_contract['contract_date']));
					/* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL AÑO */
					$year_contract = date("Y", strtotime($request_contract['contract_date']));
					/* CONSULTA PARA OBTENER LA INSTALACION DEL CLIENTE */
					$sql_facility = "SELECT MAX(attention_date) AS attention_date FROM facility WHERE clientid = $client AND state != 5";
					$request_facility = $this->select($sql_facility);
					//VALIDAR SI EXISTE INSTALACION
					if (!empty($request_facility['attention_date'])) {
						/* FECHA DE INSTALACION */
						$date_facility = new DateTime($request_facility['attention_date']);
						/* OBTENER TOTAL DE DIAS DEL MES */
						$total_day = cal_days_in_month(CAL_GREGORIAN, $month_contract, $year_contract);
						/* ULTIMO DIA DEL MES EN FORMATO FECHA */
						$date_lastday = $year_contract . "-" . $month_contract . "-" . $total_day;
						$last_day = new DateTime($date_lastday);
						/* OBETENR LOS DIAS DE DIERENCIA ENTRE EL CONTRATO Y ULTIMO DIA DEL MES */
						$diff = $last_day->diff($date_facility);
						$used_days = ($diff->invert == 1) ? $diff->days : $diff->days;
					}
					/* DIA DE PAGO */
					$payday = str_pad($request_contract['payday'], 2, "0", STR_PAD_LEFT);
					/* FECHA DE VENCIMIENTO DE LA FACTURA */
					$date_payday = date("Y-m-" . $payday);
					$billed_month = date("d/m/Y", strtotime($date_payday));
					$date_exp = date("Y-m-d", strtotime($date_payday . " + 1 months"));
					$expiration = date("d/m/Y", strtotime($date_payday . " + 1 months"));
					/* OBTENER MES EN LETRAS */
					$months = months();
					$month = $months[date('n', strtotime($date_payday)) - 1];
					/* MODIFICAR EL COSTO DEL SERVCIO POR LOS DIAS A UTILIZAR */
					if (count($request_detail) > 0) {
						for ($i = 0; $i < count($request_detail); $i++) {
							if (!empty($request_facility['attention_date'])) {
								$cost_day = $request_detail[$i]['price'] / $total_day;
								$price_prorrateado = $cost_day * $used_days;
								$request_detail[$i]['price'] = round($price_prorrateado);
								$request_detail[$i]['service'] = "SERVICIO DE " . $request_detail[$i]['service'] . ", MES DE " . strtoupper($month) . " PRORRATEADO";
							} else {
								$request_detail[$i]['service'] = "SERVICIO DE " . $request_detail[$i]['service'] . ", MES DE " . strtoupper($month);
							}
						}
					}
					/* ARRAY DE DATOS GENERALES */
					$invoice = array(
						"idclient" => $client,
						"client" => $full_name,
						"document" => $document,
						"expiration" => $expiration,
						"billed_month" => $billed_month,
						"discount" => $discount,
						"status" => $request_contract['state']
					);
					/* ARRAY CONTENIENDO TODA LA INFO PARA LA VISTA */
					$request = array('invoice' => $invoice, 'detail' => $request_detail, 'service' => $services);
				}
			}
		}
		return $request;
	}

	public function edit_bill(string $id, array $payload)
	{
		return $this->createQueryBuilder()
			->update()
			->from("bills")
			->set($payload)
			->where("id = {$id}")
			->execute();
	}

	public function view_bill(int $id)
	{
		$request = array();
		$sql_bill = "SELECT b.id,b.clientid,b.voucherid,b.serieid,c.names,c.surnames,dt.document AS type_doc,c.document,c.mobile,c.mobile_optional,c.address,c.email,v.voucher,vs.serie,b.internal_code,b.observation,b.date_issue,b.billed_month,b.correlative,b.expiration_date,b.subtotal,b.discount,b.total,b.amount_paid,b.remaining_amount,b.type,b.sales_method,b.state
			FROM bills b JOIN clients c ON b.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN users u ON b.userid = u.id JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id = $id";
		$request_bill = $this->select($sql_bill);
		if (!empty($request_bill)) {
			$sql_business = "SELECT b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.logo_login,b.logotyope,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,c.symbol,c.money,c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id LIMIT 1";
			$request_business = $this->select($sql_business);
			$sql_payment = "SELECT p.id, p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,u.names
				FROM payments p
				JOIN forms_payment fp ON p.paytypeid = fp.id
				JOIN users u ON p.userid = u.id
				WHERE p.billid = $id AND p.state = 1";
			$request_payment = $this->select_all($sql_payment);
			$sql_atm = "SELECT u.names AS user FROM payments p JOIN users u ON p.userid = u.id WHERE p.billid = $id  ORDER BY p.id DESC LIMIT 1";
			$request_atm = $this->select($sql_atm);
			$sql_detail = "SELECT *FROM detail_bills WHERE billid = $id";
			$request_detail = $this->select_all($sql_detail);
			$request = array('bill' => $request_bill, 'detail' => $request_detail, 'business' => $request_business, 'payments' => $request_payment, 'atm' => $request_atm);
		}
		return $request;
	}
	public function voided_payments(int $id)
	{
		$this->intId = $id;
		$sql = "SELECT COUNT(*) AS total FROM payments WHERE billid = $this->intId AND state = 1";
		$answer = $this->select($sql);
		$total = $answer['total'];
		return $total;
	}

	public function find_client($clientId)
	{
		return (Object) $this->createQueryBuilder()
			->from("clients")
			->select("*")
			->where("id = {$clientId}")
			->getOne();
	}

	public function total_paid(int $id)
	{
		$this->intId = $id;
		$sql = "SELECT COALESCE(SUM(amount_paid),0) AS amount_paid FROM payments WHERE billid = $this->intId AND state = 1";
		$answer = $this->select($sql);
		$amount_paid = $answer['amount_paid'];
		return $amount_paid;
	}
	public function last_payment(int $id)
	{
		$this->intId = $id;
		$sql = "SELECT MAX(id) AS id FROM payments WHERE billid = $this->intId AND state = 1";
		$answer = $this->select($sql);
		$id = $answer['id'];
		return $id;
	}
	public function select_record(int $id)
	{
		$this->intId = $id;
		$sql = "SELECT b.id,b.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.subtotal,b.discount,b.total,b.amount_paid,b.remaining_amount,b.type,b.sales_method,b.billed_month,b.state,vs.serie,v.voucher,b.observation,b.voucherid,b.serieid,b.promise_enabled,b.promise_date,b.promise_set_date,b.promise_comment
            FROM bills b
			JOIN clients c ON b.clientid = c.id
            JOIN vouchers v ON b.voucherid = v.id
            JOIN voucher_series vs ON b.serieid = vs.id
            WHERE b.id = $this->intId";
		$answer = $this->select($sql);
		return $answer;
	}
	public function existing_client(string $names, string $surnames)
	{
		$sql = "SELECT * FROM clients WHERE names = '$names' AND surnames = '$surnames'";
		$answer = $this->select($sql);
		return $answer;
	}
	public function select_contract(int $client)
	{
		$this->intClient = $client;
		$sql = "SELECT * FROM contracts WHERE clientid = $this->intClient";
		$answer = $this->select($sql);
		return $answer;
	}
	public function select_detail_contract(int $contract)
	{
		$this->intContract = $contract;
		$sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,s.service
				FROM detail_contracts dc
				JOIN services s ON dc.serviceid = s.id
				WHERE dc.contractid = $this->intContract";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function select_client(int $client)
	{
		$this->intClient = $client;
		$sql = "SELECT * FROM clients WHERE id = $this->intClient";
		$answer = $this->select($sql);
		return $answer;
	}
	public function consult_departure(int $bill)
	{
		$this->intId = $bill;
		$sql = "SELECT *FROM departures WHERE billid = $this->intId";
		$asnwer = $this->select_all($sql);
		return $asnwer;
	}
	public function returnVoucher(int $idvoucher)
	{
		$this->intVoucher = $idvoucher;
		$sql = "SELECT voucher FROM vouchers WHERE id = $this->intVoucher";
		$answer = $this->select($sql);
		$voucher = $answer['voucher'];
		return $voucher;
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
		$this->intVoucher = $idvoucher;
		$this->intSerie = $idserie;
		$sql = "SELECT MAX(correlative) as correlative FROM bills WHERE serieid = $this->intSerie AND voucherid = $this->intVoucher";
		$answer = $this->select($sql);
		$correlative = $answer['correlative'];
		return $correlative;
	}
	public function returnUsed(int $idvoucher, int $idserie)
	{
		$this->intVoucher = $idvoucher;
		$this->intSerie = $idserie;
		$sql = "SELECT until - available + 1 AS used FROM voucher_series WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
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
	public function returnCodePayment()
	{
		$sql = "SELECT COUNT(internal_code) AS code FROM payments";
		$answer = $this->select($sql);
		$code = $answer['code'];
		return $code;
	}
	public function returnClient()
	{
		$sql = "SELECT MAX(id) AS id FROM clients";
		$answer = $this->select($sql);
		$clinet = $answer['id'];
		return $clinet;
	}
	public function generateCodePayment()
	{
		$sql = "SELECT MAX(internal_code) AS code FROM payments";
		$answer = $this->select($sql);
		$code = $answer['code'];
		return $code;
	}
	public function remaining_amount(int $bill)
	{
		$this->intId = $bill;
		$sql = "SELECT remaining_amount AS total FROM bills WHERE id = $this->intId";
		$answer = $this->select($sql);
		$total = $answer['total'];
		return $total;
	}
	public function amount_paid(int $bill)
	{
		$this->intId = $bill;
		$sql = "SELECT amount_paid AS total FROM bills WHERE id = $this->intId";
		$answer = $this->select($sql);
		$total = $answer['total'];
		return $total;
	}
	public function import(int $user, int $client, int $voucher, int $serie, string $code, string $correlative, string $issue, string $expiration, string $current, string $subtotal, string $discount, string $total, int $type, int $method, string $observation, string $state, int $year, int $month)
	{
		$this->intUser = $user;
		$this->intClient = $client;
		$this->intVoucher = $voucher;
		$this->intSerie = $serie;
		$this->strCode = $code;
		$this->strCorrelative = $correlative;
		$this->strIssue = $issue;
		$this->strExpiration = $expiration;
		$this->strCurrent = $current;
		$this->strSubtotal = $subtotal;
		$this->strDiscount = $discount;
		$this->strTotal = $total;
		$this->strType = $type;
		$this->intMethod = $method;
		$this->strObservation = $observation;
		$this->intState = $state;
		$this->intYear = $year;
		$this->intMonth = $month;
		$answer = 0;
		$sql = "SELECT *FROM bills WHERE clientid = $this->intClient AND MONTH(billed_month) = $this->intMonth AND YEAR(billed_month) = $this->intYear AND state != 4 AND type = 2";
		$request = $this->select_all($sql);
		if (empty($request)) {
			$query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation,state) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$data = array($this->intUser, $this->intClient, $this->intVoucher, $this->intSerie, $this->strCode, $this->strCorrelative, $this->strIssue, $this->strExpiration, $this->strCurrent, $this->strSubtotal, $this->strDiscount, $this->strTotal, $this->strTotal, $this->strType, $this->intMethod, $this->strObservation, $this->intState);
			$insert = $this->insert($query, $data);
			if ($insert) {
				$answer = $insert;
			} else {
				$answer = 0;
			}
		} else {
			$answer = 0;
		}
		return $answer;
	}
	public function mass_registration(int $user, int $client, int $voucher, int $serie, string $code, string $correlative, string $issue, string $expiration, string $current, string $subtotal, string $discount, string $total, int $type, int $method, string $observation, string $state)
	{
		$this->intUser = $user;
		$this->intClient = $client;
		$this->intVoucher = $voucher;
		$this->intSerie = $serie;
		$this->strCode = $code;
		$this->strCorrelative = $correlative;
		$this->strIssue = $issue;
		$this->strExpiration = $expiration;
		$this->strCurrent = $current;
		$this->strSubtotal = $subtotal;
		$this->strDiscount = $discount;
		$this->strTotal = $total;
		$this->strType = $type;
		$this->intMethod = $method;
		$this->strObservation = $observation;
		$this->intState = $state;
		$answer = 0;
		$query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation,state)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$data = array($this->intUser, $this->intClient, $this->intVoucher, $this->intSerie, $this->strCode, $this->strCorrelative, $this->strIssue, $this->strExpiration, $this->strCurrent, $this->strSubtotal, $this->strDiscount, $this->strTotal, $this->strTotal, $this->strType, $this->intMethod, $this->strObservation, $this->intState);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = $insert;
		} else {
			$answer = 0;
		}
		return $answer;
	}
	public function create(int $user, int $client, int $voucher, int $serie, string $code, string $correlative, string $issue, string $expiration, string $billed_month, string $subtotal, string $discount, string $total, int $type, int $method, string $observation)
	{
		$this->intUser = $user;
		$this->intClient = $client;
		$this->intVoucher = $voucher;
		$this->intSerie = $serie;
		$this->strCode = $code;
		$this->strCorrelative = $correlative;
		$this->strIssue = $issue;
		$this->strExpiration = $expiration;
		$this->strMonth = $billed_month;
		$this->strSubtotal = $subtotal;
		$this->strDiscount = $discount;
		$this->strTotal = $total;
		$this->strType = $type;
		$this->intMethod = $method;
		$this->strObservation = $observation;
		$answer = "";
		$query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$data = array($this->intUser, $this->intClient, $this->intVoucher, $this->intSerie, $this->strCode, $this->strCorrelative, $this->strIssue, $this->strExpiration, $this->strMonth, $this->strSubtotal, $this->strDiscount, $this->strTotal, $this->strTotal, $this->strType, $this->intMethod, $this->strObservation);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function modify(int $id, string $issue, string $expiration, string $billed_month, string $subtotal, string $discount, string $total, string $observation, int $state)
	{
		$this->intId = $id;
		$this->strIssue = $issue;
		$this->strExpiration = $expiration;
		$this->strMonth = $billed_month;
		$this->strSubtotal = $subtotal;
		$this->strDiscount = $discount;
		$this->strTotal = $total;
		$this->strObservation = $observation;
		$this->intState = $state;
		$answer = "";
		$query = "UPDATE bills SET date_issue=?,expiration_date=?,billed_month=?,subtotal=?,discount=?,total=?,observation=?,state=? WHERE id = $this->intId";
		$data = array($this->strIssue, $this->strExpiration, $this->strMonth, $this->strSubtotal, $this->strDiscount, $this->strTotal, $this->strObservation, $this->intState);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function modify_state(int $id, int $state)
	{
		$this->intId = $id;
		$this->intState = $state;
		$answer = "";
		$query = "UPDATE bills SET state = ? WHERE id = $this->intId";
		$data = array($this->intState);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function modify_amounts(int $id, string $subscriber, string $remaining, int $state)
	{
		$this->intId = $id;
		$this->strSubscriber = $subscriber;
		$this->strRemaining = $remaining;
		$this->intState = $state;
		$answer = "";
		if ($this->intState == 0) {
			$query = "UPDATE bills SET amount_paid = amount_paid + ?,remaining_amount = ? WHERE id = $this->intId";
			$data = array($this->strSubscriber, $this->strRemaining);
		} else {
			$query = "UPDATE bills SET amount_paid = amount_paid + ?,remaining_amount = ?,state = ? WHERE id = $this->intId";
			$data = array($this->strSubscriber, $this->strRemaining, $this->intState);
		}
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function replacement_amounts(int $bill, string $amount_paid, string $remaining)
	{
		$this->intId = $bill;
		$this->strTotalPaid = $amount_paid;
		$this->strRemaining = $remaining;
		$answer = "";
		$query = "UPDATE bills SET amount_paid = ?,remaining_amount = ? WHERE id = $this->intId";
		$data = array($this->strTotalPaid, $this->strRemaining);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function remaining_bill(int $bill, string $remaining)
	{
		$this->intId = $bill;
		$this->strRemaining = $remaining;
		$answer = "";
		$query = "UPDATE bills SET remaining_amount = ? WHERE id = $this->intId";
		$data = array($this->strRemaining);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function remaining_payments(int $payment, string $remaining)
	{
		$this->intPayment = $payment;
		$this->strRemaining = $remaining;
		$answer = "";
		$query = "UPDATE payments SET remaining_credit = ? WHERE id = $this->intPayment";
		$data = array($this->strRemaining);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function cancel(int $id)
	{
		$this->intId = $id;
		$answer = '';
		$sql = "UPDATE bills SET state = ? WHERE id = $this->intId";
		$data = array(4);
		$update = $this->update($sql, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function subtract_stock(int $idserpro, int $quantity)
	{
		$this->intSerpro = $idserpro;
		$this->strQuantity = $quantity;
		$answer = "";
		$query = "UPDATE products SET stock = stock - ? WHERE id = $this->intSerpro";
		$data = array($this->strQuantity);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function increase_stock(int $idserpro, int $quantity)
	{
		$this->intSerpro = $idserpro;
		$this->strQuantity = $quantity;
		$answer = "";
		$query = "UPDATE products SET stock = stock + ? WHERE id = $this->intSerpro";
		$data = array($this->strQuantity);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function increase_serie(int $idvoucher, int $idserie)
	{
		$this->intVoucher = $idvoucher;
		$this->intSerie = $idserie;
		$answer = "";
		$query = "UPDATE voucher_series SET available = available + ? WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
		$data = array(1);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function modify_available(int $idvoucher, int $idserie)
	{
		$this->intVoucher = $idvoucher;
		$this->intSerie = $idserie;
		$answer = "";
		$query = "UPDATE voucher_series SET available = available - ? WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
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
		$this->intId = $id;
		$this->strTypeD = $type;
		$this->intSerpro = $serpro;
		$this->strDescription = $description;
		$this->strQuantity = $quantity;
		$this->strPrice = $price;
		$this->strTotalD = $total;
		$answer = "";
		$query = "INSERT INTO detail_bills(billid,type,serproid,description,quantity,price,total) VALUES(?,?,?,?,?,?,?)";
		$data = array($this->intId, $this->strTypeD, $this->intSerpro, $this->strDescription, $this->strQuantity, $this->strPrice, $this->strTotalD);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function remove_datail(int $id)
	{
		$this->intId = $id;
		$answer = "";
		$sql = "DELETE FROM detail_bills WHERE billid = $this->intId";
		$delete = $this->delete($sql);
		if ($delete) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function create_departures(int $id, int $serpro, string $date, string $description, string $quantity, string $precio, string $total)
	{
		$this->intId = $id;
		$this->intSerpro = $serpro;
		$this->strDate = $date;
		$this->strDescription = $description;
		$this->strQuantity = $quantity;
		$this->strPrice = $precio;
		$this->strTotalD = $total;
		$answer = "";
		$query = "INSERT INTO departures(billid,productid,departure_date,description,quantity_departures,unit_price,total_cost) VALUES(?,?,?,?,?,?,?)";
		$data = array($this->intId, $this->intSerpro, $this->strDate, $this->strDescription, $this->strQuantity, $this->strPrice, $this->strTotalD);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function create_incomes(int $serpro, string $date, string $description, int $quantity, string $precio, string $total)
	{
		$this->intSerpro = $serpro;
		$this->strDate = $date;
		$this->strDescription = $description;
		$this->strQuantity = $quantity;
		$this->strPrice = $precio;
		$this->strTotalD = $total;
		$answer = "";
		$query = "INSERT INTO income(productid,income_date,description,quantity_income,unit_price,total_cost) VALUES(?,?,?,?,?,?)";
		$data = array($this->intSerpro, $this->strDate, $this->strDescription, $this->strQuantity, $this->strPrice, $this->strTotalD);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function create_payment(int $bill, int $user, int $client, string $code, int $type, string $datetime, string $comment, string $subscriber, string $total_paid, string $remaining, int $state, string $ticket_number, string $reference_number)
	{
		$this->intId = $bill;
		$this->intUser = $user;
		$this->intClient = $client;
		$this->strCodePayment = $code;
		$this->strTypePayment = $type;
		$this->strDatetime = $datetime;
		$this->strComment = $comment;
		$this->strSubscriber = $subscriber;
		$this->strTotalPaid = $total_paid;
		$this->strRemaining = $remaining;
		$this->intState = $state;
		$this->strTicketNumber = $ticket_number;
		$this->strReferenceNumber = $reference_number;
		$answer = "";
		$query = "INSERT INTO payments(billid,userid,clientid,internal_code,paytypeid,payment_date,comment,amount_paid,amount_total,remaining_credit,state,ticket_number,reference_number)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$data = array($this->intId, $this->intUser, $this->intClient, $this->strCodePayment, $this->strTypePayment, $this->strDatetime, $this->strComment, $this->strSubscriber, $this->strTotalPaid, $this->strRemaining, $this->intState, $this->strTicketNumber, $this->strReferenceNumber);
		$insert = $this->insert($query, $data);
		if ($insert) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function state_payments(int $bill, int $state)
	{
		$this->intId = $bill;
		$this->intState = $state;
		$answer = "";
		$query = "UPDATE payments SET state = ? WHERE billid = $this->intId";
		$data = array($this->intState);
		$update = $this->update($query, $data);
		if ($update) {
			$answer = 'success';
		} else {
			$answer = 'error';
		}
		return $answer;
	}
	public function debt_opening(string $month, string $year)
	{
		$request = array();
		$sql_consult = "SELECT COUNT(*) AS total FROM bills WHERE MONTH(billed_month) = $month AND YEAR(billed_month) = $year AND type = 2";
		$request_consult = $this->select($sql_consult);
		if ($request_consult >= 0) {
			$sql_clients = "SELECT COUNT(clientid) AS total FROM contracts WHERE state NOT IN(4,5)";
			$request_clients = $this->select($sql_clients);
			$total_clients = $request_clients['total'];

			$sql_issued_invoices = "SELECT COUNT(*) AS total FROM contracts WHERE state NOT IN(4,5) AND clientid  NOT IN(SELECT clientid FROM bills WHERE MONTH(billed_month) = $month AND YEAR(billed_month) = $year AND state != 4 AND type = 2)";
			$request_issued_invoices = $this->select($sql_issued_invoices);
			$total_issued = $request_issued_invoices['total'];

			$request = array('issued_invoices' => $total_issued, 'total_clients' => $total_clients);
		}
		return $request;
	}
	public function detail_opening(string $month, string $year)
	{
		$sql = "SELECT c.id,c.clientid,c.payday,cl.names,cl.surnames
				FROM contracts c
				JOIN clients cl ON c.clientid = cl.id
				WHERE c.state NOT IN(4,5) AND c.clientid NOT IN(SELECT b.clientid FROM bills b WHERE MONTH(b.billed_month) = $month AND YEAR(b.billed_month) = $year AND b.state != 4 AND b.type = 2)";
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
}
