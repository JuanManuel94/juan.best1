<?php

class ClientSuspendService extends BaseService
{
  private Mysql $mysql;
  private object $businnes;
  private bool $canTransaction = true;
  private string $date;
  private bool $isCancelled = false;
  private int $stateContract = 3;
  private int $statePlan = 2;

  public function __construct(object $businnes)
  {
    parent::__construct();
    $this->businnes = $businnes;
    $this->mysql = new Mysql("clients");
    $this->date = date("Y-m-d");
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
      // routeos disabled
      $service = new ClientRouterService();
      $service->setMysql($this->mysql);
      $service->setClient($client);
      $request = $service->blockNetwork($id);
      // validar 
      if (!$request->success) {
        throw new Exception($request->message);
      }
      // actualizar info
      $this->suspend_contract($id);
      $this->suspend_plan($id);

      if ($this->canTransaction) {
        $this->mysql->commit();
      }
      // emitir evento 
      $this->eventManager->subscribe(new ClientSuspendedListener($this->mysql, $this->businnes));
      $this->eventManager->triggerEvent($client);
      // response
      return ["success" => true, "message" => "Cliente suspendido"];
    } catch (\Throwable $th) {
      if ($this->canTransaction) {
        $this->mysql->rollback();
      }
      return ["success" => false, "message" => $th->getMessage()];
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

  public function setDate(string $date)
  {
    $this->date = $date;
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

  public function suspend_contract(string $id)
  {
    return $this->mysql->createQueryBuilder()
      ->update()
      ->from("contracts")
      ->where("clientid = {$id}")
      ->set([
        "suspension_date" => $this->date,
        "state" => $this->stateContract
      ])->execute();
  }

  public function suspend_plan(string $id)
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
      ->set([
        "state" => $this->statePlan
      ])->execute();
  }
}