<?php

class BillAddPromiseService extends BaseService
{

  private Mysql $mysql;
  private bool $canTransaction = true;

  public function __construct()
  {
    parent::__construct();
    $this->mysql = new Mysql("bills");
  }

  public function execute(string $id, array $payload)
  {
    try {
      if ($this->canTransaction) {
        $this->mysql->createQueryRunner();
      }

      $bill = $this->find_bill($id);
      if (empty($bill->id)) {
        throw new Exception("No se encontró la factura");
      }

      $client = $this->find_client($bill->clientid);
      if (empty($client->id)) {
        throw new Exception("No se encontró el cliente");
      }

      $business = $this->find_business();
      if (empty($business->id)) {
        throw new Exception("No se encontró la empresa");
      }

      $this->update_bill($bill->id, $payload);

      if ($client->contractState != 2) {
        $service = new ClientActivedService($business);
        $service->setCanTransaction($this->canTransaction);
        $result = $service->execute($bill->clientid);
        if (!$result['success']) {
          throw new Exception($result['message']);
        }
      }

      // save process
      $this->mysql->commit();

      // notificar
      $observable = new PromiseAddListener($this->mysql, $business);
      $observable->setArrayBillId([$bill->id]);
      $this->eventManager->subscribe($observable);
      $this->eventManager->triggerEvent($client);

      return true;
    } catch (\Throwable $th) {
      $this->mysql->rollback();
      return false;
    }
  }

  public function setCanTransaction(bool $canTransaction)
  {
    $this->canTransaction = $canTransaction;
  }


  public function find_bill(string $id)
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("bills")
      ->where("id = {$id}")
      ->getOne();
  }

  public function find_client(string $id)
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("clients", "cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->where("cli.id = {$id}")
      ->select("cli.*, c.state contractState")
      ->getOne();
  }

  public function update_bill(string $id, array $payload)
  {
    return $this->mysql->createQueryBuilder()->update()
      ->from("bills")
      ->where("id = {$id}")
      ->set($payload)
      ->execute();
  }

  public function find_business()
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("business")
      ->getOne();
  }
}