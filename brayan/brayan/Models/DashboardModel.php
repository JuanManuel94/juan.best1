<?php
class DashboardModel extends Mysql
{
	public function __construct()
	{
		parent::__construct();
	}
	public function number_customers()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM contracts WHERE state NOT IN (0, 3, 4, 5)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}

	public function canceled_customers()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM contracts WHERE state = 4 AND clientid IN (SELECT id FROM clients WHERE state = 1)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}

	public function suspended_customers()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM contracts WHERE state = 3";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}

	public function gratis_customers()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM contracts WHERE state = 5";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}

	public function number_internet()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM services WHERE type = 1 AND state != 0";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function number_plans()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM services WHERE state != 0";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function number_products()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM products";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function stock_products()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM products WHERE stock = 0";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function number_users()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM users WHERE id != 1 AND state NOT IN(0,2)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function inactive_users()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM users WHERE state = 2";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function number_installations()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM facility WHERE state != 0";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function pending_installations()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM facility WHERE state NOT IN(1,3,5)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function number_tickets()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM tickets WHERE state != 0";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function pending_tickets()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM tickets WHERE state NOT IN(1,3,6)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function total_transactions_day(string $day)
	{
		$sql = "SELECT COALESCE(SUM(amount_paid),0) AS total FROM payments WHERE DATE(payment_date) = '$day' AND state = 1";
		$answer = $this->select($sql);
		$total = $answer['total'];
		return $total;
	}
	public function total_transactions_month()
	{
		$sql = "SELECT COALESCE(SUM(amount_paid),0) AS total FROM payments WHERE MONTH(payment_date) = MONTH(NOW()) AND YEAR(payment_date) = YEAR(NOW()) AND state = 1";
		$answer = $this->select($sql);
		$total = $answer['total'];
		return $total;
	}
	public function unpaid_bills()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM bills WHERE state NOT IN(1,4) AND id NOT IN (SELECT billid FROM payments WHERE state = 1)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function expired_bills()
	{
		$sql = "SELECT COUNT(*) AS quantity FROM bills WHERE state = 3 AND id NOT IN(SELECT billid FROM payments WHERE state = 1)";
		$answer = $this->select($sql);
		$quantity = $answer['quantity'];
		return $quantity;
	}
	public function transactions_month(int $month, int $year)
	{
		$total_payments = 0;
		$days_payments = array();
		$days_array = array();
		$days = cal_days_in_month(CAL_GREGORIAN, $month, $year) + 1;
		$day = 1;
		for ($i = 1; $i < $days; $i++) {
			$date = date_create($year . "-" . $month . "-" . $day);
			$formatted_date = date_format($date, "Y-m-d");
			$query = "SELECT DAY(payment_date) AS day,COUNT(id) AS quantity,COALESCE(SUM(amount_paid),0) AS total FROM payments WHERE DATE(payment_date) = '$formatted_date' AND state = 1";
			$payments = $this->select($query);
			$payments['day'] = $day;
			$total_payments += $payments['total'];
			array_push($days_payments, $payments);
			$days_array[] = "Día " . $i;
			$day++;
		}
		$months = months();
		$data = array('year' => $year, 'month' => $months[intval($month - 1)], 'total' => $total_payments, 'payments' => $days_payments, 'days' => $days_array);
		return $data;
	}
	public function payments_type(int $month, int $year)
	{
		$arrPayments = array();
		$payments = array('payment_type' => '', 'quantity' => '', 'total' => '', 'count_total' => '');
		$query = "SELECT fp.payment_type,COUNT(p.paytypeid) AS quantity,COALESCE(SUM(p.amount_paid),0) AS total
			FROM payments p
			JOIN forms_payment fp ON p.paytypeid = fp.id
			WHERE MONTH(p.payment_date) = '$month' AND YEAR(p.payment_date) = '$year' AND p.state = 1 GROUP BY fp.payment_type ORDER BY COUNT(p.paytypeid) DESC LIMIT 6";
		$answer = $this->select_all($query);
		for ($i = 0; $i < COUNT($answer); $i++) {
			$payments['payment_type'] = $answer[$i]['payment_type'];
			$payments['quantity'] = $answer[$i]['quantity'];
			$payments['total'] = $_SESSION['businessData']['symbol'] . format_money($answer[$i]['total']);
			$payments['count_total'] = $answer[$i]['total'];
			array_push($arrPayments, $payments);
		}
		$months = months();
		$data = array('year' => $year, 'month' => $months[intval($month - 1)], 'payments' => $arrPayments);
		return $data;
	}
	public function top_products()
	{
		$sql = "SELECT p.product,SUM(db.quantity) AS quantity,SUM(ROUND(db.total,2)) AS total,image FROM
			detail_bills db
			JOIN products p ON db.serproid = p.id
			WHERE db.type = 1
			GROUP BY p.product
			ORDER BY SUM(ROUND(db.total,2)) DESC
			LIMIT 10";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function products_sellout()
	{
		$sql = "SELECT id,internal_code,product,stock,sale_price FROM	products
			WHERE stock <= stock_alert
			ORDER BY stock ASC
			LIMIT 15";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function last_payments()
	{
		$sql = "SELECT ct.id,c.names,c.surnames,u.username,p.payment_date,p.amount_paid FROM
			payments p
			JOIN users u ON p.userid = u.id
			JOIN clients c ON p.clientid = c.id
			JOIN contracts ct ON c.id = ct.clientid
			WHERE p.state = 1
			ORDER BY p.id DESC
			LIMIT 15";
		$answer = $this->select_all($sql);
		return $answer;
	}
	public function libre_services(int $year)
	{
		$products = array();
		$services = array();
		for ($i = 0; $i < 12; $i++) {
			$months = $i + 1;
			$sql_products = "SELECT COUNT(id) AS total FROM bills
				WHERE MONTH(date_issue) = $months AND YEAR(date_issue) = $year AND state != 4 AND type = 1";
			$productsMonths = $this->select($sql_products);
			$sql_services = "SELECT COUNT(id) AS total FROM bills
				WHERE MONTH(date_issue) = $months AND YEAR(date_issue) = $year AND state != 4 AND type = 2";
			$servicesMonths = $this->select($sql_services);
			array_push($products, $productsMonths);
			array_push($services, $servicesMonths);
		}
		$data = array('year' => $year, 'products' => $products, 'services' => $services);
		return $data;
	}
	public function list_paymentes(int $user)
	{
		$sql = "SELECT p.id,p.clientid,p.billid,p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,p.state,b.total AS bill_total,CONCAT_WS(' ', c.names, c.surnames) AS client,u.names AS user,vs.serie,v.voucher,b.correlative
			FROM payments p
			JOIN forms_payment fp ON p.paytypeid = fp.id
			JOIN users u ON p.userid = u.id
			JOIN clients c ON p.clientid = c.id
			JOIN bills b ON p.billid = b.id
			JOIN vouchers v ON b.voucherid = v.id
			JOIN voucher_series vs ON b.serieid = vs.id
			WHERE p.state = 1 AND p.userid = $user ORDER BY p.id DESC";
		$answer = $this->select_all($sql);
		return $answer;
	}
}
