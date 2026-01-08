<?php

class ContractActiveService
{

  private Mysql $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }

  public function execute(string $id)
  {
    try {
      $client = $this->select_info_client_by_contract($id);
      // activar mikrotik
      $service = new ClientSwitchMikrotikService();
      $service->executeActive($client);
      // transaction
      $this->mysql->createQueryRunner();
      // update
      $this->active_client_by_contract($id);
      $this->active_detail($id);
      // response
      $this->mysql->commit();
      return true;
    } catch (\Throwable $th) {
      $this->mysql->rollback();
      return false;
    }
  }

  public function executeByClient(string $id)
  {
    try {
      $client = $this->select_info_client($id);
      if (!$client) {
        throw new Exception("No se encontró el cliente");
      }
      // activar mikrotik
      if ($client['mikrotik'] && ($client['opcion'] == 'WISP' || $client['mobile_optional'])) {
        $apiMikrotik = new CustomerAssignSimpleQueue($client['mikrotik']);
        $apiMikrotik->executeActive($client);
      } else if ($client['mikrotik'] && $client['opcion'] == 'PPOE') {
        $apiMikrotik = new CustomerAssignPppSecret($client['mikrotik']);
        $apiMikrotik->executeChangeState($client, true);
      }
      // transaction
      $this->mysql->createQueryRunner();
      // update
      $this->active_client($id);
      $this->active_detail($client['contractId']);
      // response
      $this->mysql->commit();
      return true;
    } catch (\Throwable $th) {
      $this->mysql->rollback();
      return false;
    }
  }

  public function select_info_client_by_contract(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients cl")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->where("c.id = {$id}")
      ->select("cl.*")
      ->getOne();
  }

  public function select_info_client(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients cl")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->where("cl.id = {$id}")
      ->select("cl.*, c.id contractId")
      ->getOne();
  }

  public function active_client(string $id)
  {
    $date_finish = "0000-00-00";
    $this->mysql->createQueryBuilder()
      ->update()
      ->from("contracts")
      ->where("clientid = {$id}")
      ->set([
        "finish_date" => $date_finish,
        "state" => 2
      ])->execute();
  }

  public function active_client_by_contract(string $id)
  {
    $date_finish = "0000-00-00";
    $this->mysql->createQueryBuilder()
      ->update()
      ->from("contracts")
      ->where("id = {$id}")
      ->set([
        "finish_date" => $date_finish,
        "state" => 2
      ])->execute();
  }

  public function active_detail(string $id)
  {
    $this->mysql->createQueryBuilder()
      ->update()
      ->from("detail_contracts")
      ->where("contractid = {$id}")
      ->set([
        "state" => 1
      ])->execute();
  }
}