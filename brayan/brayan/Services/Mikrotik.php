<?php

class Mikrotik
{
  public function __construct(string $mikrotikId)
  {
    $this->mikrotikId = $mikrotikId;
    $this->mysql = new Mysql("business");
  }

  private Mysql $mysql;
  private string $mikrotikId;

  private function api(string $url, string $method, $body = [])
  {
    $business = $this->find_business();
    if (!$business) {
      throw new Exception("No se encontró la configuración");
    }
    // validar host/token
    $host = $business['mikrotik_endpoint'];
    $token = $business['mikrotik_token'];
    if (!$host || !$token) {
      print_r("error");
      throw new Exception("No soporta el mikrotik");
    }
    // request
    $json_data = json_encode($body);
    $ch = curl_init();
    $apiUrl = $host . "/" . $url;
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (strtoupper($method) == "POST") {
      curl_setopt($ch, CURLOPT_POST, strtoupper($method) == "POST");
    } else {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    }


    $headers = [
      'Content-Type: application/json',
      "business-identify: {$token}",
      "mikrotik-identify: {$this->mikrotikId}",
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if ($response === false) {
      throw new Exception($ch);
    } else {
      $data = json_decode($response, true);
      curl_close($ch);
      return $data;
    }
  }

  public function find_business()
  {
    return $this->mysql->createQueryBuilder()->getOne();
  }

  public function createSimpleQueue(string $name, string $maxLimit, string $target)
  {
    return $this->api("simpleQueues", "POST", [
      "name" => $name,
      "maxLimit" => $maxLimit,
      "target" => $target
    ]);
  }

  public function editSimpleQueue(string $id, string $name, string $maxLimit, string $target)
  {
    return $this->api("simpleQueues/{$id}", "PUT", [
      "name" => $name,
      "maxLimit" => $maxLimit,
      "target" => $target
    ]);
  }

  public function createPppSecret(array $params)
  {
    return $this->api("ppp-secret", "POST", [
      "name" => $params['name'],
      "service" => $params['service'],
      "profile" => $params['profile'],
      "password" => $params['password'],
      "comment" => $params['comment'],
    ]);
  }

  public function deleteSimpleQueue(string $id)
  {
    return $this->api("simpleQueues/{$id}", "DELETE");
  }


  public function editPppSecret(string $id, array $params)
  {
    return $this->api("ppp-secret/{$id}", "PUT", [
      "name" => $params['name'],
      "service" => $params['service'],
      "profile" => $params['profile'],
      "password" => $params['password'],
      "comment" => $params['comment'],
    ]);
  }

  public function deletePppSecret(string $id)
  {
    return $this->api("ppp-secret/{$id}", "DELETE");
  }

  public function changeStatePppSecret(string $id, bool $state)
  {
    $path = $state ? "enabled" : "disabled";
    return $this->api("ppp-secret/{$id}/{$path}", "PUT", ["disabled" => $state]);
  }

  public function disabledFirewallCut(array $params)
  {
    return $this->api("firewall-cut/disabled", "POST", [
      "address" => $params["address"],
      "comment" => $params["comment"]
    ]);
  }

  public function enabledFirewallCut(array $params)
  {
    return $this->api("firewall-cut/enabled", "POST", [
      "address" => $params["address"],
    ]);
  }
}