<?php

class InvoiceEmailService
{
  private Mysql $mysql;

  public function __construct($mysql)
  {
    $this->mysql = $mysql;
  }

  private $errorMessage;

  public function getErrorMessage()
  {
    return $this->errorMessage;
  }

  public function send($business, $invoice, $affair)
  {
    $correlative = str_pad($invoice['bill']['correlative'], 7, "0", STR_PAD_LEFT);
    $voucher = $invoice['bill']['serie'] . '-' . $correlative;
    $payload = [
      'logo' => $business['logo_email'],
      'name_sender' => $business['business_name'],
      'sender' => $business['email'],
      'password' => $business['password'],
      'mobile' => $business['mobile'],
      'address' => $business['address'],
      'host' => $business['server_host'],
      'port' => $business['port'],
      'addressee' => $invoice['bill']['email'],
      'name_addressee' => "{$invoice['bill']['names']} {$invoice['bill']['surnames']}",
      'affair' => $affair,
      'add_pdf' => true,
      'type_pdf' => 'ticket',
      'data' => $invoice,
      'state' => $affair,
      'voucher' => $invoice['bill']['voucher'],
      'invoice' => $voucher,
      'transaction' => $invoice['bill']['internal_code'],
      'sub_invoice' => $invoice['bill']['subtotal'],
      'dis_invoice' => $invoice['bill']['discount'],
      'total_invoice' => $invoice['bill']['total'],
      'issue' => $invoice['bill']['date_issue'],
      'expiration' => $invoice['bill']['expiration_date'],
      'money_plural' => $business['money_plural'],
      'money' => $business['money'],
      'business' => $business
    ];
    // enviar
    $result = SendMail::message($payload, "notification");
    if (!$result['status']) {
      $this->errorMessage = $result['message'];
      return $this->register($business, $invoice, $affair, 2);
    }
    $this->errorMessage = "";
    return $this->register($business, $invoice, $affair, 1);
  }

  private function register($business, $invoice, $affair, $state)
  {
    $payload = [
      "clientid" => $invoice['bill']['clientid'],
      "billid" => $invoice['bill']['id'],
      "affair" => $affair,
      "sender" => $business['email'],
      "files" => 'true',
      "type_file" => "ticket",
      "template_email" => "notification",
      "registration_date" => date("Y-m-d H:i:s"),
      "state" => $state
    ];
    // save data
    $this->mysql
      ->setTableName("emails")
      ->insertObject([
        "clientid",
        "billid",
        "affair",
        "sender",
        "files",
        "type_file",
        "template_email",
        "registration_date",
        "state"
      ], $payload);
    return $state === 1;
  }
}