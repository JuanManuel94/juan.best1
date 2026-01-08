<?php

class ClientDeleteService extends BaseService
{
  private Mysql $mysql;
  private object $businnes;
  private bool $canTransaction = true;

  public function __construct(object $businnes)
  {
    parent::__construct();
    $this->businnes = $businnes;
    $this->mysql = new Mysql("clients");
  }

  public function execute(string $id)
{
  try {
    if ($this->canTransaction) {
      $this->mysql->createQueryRunner();
    }

    $client = (Object) $this->select_info_client($id);

    if (!$client->id) {
      throw new Exception("No se encontró el cliente");
    }

    // eliminar datos de la DB
    $this->delete_payments($id);
    $this->delete_detail_bills($id);
    $this->delete_emails($id);
    $this->delete_bills($id);
    $this->delete_detail_contract($id);
    $this->delete_contract($id);
    $this->delete_facility($id);
    $this->delete_client($id);

    // VALIDACIÓN: si net_router es distinto de 0, eliminar en el router
    if (!empty($client->net_router) && $client->net_router != "0") {
      $service = new ClientRouterService();
      $service->setMysql($this->mysql);
      $service->setClient($client);
      $request = $service->deleteNetwork($id);

      if (!$request->success) {
        throw new Exception($request->message);
      }
    }

    if ($this->canTransaction) {
      $this->mysql->commit();
    }

    return true;
  } catch (\Throwable $th) {
    if ($this->canTransaction) {
      $this->mysql->rollback();
    }

    throw $th;
  }
}


  public function setCanTransaction(bool $canTransaction)
  {
    $this->canTransaction = $canTransaction;
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
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

  public function delete_client(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("clients")
      ->where("id = {$id}")
      ->execute();
  }

  public function delete_facility(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("facility")
      ->where("clientid = {$id}")
      ->execute();
  }

  public function delete_contract(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("contracts")
      ->where("clientid = {$id}")
      ->execute();
  }

  public function delete_detail_contract(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("detail_contracts", "dc")
      ->setTableDelete(["dc"])
      ->innerJoin("contracts c", "c.id = dc.contractid")
      ->where("c.clientid = {$id}")
      ->execute();
  }

  public function delete_bills(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("bills")
      ->where("clientid = {$id}")
      ->execute();
  }

  public function delete_emails(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("emails", "e")
      ->setTableDelete(["e"])
      ->innerJoin("bills b", "b.id = e.billid")
      ->where("b.clientid = {$id}")
      ->execute();
  }

  public function delete_detail_bills(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("detail_bills", "db")
      ->setTableDelete(["db"])
      ->innerJoin("bills b", "b.id = db.billid")
      ->where("b.clientid = {$id}")
      ->execute();
  }

  public function delete_payments(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->delete()
      ->from("payments")
      ->where("clientid = {$id}")
      ->execute();
  }
}