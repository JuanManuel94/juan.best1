<?php

class CajaNapModel extends Mysql
{
  public function __construct()
  {
    parent::__construct("caja_nap");
  }

  public function listRecords($filters = [])
  {
    $query = $this->createQueryBuilder()
      ->from("caja_nap", "c")
      ->leftJoin("zonas z", "z.id = c.zonaId")
      ->select("c.*, z.nombre_zona zona")
      ->orderBy("c.nombre", "asc");

    if (isset($filters["querySearch"])) {
      $querySeach = $filters["querySearch"];
      $query->andWhere("c.nombre LIKE '%{$querySeach}%'");
    }

    if (isset($filters["zonaId"])) {
      $condition = $filters["zonaId"];
      $query->andWhere("c.zonaId = {$condition}");
    }

    if (isset($filters["tipo"])) {
      $condition = $filters["tipo"];
      $query->andWhere("c.tipo = '{$condition}'");
    }

    $result = $query->getMany();
    foreach ($result as $key => $item) {
      $item["array_puertos"] = $this->list_puertos($item['id']);
      $result[$key] = $item;
    }
    return $result;
  }

  public function select_record(string $id)
  {
    $data = $this->createQueryBuilder()
      ->from("caja_nap")
      ->where("id = {$id}")
      ->getOne();
    if (!$data)
      return null;
    $data['state'] = $this->get_state($data['id']);
    return $data;
  }

  public function update_record($id, $data = [])
  {
    $caja = $this->select_record($id);
    if (!$caja)
      throw new Exception();
    $isChangePort = $this->get_state($caja['id']);
    $currentPorts = (int) $caja['puertos'];
    $newPorts = (int) $data['puertos'];
    $isUpdated = $this->createQueryBuilder()
      ->update()
      ->where("id = {$id}")
      ->set($data);
    // validar actualización
    if ($isChangePort) {
      $data['puertos'] = $currentPorts;
      return $isUpdated->set($data)->execute();
    } else if ($currentPorts == $newPorts) {
      return $isUpdated->execute();
    } else if ($currentPorts > $newPorts) {
      $diff = $currentPorts - $newPorts;
      $counter = $currentPorts - $diff;
      $dataId = [];
      for ($i = $counter + 1; $i <= $currentPorts; $i++) {
        array_push($dataId, $i);
      }
      $this->delete_puertos($id, $dataId);
      return $isUpdated->execute();
    } else if ($currentPorts < $newPorts) {
      $diff = $newPorts - $currentPorts;
      $counter = $currentPorts + 1;
      $puertos = [];
      for ($i = $counter; $i <= $newPorts; $i++) {
        array_push($puertos, $i);
      }
      $this->add_puertos($id, $puertos);
      return $isUpdated->execute();
    } else {
      return false;
    }
  }

  public function create($data = [])
  {
    $this->createQueryBuilder();
    try {
      $columns = ["nombre", "color_tubo", "color_hilo", "zonaId", "tipo", "longitud", "latitud", "puertos", "ubicacion", "detalles"];
      $napId = $this->insertObject($columns, $data);
      $puertos = [];
      for ($i = 0; $i < $data['puertos']; $i++) {
        array_push($puertos, $i + 1);
      }

      if (count($puertos) > 0) {
        $this->add_puertos($napId, $puertos);
      }

      $this->commit();
      return $napId;
    } catch (\Throwable $th) {
      $this->rollback();
      throw $th;
    }
  }

  public function remove_record(string $id)
  {
    $query = "DELETE FROM {$this->tableName} WHERE id = '$id'";
    $result = $this->delete($query);
    return $result;
  }

  public function list_puertos(string $id)
  {
    return $this->createQueryBuilder()
      ->select("nc.*")
      ->addSelect("n.nombre", "nap")
      ->addSelect("cl.id", "cliente_id")
      ->addSelect("CONCAT(cl.names, CONCAT(' ', cl.surnames))", "cliente")
      ->addSelect("IF(cl.id, 0, 1)", "state")
      ->from("caja_nap_clientes nc")
      ->innerJoin("caja_nap n", "n.id = nc.nap_id")
      ->leftJoin("clients cl", "cl.nap_cliente_id = nc.id")
      ->where("nap_id = {$id}")
      ->orderBy("nc.puerto", "ASC")
      ->getMany();
  }

  public function search_puertos($filters = [])
  {
    $query = $this->createQueryBuilder()
      ->select("cnc.id, CONCAT(nap.nombre, CONCAT('-', cnc.puerto)) nombre")
      ->from("caja_nap nap")
      ->innerJoin("caja_nap_clientes cnc", "cnc.nap_id = nap.id")
      ->leftJoin("clients cl", "cl.nap_cliente_id = cnc.id")
      ->where("cl.id is null")
      ->andWhere("nap.tipo = 'nap'")
      ->orderBy("nombre", "asc");
    if (isset($filters["querySearch"])) {
      $querySeach = $filters["querySearch"];
      $query->andWhere("CONCAT(nap.nombre, CONCAT('-', cnc.puerto)) LIKE '%{$querySeach}%'");
    }
    return $query->getMany();
  }

  public function get_state(string $id)
  {
    $data = $this->createQueryBuilder()
      ->select("IF(COUNT(*), 1, 0) counter")
      ->from("caja_nap_clientes nc")
      ->innerJoin("clients cl", "cl.nap_cliente_id = nc.id")
      ->where("nap_id = {$id}")
      ->getOne();
    return (int) $data["counter"];
  }

  public function delete_puertos(string $id, $puertos = [])
  {
    $condition = "'" . implode("', '", $puertos) . "'";
    $query = "DELETE FROM caja_nap_clientes WHERE nap_id = $id AND puerto IN ({$condition})";
    $result = $this->delete($query);
    return $result;
  }

  public function add_puertos(string $id, $puertos = [])
  {
    $mysql = new Mysql("caja_nap_clientes");
    $collection = [];
    foreach ($puertos as $key => $puerto) {
      array_push($collection, [
        "puerto" => $puerto,
        "nap_id" => $id
      ]);
    }
    $mysql->insertMassive(["puerto", "nap_id"], $collection);
  }
}