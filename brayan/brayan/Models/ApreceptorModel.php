<?php

class ApreceptorModel extends Mysql {
  public function __construct(){
		parent::__construct("ap_receptor");
	}

  public function listMetaData() {
    return $this->createQueryBuilder()
      ->select("ca.*")
      ->from("p_tabla ta")
      ->innerJoin("p_campos ca", "ca.tablaId = ta.id")
      ->where("ta.tabla = '{$this->tableName}'")
      ->getMany();
  }

  public function listRecords($filters = []) {
    $query = $this->createQueryBuilder();
    if (isset($filters["querySearch"])) {
      $querySeach = $filters["querySearch"];
      $query->andWhere("nombre LIKE '%{$querySeach}%'");
    }
    return $query->getMany();
  }

  public function select_record(string $id) {
    return $this->createQueryBuilder()
      ->where("id = {$id}")
      ->getOne();
  }

  public function update_record($id, $data = []) {
    return $this->createQueryBuilder()
      ->update()
      ->where("id = {$id}")
      ->set($data)
      ->execute();
  }

  public function create($data = []) {
    $meta = $this->listMetaData();
    $columns = ["nombre", "ip", "version"];
    foreach ($meta as $value) {
      array_push($columns, $value['campo']);
    }
    return $this->insertObject($columns, $data);
  } 

  public function remove_record(string $id) {
    $query = "DELETE FROM {$this->tableName} WHERE id = '$id'";
    $result = $this->delete($query);
    return $result;
  }
}