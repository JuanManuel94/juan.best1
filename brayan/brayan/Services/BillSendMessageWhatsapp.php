<?php

class BillSendMessageWhatsapp
{
  private $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql();
  }

  public function send($filters = [])
  {
    try {
      $array_success = [];
      $array_error = [];

      // Obtener empresa
      $business = $this->findBusiness();

      // Asignar deuda como mes de corte de la empresa
      $filters["deuda"] = $business->month_corte;

      // Obtener lista de clientes con deuda
      $data = $this->listBills($filters);

      // Si no hay clientes con deuda
      if (empty($data)) {
        return [
          "message" => "No se encontraron clientes con deuda para enviar mensajes.",
          "data" => [],
          "error" => [],
          "status" => false
        ];
      }

      // Iterar clientes
      foreach ($data as $item) {
        $client = (Object) $item;

        // Generar mensaje personalizado
        $message = new PlantillaWspInfoService($client, $business);
        $message->setMysql($this->mysql);

        $billIds = [...$client->billIds];
        $message->setArrayBillId($billIds);

        $str_message = $message->execute("PAYMENT_PENDING");

        // Instanciar clase WhatsApp
        $whatsapp = new SendWhatsapp($business);

        // Agrupar números únicos válidos
        $numbers = array_unique(array_filter([
          $client->mobile ?? null,
          $client->mobile_optional ?? null
        ]));

        // Enviar mensaje a cada número
        foreach ($numbers as $number) {
          $response = $whatsapp->send($number, $str_message);

          if ($response) {
            $array_success[] = $number;
          } else {
            $array_error[] = $number;
          }

          // Esperar 15 - 20 segundos entre envíos
          sleep(rand(15, 25));
        }
      }

      // Validar si todos fallaron
      if (count($array_success) === 0) {
        return [
          "message" => "Ningún mensaje fue enviado correctamente.",
          "data" => [],
          "error" => $array_error,
          "status" => false
        ];
      }

      // Retornar resultado normal
      return [
        "message" => "Mensajes enviados correctamente.",
        "data" => $array_success,
        "error" => $array_error,
        "status" => true
      ];
    } catch (\Throwable $th) {
      return [
        "message" => $th->getMessage(),
        "status" => false
      ];
    }
  }

  private function listBills($filters = [])
  {
    $query = $this->mysql->createQueryBuilder()
      ->from("clients cl")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->innerJoin("document_type d", "cl.documentid = d.id")
      ->innerJoin("bills b", "b.clientid = cl.id AND b.state IN (2, 3)")
      ->where("c.state IN (2, 3, 4)")
      ->groupBy("cl.id, cl.names, cl.surnames, cl.documentid, cl.document, cl.mobile, cl.mobile_optional, cl.email, cl.address")
      ->addGroupBy("cl.reference")
      ->addGroupBy("cl.note")
      ->addGroupBy("cl.latitud")
      ->addGroupBy("cl.longitud")
      ->addGroupBy("cl.state")
      ->addGroupBy("cl.net_router")
      ->addGroupBy("cl.net_name")
      ->addGroupBy("cl.net_password")
      ->addGroupBy("cl.net_localaddress")
      ->addGroupBy("cl.net_ip")
      ->select("cl.id, cl.names, cl.surnames, cl.documentid, cl.document, cl.mobile, cl.mobile_optional, cl.email, cl.address")
      ->addSelect("cl.reference", "reference")
      ->addSelect("cl.note", "note")
      ->addSelect("cl.latitud", "latitud")
      ->addSelect("cl.longitud", "longitud")
      ->addSelect("cl.state", "state")
      ->addSelect("cl.net_router", "net_router")
      ->addSelect("cl.net_name", "net_name")
      ->addSelect("cl.net_password", "net_password")
      ->addSelect("cl.net_localaddress", "net_localaddress")
      ->addSelect("cl.net_ip", "net_ip")
      ->addSelect("GROUP_CONCAT(b.id)", "billIds")
      ->addSelect("COUNT(b.id)", "counter");

    // Filtros
    if (isset($filters['id'])) {
      $query->andWhere("cl.id = '{$filters['id']}'");
    }

    if (isset($filters['phone:required'])) {
      $query->andWhere("(cl.mobile IS NOT NULL OR cl.mobile <> '')");
    }

    if (isset($filters['deuda'])) {
      $query->andHaving("counter >= {$filters['deuda']}");
    }

    if (isset($filters['payday'])) {
      $query->andWhere("c.payday = '{$filters['payday']}'");
    }

    // Ejecutar consulta
    $data = $query->getMany();

    // Convertir billIds a array
    foreach ($data as $key => $item) {
      $data[$key]['billIds'] = explode(",", $item['billIds']);
    }

    return $data;
  }

  private function findBusiness()
  {
    $business = $this->mysql
      ->createQueryBuilder()
      ->from("business b")
      ->innerJoin("currency c", "c.id = b.currencyid")
      ->setLimit(1)
      ->select("b.*, c.symbol")
      ->getOne();

    if (!$business) {
      throw new Exception("No se encontró la empresa");
    }

    if (!$business['whatsapp_key']) {
      throw new Exception("No cuenta con una configuración de whatsapp");
    }

    return (Object) $business;
  }
}
