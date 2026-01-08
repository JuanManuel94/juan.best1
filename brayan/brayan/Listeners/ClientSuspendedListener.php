<?php

class ClientSuspendedListener implements Observer
{

  private Mysql $mysql;
  private object $businnes;

  public function __construct(Mysql $mysql, object $businnes)
  {
    $this->mysql = $mysql;
    $this->businnes = $businnes;
  }

  public function update($client)
  {
    if (isset($client->mobile)) {
      $this->send_message($client, $client->mobile);
    }

    if (isset($client->mobile_optional)) {
      $this->send_message($client, $client->mobile_optional);
    }
  }

  public function send_message(object $client, string $phone)
  {
    $message = new PlantillaWspInfoService($client, $this->businnes);
    $str_message = $message->execute("CLIENT_SUSPENDED");
    $wsp = new SendWhatsapp($this->businnes);
    $mobile = "{$this->businnes->country_code}{$phone}";
    $wsp->send($mobile, $str_message);
  }
}