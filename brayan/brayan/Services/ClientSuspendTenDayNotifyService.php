<?php

class ClientSuspendTenDayNotifyService
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
    "cli.zonaid",
    "c.suspension_date"
  ];

  public function __construct()
  {
    $this->mysql = new Mysql("clients");
  }

  public function execute(string $date)
  {
    $business = $this->find_business();
    $collect = $this->list_client($date);
    $result = [];
    $error = [];
    // validate
    if (count($collect) == 0) {
      throw new Exception("No se encontró deudas");
    }
    // disabled
    foreach ($collect as $item) {
      $client = (Object) $item;
      $message = new PlantillaWspInfoService($client, $business);
      // validar bills
      if (isset($client->billIds)) {
        $array_bill = explode(",", ($client->billIds));
        $message->setArrayBillId($array_bill);
      }
      // message settings
      $str_message = $message->execute("SUSPENDED_NOTIFY");
      $wsp = new SendWhatsapp($business);
      $mobile = "{$business->country_code}{$client->mobile}";
      // validar message wsp
      try {
        $response = $wsp->send($mobile, $str_message);
        if ($response) {
          array_push($result, $client);
        } else {
          array_push($error, $client);
        }
      } catch (\Throwable $th) {
        array_push($error, $client);
      }
    }
    // response
    return [
      "result" => $result,
      "error" => $error
    ];
  }

  public function list_client(string $date)
  {
    return $this->mysql->createQueryBuilder()
      ->from("clients", "cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->leftJoin("bills b", "b.clientid = cli.id")
      ->where("DATE_ADD(c.suspension_date, INTERVAL 10 DAY) <= '{$date}'")
      ->andWhere("c.state = 3")
      ->select(implode(", ", $this->columns))
      ->addSelect("GROUP_CONCAT(b.id)", "billIds")
      ->groupBy(implode(", ", $this->columns))
      ->getMany();
  }

  public function find_business()
  {
    return (Object) $this->mysql->createQueryBuilder()
      ->from("business")
      ->getOne();
  }
}