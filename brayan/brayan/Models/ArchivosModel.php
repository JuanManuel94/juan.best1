<?php

class ArchivosModel extends Mysql {
  public function __construct(){
		parent::__construct("archivos");
	}

  public function list_records($filters = []) {
    $query = $this->createQueryBuilder()
      ->from("archivos")
      ->select("*");
    if (isset($filters['tabla'])) {
      $condition = $filters['tabla'];
      $query->andWhere("tabla = '{$condition}'");
    }
    if (isset($filters['object_id'])) {
      $condition = $filters['object_id'];
      $query->andWhere("object_id = '{$condition}'");
    }
    return $query->getMany();
  }

  public function select_record(string $id) {
    return $this->createQueryBuilder()
      ->from("archivos")
      ->select("*")
      ->where("id = '$id'")
      ->getOne();
  }

  public function exists($tabla, $object_id, $name) {
    $query = $this->createQueryBuilder()
      ->from("archivos")
      ->select("id")
      ->where("tabla = '{$tabla}'")
      ->andWhere("object_id = '{$object_id}'")
      ->andWhere("nombre = '{$name}'")
      ->getOne();
    return $query ? true : false;
  }

  public function create($data = []) {
    $columns = ["nombre", "size", "ruta", "tipo", "tabla", "object_id"];
    return $this->insertObject($columns, $data);
  } 

  public function remove_record(string $id) {
    $query = "DELETE FROM {$this->tableName} WHERE id = '$id'";
    $result = $this->delete($query);
    return $result;
  }
}