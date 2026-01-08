<?php

class ClientActivedService extends BaseService
{
  private Mysql $mysql;
  private object $businnes;
  private bool $canTransaction = true;
  private int $state = 2;

  private int $stateActive = 2;

  public function __construct(object $businnes)
  {
    parent::__construct();
    $this->businnes = $businnes;
    $this->mysql = new Mysql("clients");
  }

  public function setState(int $state)
  {
    $this->state = $state;
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function execute(string $id)
  {
    try {
      if ($this->canTransaction) {
        $this->mysql->createQueryRunner();
      }

      $client = (Object) $this->select_info_client($id);
      if (!$client) {
        throw new Exception("No se encontró el cliente");
      }
      // routeos enabled
      $service = new ClientRouterService();
      $service->setMysql($this->mysql);
      $service->setClient($client);
      $request = $service->unlockNetwork($id);
      // validar 
      if (!$request->success) {
        throw new Exception($request->message);
      }
      // actualizar info
      $this->actived_contract($id);
      $this->actived_plan($id);

      if ($this->canTransaction) {
        $this->mysql->commit();
      }
      // emitir evento
      if ($this->state == $this->stateActive) {
        $this->eventManager->subscribe(new ClientActivedListener($this->mysql, $this->businnes));
        $this->eventManager->triggerEvent($client);
      }
      // response
      return [
        "success" => true,
        "message" => "Activación completada"
      ];
    } catch (\Throwable $th) {
      if ($this->canTransaction) {
        $this->mysql->rollback();
      }
      // response
      return [
        "success" => false,
        "message" => $th->getMessage()
      ];
    }
  }

  public function setCanTransaction(bool $canTransaction)
  {
    $this->canTransaction = $canTransaction;
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

  public function actived_contract(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->update()
      ->from("contracts")
      ->where("clientid = {$id}")
      ->set([
        "suspension_date" => null,
        "state" => $this->state
      ])->execute();
  }

  public function actived_plan(string $id)
  {
    $condition = $this->mysql->createQueryBuilder("cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->where("cli.id = {$id}")
      ->andWhere("d.contractid = c.id")
      ->getSql();
    // update
    return $this->mysql->createQueryBuilder()
      ->update()
      ->from("detail_contracts", "d")
      ->where("EXISTS ({$condition})")
      ->set(["state" => 1])->execute();
  }
}