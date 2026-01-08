<?php

class ApClientesModel extends Mysql
{
  public function __construct()
  {
    parent::__construct("ap_clientes");
  }

  public function listMetaData()
  {
    return $this->createQueryBuilder()
      ->select("ca.*")
      ->from("p_tabla ta")
      ->innerJoin("p_campos ca", "ca.tablaId = ta.id")
      ->where("ta.tabla = '{$this->tableName}'")
      ->getMany();
  }

  public function listRecords($filters = [])
  {
    $columns = $this->listMetaData();
    $query = $this->createQueryBuilder()
      ->from("ap_clientes ap")
      ->leftJoin("clients c", "c.ap_cliente_id = ap.id")
      ->select("ap.id, ap.nombre, ap.ip, ap.`version`")
      ->addSelect("COUNT(c.id)", "countClientes")
      ->groupBy("ap.id, ap.nombre, ap.ip, ap.`version`")
      ->orderBy("ap.ip", "asc");
    if (isset($filters["querySearch"])) {
      $querySeach = $filters["querySearch"];
      $query->andWhere("ap.nombre LIKE '%{$querySeach}%'");
    }
    // agregar columns
    foreach ($columns as $col) {
      $query->addSelect("ap.{$col['campo']}", $col["campo"]);
    }
    // response
    return $query->getMany();
  }

  public function select_record(string $id)
  {
    $columns = $this->listMetaData();
    $query = $this->createQueryBuilder()
      ->from("ap_clientes ap")
      ->leftJoin("clients c", "c.ap_cliente_id = ap.id")
      ->select("ap.id, ap.nombre, ap.ip, ap.`version`")
      ->addSelect("COUNT(c.id)", "countClientes")
      ->groupBy("ap.id, ap.nombre, ap.ip, ap.`version`")
      ->where("ap.id = {$id}");
    // agregar columns
    foreach ($columns as $col) {
      $query->addSelect("ap.{$col['campo']}", $col["campo"]);
    }
    // response
    return $query->getOne();
  }

  public function update_record($id, $data = [])
  {
    return $this->createQueryBuilder()
      ->update()
      ->where("id = {$id}")
      ->set($data)
      ->execute();
  }

  public function create($data = [])
  {
    $meta = $this->listMetaData();
    $columns = ["nombre", "ip", "version"];
    foreach ($meta as $value) {
      array_push($columns, $value['campo']);
    }
    return $this->insertObject($columns, $data);
  }

  public function remove_record(string $id)
  {
    $query = "DELETE FROM {$this->tableName} WHERE id = '$id'";
    $result = $this->delete($query);
    return $result;
  }

  public function list_users_record(string $id)
  {
    return $this->createQueryBuilder()
      ->from("ap_clientes ap")
      ->innerJoin("clients cl", "cl.ap_cliente_id = ap.id")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->innerJoin("document_type d", "cl.documentid = d.id")
      ->where("ap.id = '{$id}'")
      ->select("c.id,c.internal_code,c.clientid,c.payday,c.create_invoice,c.days_grace,c.discount,c.discount_price,c.months_discount,c.contract_date,c.suspension_date,c.finish_date,c.state,CONCAT_WS(' ', cl.names, cl.surnames) AS client,cl.document,d.document AS name_doc,cl.latitud,cl.longitud,cl.email,cl.mobile,cl.mobile_optional,cl.net_ip,cl.address,cl.reference, cl.ap_cliente_id")
      ->addSelect("ap.nombre", "ap_name")
      ->getMany();
  }
}