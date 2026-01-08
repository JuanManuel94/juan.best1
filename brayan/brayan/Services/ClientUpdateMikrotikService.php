<?php

class ClientUpdateMikrotikService
{
  private Mysql $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }
  public function execute($client)
  {
    try {
      $clientId = $client['id'];
      $referen = $client['mikrotik_referen'];
      $mikrotikOption = $client['opcion'];
      $mikrotikName = $clientId . " - " . $client['names'] . " " . $client["surnames"];
      $mikrotikId = $client['mikrotik'];
      $mikrotikIp = $client['mobile_optional'];
      $mikrotikUser = $client['ppoe_usuario'];
      $mikrotikPassword = $client['ppoe_password'];
      // validar ocnection
      if ($referen && $mikrotikOption == 'WISP') {
        $apiMikrotik = new CustomerAssignSimpleQueue($mikrotikId);
        $apiMikrotik->execute($clientId, $mikrotikName, $mikrotikIp);
      } else if ($referen && $mikrotikOption == "PPOE") {
        $apiMikrotik = new CustomerAssignPppSecret($mikrotikId);
        $apiMikrotik->execute($clientId, $mikrotikName, $mikrotikUser, $mikrotikPassword);
      }
      // response
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }

  public function executeByContractId(string $id)
  {
    $client = $this->find_client_by_contractId($id);
    if (!$client) {
      throw new Exception("No se encontró el cliente!!!");
    }
    return $this->execute($client);
  }

  public function find_client_by_contractId(string $id)
  {
    return $this->mysql->createQueryBuilder("cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->where("c.clientid = {$id}")
      ->select("cli.*")
      ->getOne();
  }
}