<?php

class OtrosPagosModel extends Mysql
{
  public function __construct()
  {
    parent::__construct("otros_ingresos");
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

  public function resume($tipo = "")
  {
    $currentDate = date("Y-m-d");
    $query = $this->createQueryBuilder()
      ->where("tipo = '{$tipo}'")
      ->select("IFNULL(SUM(monto), 0) monto");
    $total = $query->getOne();
    $toDay = $query->andWhere("fecha LIKE '{$currentDate}'")->getOne();
    return [
      "total" => $total['monto'],
      "today" => $toDay['monto']
    ];
  }

  public function resumeEgresos()
  {
    return $this->resume("EGRESO");
  }

  public function resumeIngresos()
  {
    return $this->resume("INGRESO");
  }

  public function listRecords($filters = [])
  {
    $query = $this->createQueryBuilder()
      ->select("o.*, u.username operador")
      ->from("otros_ingresos o")
      ->innerJoin("users u", "u.id = o.userId");
    if (isset($filters["querySearch"])) {
      $querySeach = $filters["querySearch"];
      $query->andWhere("nombre LIKE '%{$querySeach}%'");
    }

    // filter dates
    $dateStart = $filters["dateStart"];
    $dateOver = $filters["dateOver"];
    if (isset($dateStart) && isset($dateOver)) {
      $query->andWhere("o.fecha BETWEEN '{$dateStart}' AND '{$dateOver}'");
    } else if (isset($dateStart)) {
      $query->andWhere("o.fecha <= '{$dateStart}'");
    } else if (isset($dateOver)) {
      $query->andWhere("o.fecha <= '{$dateOver}'");
    }

    return $query->getMany();
  }

  public function select_record(string $id)
  {
    return $this->createQueryBuilder()
      ->where("id = {$id}")
      ->getOne();
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
    $columns = ["tipo", "fecha", "monto", "descripcion", "userId", "state"];

    // Garantiza que 'state' tenga un valor predeterminado
    if (!isset($data['state']) || empty($data['state'])) {
      $data['state'] = ($data['tipo'] === "INGRESO") ? "NORMAL" : "PENDIENTE";
    }

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
}