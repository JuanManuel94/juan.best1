<?php

class ClientNetworkChangeService
{
  private Mysql $mysql;
  private object $client;
  private object $plan;
  private string $maxLimit;

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }

  public function execute(array $payload)
  {
    if (empty($this->client)) {
      throw new Exception("El cliente no existe!");
    }

    $network = $this->findNetwork($this->client->net_router);
    if (empty($network)) {
      throw new Exception("No se encontró el router");
    }

    $this->plan = $this->findPlan();
    if (empty($plan)) {
      throw new Exception("No se encontró el plan");
    }

    // add maxLimit
    $this->maxLimit = "{$this->plan->rise}M/{$this->plan->descent}M";

    // cambio de router
    if (isset($payload['net_router']) && $payload['net_router'] != $network->next_router) {
      return $this->changeRouter($network, $payload['net_router']);
    }
  }

  private function changeRouter(object $currentRouter, array $payload)
  {
    $newRouter = $this->findNetwork($payload['net_router']);
    if (empty($newRouter)) {
      throw new Exception("No se encontró el router");
    }

    // validar conexión del router actual
    if (!$currentRouter->connected) {
      throw new Exception("No se pudo conectar al router actual");
    }

    // validar conexión del nuevo router
    if (!$newRouter->connected) {
      throw new Exception("No se pudo conectar al nuevo router");
    }

    // eliminar datos anteriores

  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function setClient(object $client)
  {
    $this->client = $client;
  }

  public function findClient(string $clientId)
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("clients")
      ->where("id = {$clientId}")
      ->select("*")
      ->getOne();
  }

  private function findNetwork(string $net_router)
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("network_routers", "r")
      ->innerJoin("network_zones z", "z.id = r.zoneid")
      ->where("r.id = {$net_router}")
      ->select("r.*, z.mode")
      ->getOne();
  }

  private function connectRouter(object $network)
  {
    return new Router($network->ip, $network->port, $network->username, decrypt_aes($network->password, SECRET_IV));
  }

  private function findPlan()
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("services", "s")
      ->innerJoin("contracts c", " c.clientid = {$this->client->id}")
      ->innerJoin("detail_contracts cd", "cd.serviceid = s.id")
      ->where("contractid = c.id")
      ->select("s.*")
      ->getOne();
  }
}