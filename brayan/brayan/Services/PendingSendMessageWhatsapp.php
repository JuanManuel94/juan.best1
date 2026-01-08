<?php

class PendingSendMessageWhatsapp
{
  private $business;
  private $messageError;
  private Mysql $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql();
  }

  public function send()
  {
    try {
      $this->business = $this->getBusiness();
      if (!$this->business) {
        throw new Exception("No se encontró la empresa");
      }

      $message = $this->generateMessage();
      $number = $this->business['mobile_refrence'];

      if (!$number) {
        throw new Exception("No tiene número");
      }
      // enviar mensaje
      $whatsapp = new SendWhatsapp($this->business);
      $isSend = $whatsapp->send($number, $message);

      if (!$isSend) {
        throw new Exception($whatsapp->getMessageError());
      }
      // response success
      return true;
    } catch (\Throwable $th) {
      $this->messageError = $th->getMessage();
      return false;
    }
  }

  public function getMessageError()
  {
    return $this->messageError;
  }

  private function getBusiness()
  {
    return $this->mysql->createQueryBuilder()
      ->from("business b")
      ->innerJoin("currency c", "c.id = b.currencyid")
      ->select("*")
      ->getOne();
  }

  private function generateMessage()
  {
    $currentDate = date('Y-m-d');
    $data = $this->mysql->createQueryBuilder()
      ->from("otros_ingresos")
      ->where("state = 'PENDIENTE'")
      ->andWhere("tipo = 'EGRESO'")
      ->andWhere("fecha <= '{$currentDate}'")
      ->getMany();
    $counter = count($data);
    if (!$counter)
      throw new Exception("No hay deuda pendiente!!!");
    // generar mensaje
    $message = "*RECORDATORIO PAGOS PENDIENTE* \n\n";
    $message .= "Empresa {$this->business['business_name']}, ";
    $message .= "Se le recuerda que tiene pendiente de pago lo siguiente:\n\n";
    foreach ($data as $item) {
      $descripcion = $item['descripcion'];
      $symbol = $this->business['symbol'];
      $monto = $item['monto'];
      $fecha = $item['fecha'];
      $message .= "° {$descripcion}, con fecha registrada para el {$fecha}, por el monto TOTAL DE {$monto}.\n";
    }
    // response message
    return $message;
  }
}