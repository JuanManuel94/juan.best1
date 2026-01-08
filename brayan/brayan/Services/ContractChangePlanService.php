<?php

class ContractChangePlanService
{
  private Mysql $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql("contracts");
  }

  public function execute(string $id)
  {
    // find client
    $client = $this->find_client_by_contractId($id);
    if (!$client) {
      throw new Exception("No se encontró el cliente");
    }
    // response
    $service = new ClientSwitchMikrotikService();
    $service->executeActive($client);
  }

  public function find_client_by_contractId(string $id)
  {
    return $this->mysql->createQueryBuilder("c")
      ->innerJoin("clients cl", "cl.id = c.clientid")
      ->select("cl.*")
      ->where("c.id = {$id}")
      ->getOne();
  }
}
