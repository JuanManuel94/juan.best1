<?php

class SendWhatsapp
{
  private $business;
  private $messageError;

  public function __construct($business)
  {
    if (is_object($business)) {
      $this->business = $business;
    } else {
      $this->business = (object) $business;
    }
  }

  public function send($number, $message)
  {
    try {
      $key = $this->business->whatsapp_key;
      $url = "{$this->business->whatsapp_api}/api/messages/send";
      if (!$key) {
        throw new Exception("No se encontró la configuración");
      }
      // validar number
      if (empty($number)) {
        throw new Exception("El número está vacío!!!");
      }
      // agregar headers
      $headers = [
        "Authorization: Bearer {$this->business->whatsapp_key}",
        "Content-Type: application/json",
        "cache-control: no-cache"
      ];
      // enviar whatsapp
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => json_encode(["number" => $number, "body" => $message]),
        CURLOPT_HTTPHEADER => $headers
      ]);
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if (!$response) {
        throw new Exception($err);
      }
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
}