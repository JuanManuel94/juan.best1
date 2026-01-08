<?php

class CustomerAssignPppSecret
{
  public function __construct(string $mikrotikId)
  {
    $this->mysql = new Mysql();
    $this->mikrotikId = $mikrotikId;
  }

  private $mysql;
  private string $mikrotikId;

  public function execute(string $clientId, string $name, string $user, string $password)
  {
    $client = contract_information($clientId)['client'];
    $plan = $this->findPlan($clientId);
    $apiMikrotik = new Mikrotik($this->mikrotikId);
    $profile = $plan['profile'];

    $payload = [
      "name" => $user,
      "service" => "pppoe",
      "profile" => $profile,
      "password" => $password,
      "comment" => $name
    ];

    if ($client['mikrotik_referen']) {
      $id = $client['mikrotik_referen'];
      $apiMikrotik->editPppSecret($id, $payload);
    } else {
      $response = $apiMikrotik->createPppSecret($payload);
      $referen = $response['id'];
      $this->updateReferen($clientId, $referen);
    }
  }

  public function executeChangeState($client, bool $state)
  {
    $apiMikrotik = new Mikrotik($this->mikrotikId);
    $id = $client['mikrotik_referen'];
    return $apiMikrotik->changeStatePppSecret($id, $state);
  }

  public function generateName($client)
  {
    return $client['id'] . " - " . $client['names'] . " " . $client['surnames'];
  }

  public function generatePayload($client, string $profile)
  {
    return [
      "name" => $client['ppoe_usuario'],
      "service" => "pppoe",
      "profile" => $profile,
      "password" => $client['ppoe_password'],
      "comment" => $this->generateName($client)
    ];
  }

  public function executeOnlyCreate(string $clientId, string $serviceId, string $name, string $user, string $password)
  {
    $plan = $this->findPlanById($serviceId);
    $apiMikrotik = new Mikrotik($this->mikrotikId);
    $profile = $plan['profile'];
    $response = $apiMikrotik->createPppSecret([
      "name" => $user,
      "service" => "pppoe",
      "profile" => $profile,
      "password" => $password,
      "comment" => $name
    ]);
    $referen = $response['id'];
    $this->updateReferen($clientId, $referen);
  }

  private function findPlan($clientId)
  {
    return $this->mysql
      ->createQueryBuilder()
      ->from("contracts", "c")
      ->innerJoin("detail_contracts dc", "dc.contractid = c.id")
      ->innerJoin("services s", "s.id = dc.serviceid")
      ->where("c.clientid = {$clientId}")
      ->select("s.details profile")
      ->getOne();
  }

  private function findPlanById(string $serviceId)
  {
    return $this->mysql
      ->createQueryBuilder()
      ->from("services")
      ->where("id = {$serviceId}")
      ->select("details profile")
      ->getOne();
  }

  private function findPlanByContractId(string $contractId)
  {
    return $this->mysql
      ->createQueryBuilder()
      ->from("contracts", "c")
      ->innerJoin("clients cc", "cc.id = c.clientid")
      ->innerJoin("detail_contracts dc", "dc.contractid = c.id")
      ->innerJoin("services s", "s.id = dc.serviceid")
      ->where("c.id = {$contractId}")
      ->select("s.details profile, cc.id, cc.names, cc.surnames, cc.mobile_optional, cc.mikrotik_referen")
      ->getOne();
  }

  public function updateReferen(string $clientId, string $referen)
  {
    $this->mysql
      ->createQueryBuilder()
      ->update("clients")
      ->where("id = {$clientId}")
      ->set([
        "mikrotik_referen" => $referen
      ])
      ->execute();
  }
}