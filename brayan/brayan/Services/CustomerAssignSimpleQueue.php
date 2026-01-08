<?php

class CustomerAssignSimpleQueue
{
  public function __construct(string $mikrotikId)
  {
    $this->mysql = new Mysql();
    $this->mikrotikId = $mikrotikId;
  }

  private $mysql;
  private string $mikrotikId;

  public function execute(string $clientId, string $name, string $ip)
  {
    $client = contract_information($clientId)['client'];
    $plan = $this->findPlan($clientId);
    $apiMikrotik = new Mikrotik($this->mikrotikId);
    $maxLimit = "{$plan['rise']}M/{$plan['descent']}M";
    if ($client['mikrotik_referen']) {
      $id = $client['mikrotik_referen'];
      $apiMikrotik->editSimpleQueue($id, $name, $maxLimit, $ip);
    } else {
      $response = $apiMikrotik->createSimpleQueue($name, $maxLimit, $ip);
      $referen = $response['id'];
      $this->updateReferen($clientId, $referen);
    }
  }

  public function executeActive($client)
  {
    $clientId = $client['id'];
    $name = $this->generateName($client);
    $ip = $client['mobile_optional'];
    $cortefirewall = $client['corte_firewall'];
    if ($cortefirewall) {
      $apiMikrotik = new Mikrotik($this->mikrotikId);
      $apiMikrotik->enabledFirewallCut([
        "address" => $ip
      ]);
    } else {
      return $this->execute($clientId, $name, $ip);
    }
  }

  public function generateName($client)
  {
    return $client['id'] . " - " . $client['names'] . " " . $client['surnames'];
  }

  public function executeOnlyCreate(string $clientId, string $serviceId, string $name, string $ip)
  {
    $plan = $this->findPlanById($serviceId);
    $apiMikrotik = new Mikrotik($this->mikrotikId);
    $maxLimit = "{$plan['rise']}M/{$plan['descent']}M";
    $response = $apiMikrotik->createSimpleQueue($name, $maxLimit, $ip);
    $referen = $response['id'];
    $this->updateReferen($clientId, $referen);
  }

  public function executeSuspend(string $contractId)
  {
    $plan = $this->findPlanByContractId($contractId);
    $mikrotik_referen = $plan['mikrotik_referen'];
    $corte_firewall = $plan['corte_firewall'];

    if ($mikrotik_referen && !$corte_firewall) {
      $apiMikrotik = new Mikrotik($this->mikrotikId);
      $maxLimit = "1k/1k";
      $name = $plan["id"] . " - " . $plan['names'] . " " . $plan["surnames"];
      $ip = $plan['mobile_optional'];
      $apiMikrotik->editSimpleQueue($mikrotik_referen, $name, $maxLimit, $ip);
    } else if ($mikrotik_referen && $corte_firewall) {
      $apiMikrotik = new Mikrotik($this->mikrotikId);
      $ip = $plan['mobile_optional'];
      $name = $plan["id"] . " - " . $plan['names'] . " " . $plan["surnames"];
      $apiMikrotik->disabledFirewallCut([
        "address" => $ip,
        "comment" => $name
      ]);
    }
  }

  private function findPlan($clientId)
  {
    return $this->mysql
      ->createQueryBuilder()
      ->from("contracts", "c")
      ->innerJoin("detail_contracts dc", "dc.contractid = c.id")
      ->innerJoin("services s", "s.id = dc.serviceid")
      ->where("c.clientid = {$clientId}")
      ->select("s.rise, s.descent")
      ->getOne();
  }

  private function findPlanById(string $serviceId)
  {
    return $this->mysql
      ->createQueryBuilder()
      ->from("services")
      ->where("id = {$serviceId}")
      ->select("rise, descent")
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
      ->select("s.rise, s.descent, cc.id, cc.names, cc.surnames, cc.mobile_optional, cc.mikrotik_referen, cc.corte_firewall")
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