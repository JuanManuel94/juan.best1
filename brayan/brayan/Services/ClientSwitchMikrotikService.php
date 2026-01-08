<?php

class ClientSwitchMikrotikService
{
  private Mysql $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }
  public function executeActive($client)
  {
    if ($client['mikrotik'] && ($client['opcion'] == 'WISP' || $client['mobile_optional'])) {
      $apiMikrotik = new CustomerAssignSimpleQueue($client['mikrotik']);
      $apiMikrotik->executeActive($client);
    } else if ($client['mikrotik'] && $client['opcion'] == 'PPOE') {
      $apiMikrotik = new CustomerAssignPppSecret($client['mikrotik']);
      $apiMikrotik->executeChangeState($client, true);
    }
  }

  public function executeSuspend($client)
  {
    $contract = $this->find_contract_by_clientId($client['id']);
    if (!$contract) {
      throw new Exception("No se encontró el contrato");
    }
    // api routeos
    if ($client['mikrotik'] && ($client['opcion'] == 'WISP' || $client['mobile_optional'])) {
      $apiMikrotik = new CustomerAssignSimpleQueue($client['mikrotik']);
      $apiMikrotik->executeSuspend($contract['id']);
    } else if ($client['mikrotik'] && $client['opcion'] == 'PPOE') {
      $apiMikrotik = new CustomerAssignPppSecret($client['mikrotik']);
      $apiMikrotik->executeChangeState($client, false);
    }
  }

  public function find_contract_by_clientId(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->from("contracts c")
      ->where("c.clientid = {$id}")
      ->select("c.*")
      ->getOne();
  }
}