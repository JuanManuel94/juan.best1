<?php

class ClientRouterService
{
  private Mysql $mysql;
  private object $client;

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }

  public function blockNetwork(string $clientId)
  {
    try {
      $data = (Object) $this->handleData($clientId);
      $router = $data->router;
      $firewall = $data->firewall;
      $client = $data->client;

      if ($firewall->success && count($firewall->data) == 0) {
        $fullName = $this->client->names . ' ' . $this->client->surnames;
        $router->APIAddFirewallAddress($client->net_ip, "moroso", "{$fullName}");
      }

      return (Object) [
        "success" => true,
        "message" => "Red bloqueada"
      ];
    } catch (\Throwable $th) {
      return (Object) [
        "success" => false,
        "message" => $th->getMessage()
      ];
    }
  }

  public function unlockNetwork(string $clientId)
  {
    try {
      $data = (Object) $this->handleData($clientId);
      $router = $data->router;
      $firewall = $data->firewall;
      $client = $data->client;

      if ($firewall->success && count($firewall->data) > 0) {
        $router->APIRemoveFirewallAddress($client->net_ip, "moroso");
      }

      return (Object) [
        "success" => true,
        "message" => "Red desbloqueada"
      ];
    } catch (\Throwable $th) {
      return (Object) [
        "success" => false,
        "message" => $th->getMessage()
      ];
    }
  }

  public function deleteNetwork(string $clientId)
  {
    try {
      $data = (Object) $this->handleData($clientId);
      $router = $data->router;
      $firewall = $data->firewall;
      $client = $data->client;

      if ($firewall->success) {
        $router->APIRemoveFirewallAddress($client->net_ip, "moroso");
        $router->APIDeleteQueuesSimple($client->net_ip);
        $router->APIDeletePPPSecret($client->net_ip);
      }

      return (Object) [
        "success" => true,
        "message" => "Entrada datos del mikrotik"
      ];
    } catch (\Throwable $th) {
      return (Object) [
        "success" => false,
        "message" => $th->getMessage()
      ];
    }
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function setClient(object $client)
  {
    $this->client = $client;
  }

  private function handleData(string $clientId)
  {
    if (empty($this->client)) {
      $this->client = (Object) $this->findClient($clientId);
    }

    if (empty($this->client)) {
      throw new Exception("El cliente no existe!");
    }

    $network = (Object) $this->findNetwork($this->client->net_router);
    if (empty($network)) {
      throw new Exception("No se encontró el router");
    }

    $router = new Router($network->ip, $network->port, $network->username, decrypt_aes($network->password, SECRET_IV));

    if ($router->connected != true) {
      throw new Exception("No hay conexión con el equipo mikrotik para realizar la operación");
    }

    $firewall = $router->APIGetFirewallAddress($this->client->net_ip, "moroso");

    if ($firewall->success != true) {
      throw new Exception("No se pudo desconectar");
    }

    return [
      "client" => $this->client,
      "router" => $router,
      "firewall" => $firewall,
    ];
  }

  private function findClient(string $clientId)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients")
      ->where("id = {$clientId}")
      ->getOne();
  }

  private function findNetwork(string $netRouterId)
  {
    return $this->mysql->createQueryBuilder()
      ->from("network_routers", "r")
      ->innerJoin("network_zones z", "z.id = r.zoneid")
      ->andWhere("r.id = {$netRouterId}")
      ->getOne();
  }
}