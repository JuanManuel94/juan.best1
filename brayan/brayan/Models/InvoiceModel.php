<?php
    class InvoiceModel extends Mysql{
        public function __construct(){
            parent::__construct();
        }
        public function document(int $id){
            $request = array();
            $sql_bill = "SELECT b.id,b.voucherid,c.names,c.surnames,dt.document AS type_doc,c.document,c.mobile,c.address,c.email,v.voucher,vs.serie,b.internal_code,b.observation,b.date_issue,b.correlative,b.expiration_date,b.subtotal,b.discount,b.total,b.type,u.names AS user,b.state
            FROM bills b	JOIN clients c ON b.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN users u ON b.userid = u.id JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id = $id";
            $request_bill = $this->select($sql_bill);
            if(!empty($request_bill)){
              $sql_business = "SELECT b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.logo_login,b.logotyope,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,c.symbol,c.money,c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id LIMIT 0,1";
              $request_business = $this->select($sql_business);
              $sql_atm = "SELECT u.names AS user FROM payments p JOIN users u ON p.userid = u.id WHERE p.billid = $id  ORDER BY p.id DESC LIMIT 1";
      				$request_atm = $this->select($sql_atm);
              $sql_payment = "SELECT p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,u.names
              FROM payments p
              JOIN forms_payment fp ON p.paytypeid = fp.id
              JOIN users u ON p.userid = u.id
              WHERE p.billid = $id AND p.state != 3";
              $request_payment = $this->select_all($sql_payment);
              $sql_detail = "SELECT *FROM detail_bills WHERE billid = $id";
              $request_detail = $this->select_all($sql_detail);
              $request = array('bill' => $request_bill,'detail' => $request_detail,'business' => $request_business, 'payments' => $request_payment, 'atm' => $request_atm);
            }
            return $request;
        }
    }
