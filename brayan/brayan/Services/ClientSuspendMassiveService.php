<?php

class ClientSuspendMassiveService
{
  private Mysql $mysql;
  private $columns = [
    "cli.id",
    "cli.names",
    "cli.surnames",
    "cli.documentid",
    "cli.document",
    "cli.mobile",
    "cli.mobile_optional",
    "cli.email",
    "cli.address",
    "cli.reference",
    "cli.note",
    "cli.latitud",
    "cli.longitud",
    "cli.state",
    "cli.net_router",
    "cli.net_name",
    "cli.net_password",
    "cli.net_localaddress",
    "cli.net_ip",
    "cli.nap_cliente_id",
    "cli.ap_cliente_id",
    "cli.zonaid"
  ];

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }

  public function execute(string $date)
  {
    $business = $this->find_business();
    $pospago = $this->list_client_postpago($date, $business->month_corte);
    $prepago = $this->list_client_prepago($date, $business->month_corte);
    $collect = [...$pospago, ...$prepago];
    $result = [];
    $error = [];
    // validate
    if (count($collect) == 0) {
      throw new Exception("No se encontró deudas");
    }
    // disabled
    foreach ($collect as $item) {
      $service = new ClientSuspendService($business);
      $response = $service->execute($item['id']);
      if ($response['success']) {
        array_push($result, $item);
      } else {
        array_push($error, $item);
      }
    }
    // response
    return [
      "result" => $result,
      "error" => $error
    ];
  }

  public function list_client_postpago(string $date, int $monthCorte)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients", "cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->innerJoin("bills b", "b.clientid = cli.id")
      ->where("DATE_ADD(b.expiration_date, INTERVAL c.days_grace DAY) <= '{$date}'")
      ->andWhere("b.state IN (2, 3)")
      ->andWhere("c.state = 2")
      ->andWhere("NOT EXISTS (
  SELECT 1
  FROM bills b2
  WHERE b2.clientid = cli.id
    AND b2.state IN (2, 3)
    AND b2.promise_enabled = 1
    AND b2.promise_date >= '{$date}'
    AND (
      SELECT COUNT(*)
      FROM bills b3
      WHERE b3.clientid = cli.id
        AND b3.state IN (2, 3)
        AND b3.expiration_date > b2.expiration_date
    ) < 2
)")


      ->andWhere("c.modalidad = 'POSTPAGO'")
      ->select(implode(", ", $this->columns))
      ->addSelect("count(b.id)", "months")
      ->groupBy(implode(", ", $this->columns))
      ->having("months >= {$monthCorte}")
      ->getMany();
  }



  public function list_client_prepago(string $date, int $monthCorte)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients", "cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->innerJoin("bills b", "b.clientid = cli.id")
      ->where("DATE_ADD(b.date_issue, INTERVAL c.days_grace DAY) <= '{$date}'")
      ->andWhere("b.state NOT IN (1, 4)")
      ->andWhere("c.state = 2")
      ->andWhere("NOT EXISTS (
  SELECT 1
  FROM bills b2
  WHERE b2.clientid = cli.id
    AND b2.state IN (2, 3)
    AND b2.promise_enabled = 1
    AND b2.promise_date >= '{$date}'
    AND (
      SELECT COUNT(*)
      FROM bills b3
      WHERE b3.clientid = cli.id
        AND b3.state IN (2, 3)
        AND b3.expiration_date > b2.expiration_date
    ) < 2
)")


      ->andWhere("c.modalidad = 'PREPAGO'")
      ->select(implode(", ", $this->columns))
      ->addSelect("count(b.id)", "months")
      ->groupBy(implode(", ", $this->columns))
      ->having("months >= {$monthCorte}")
      ->getMany();
  }




  public function find_business()
  {
    return (Object) $this->mysql->createQueryBuilder()
      ->from("business")
      ->getOne();
  }
}