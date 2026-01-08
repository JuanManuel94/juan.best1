<?php

class CamposModel extends Mysql {
  public function __construct(){
		parent::__construct("p_campos");
	}
  
  private $types = [
    "varchar" => "varchar(255)",
    "int" => "int(11)",
    "decimal" => "decimal(12, 2)"
  ];

  public function listRecords($filters = []) {
    $query = $this->createQueryBuilder()
      ->from("p_campos ca")
      ->select("ta.id, ta.nombre, ta.tabla")
      ->groupBy("ta.id, ta.nombre, ta.tabla")
      ->innerJoin("p_tabla ta", "ta.id = ca.tablaId");
    if (isset($filters["querySearch"])) {
      $querySeach = $filters["querySearch"];
      $query->andWhere("nombre LIKE '%{$querySeach}%'");
    }
    $tables = $query->getMany();
    foreach ($tables as $key => $table) {
      $campos = $this->createQueryBuilder()
        ->where("tablaId = {$table['id']}")
        ->getMany();
      $table['campos'] = $campos;
      $tables[$key] = $table;
    }

    return $tables;
  }

  public function select_record(string $id) {
    return $this->createQueryBuilder()
      ->where("id = {$id}")
      ->getOne();
  }

  public function update_record($id, $data = []) {
    $tableId = $data['tablaId'];
    $table = $this->createQueryBuilder()
      ->from("p_tabla")
      ->where("id = {$tableId}")
      ->getOne();
    if (!isset($table)) throw new Exception("La tabla no existe!");
    $tipo = $this->types[$data['tipo']];
    $campo = str_ireplace(" ", "_", strtolower($data['campo']));
    $isRequired = (int)$data['obligatorio'] ? "NOT NULL" : "";
    $tableName = $table['tabla'];
    $query = "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$campo}` {$tipo} $isRequired";
    $this->run_simple_query($query);
    return $this->createQueryBuilder()
      ->update()
      ->where("id = {$id}")
      ->set($data)
      ->execute();
  }

  public function create($data = []) {
    $tableId = $data['tablaId'];
    $table = $this->createQueryBuilder()
    ->from("p_tabla")
    ->where("id = {$tableId}")
    ->getOne();
    if (!isset($table)) throw new Exception("La tabla no existe!");
    $columns = ["nombre", "campo", "obligatorio", "tablaId", "tipo"];
    $tipo = $this->types[$data['tipo']];
    $campo = str_ireplace(" ", "_", strtolower($data['nombre']));
    $isRequired = (int)$data['obligatorio'] ? "NOT NULL" : "";
    $tableName = $table['tabla'];
    $query = "ALTER TABLE {$tableName} ADD {$campo} {$tipo} $isRequired";
    $this->run_simple_query($query);
    $data["campo"] = $campo;
    return $this->insertObject($columns, $data);
  } 

  public function remove_record(string $id) {
    $item = $this->createQueryBuilder()
      ->select("ca.id, ta.tabla")
      ->addSelect("ca.campo", "campo")
      ->from("p_campos ca")
      ->innerJoin("p_tabla ta", "ta.id = ca.tablaId")
      ->where("ca.id = {$id}")
      ->getOne();
    if (!isset($item)) throw new Exception("El campo no existe!");
    $tableName = strtolower($item['tabla']);
    $campo = strtolower($item['campo']);
    $queryDelete = "ALTER TABLE `{$tableName}` DROP COLUMN `{$campo}`";
    $this->run_simple_query($queryDelete);
    $query = "DELETE FROM {$this->tableName} WHERE id = '$id'";
    $result = $this->delete($query);
    return $result;
  }

  public function list_tablas() {
    return $this->createQueryBuilder()
      ->from("p_tabla")
      ->getMany();
  }

  public function select_tabla(string $id) {
    return $this->createQueryBuilder()
      ->from("p_tabla")
      ->where("id = {$id}")
      ->getOne();
  }

  public function list_campos(string $id) {
    return $this->createQueryBuilder() 
      ->where("tablaId = {$id}")
      ->getMany();
  }
}