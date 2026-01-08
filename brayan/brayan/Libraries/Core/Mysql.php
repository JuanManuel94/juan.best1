<?php
class Mysql extends Conexion
{
  private $conexion;
  private $queries;
  private $values;
  public $tableName;

  private $select = [];

  function __construct($tableName = "")
  {
    $this->conexion = new Conexion();
    $this->conexion = $this->conexion->conect();
    $this->tableName = $tableName;
  }

  public function setTableName($name)
  {
    $this->tableName = $name;
    return $this;
  }

  public function createQueryRunner()
  {
    if (!$this->conexion->inTransaction()) {
      $this->conexion->beginTransaction();
    }
  }

  public function commit()
  {
    if ($this->conexion->inTransaction()) {
      $this->conexion->commit();
    }
  }

  public function rollback()
  {
    if ($this->conexion->inTransaction()) {
      $this->conexion->rollBack();
    }
  }

  public function createQueryBuilder($alias = "")
  {
    return new BuilderQuery($this, isset($alias) ? $alias : "");
  }

  public function insert(string $query, array $worth)
  {
    $this->queries = $query;
    $this->values = $worth;
    $insert = $this->conexion->prepare($this->queries);
    $response = $insert->execute($this->values);
    if ($response) {
      $lastInsert = $this->conexion->lastInsertId();
    } else {
      $lastInsert = 0;
    }
    return $lastInsert;
  }

  public function insertMassive($column = [], $collection = [])
  {
    $columnStr = implode(", ", $column);
    $query = "INSERT INTO {$this->tableName}({$columnStr})";
    $values = [];
    $array_data = [];
    $countColumn = count($column);

    foreach ($collection as $row) {
      $array_row = [];

      foreach ($column as $col) {
        $value = $row[$col];
        array_push($values, $value);
      }

      $row_str = explode(" ", trim(str_repeat("? ", $countColumn)));
      $row_str = implode(",", $row_str);
      array_push($array_data, " ({$row_str}) ");
    }

    $query .= " VALUES " . implode(", ", $array_data);
    $this->queries = $query;
    $this->values = $values;
    $insert = $this->conexion->prepare($this->queries);
    return $insert->execute($this->values);
  }

  public function insertObject($column = [], $row = [])
  {
    $columnStr = implode(", ", $column);
    $query = "INSERT INTO {$this->tableName}({$columnStr})";
    $values = [];
    $array_data = [];
    $countColumn = count($column);

    foreach ($column as $col) {
      $value = $row[$col];
      array_push($values, $value);
    }

    $row_str = explode(" ", trim(str_repeat("? ", $countColumn)));
    $row_str = implode(",", $row_str);
    array_push($array_data, " ({$row_str}) ");

    $query .= " VALUES " . implode(", ", $array_data);
    return $this->insert($query, $values);
  }

  public function select(string $query)
  {
    $this->queries = $query;
    $result = $this->conexion->prepare($this->queries);
    $result->execute();
    $return = $result->fetch(PDO::FETCH_ASSOC);
    return $return;
  }

  public function run_simple_query($query)
  {
    $this->queries = $query;
    $result = $this->conexion->prepare($this->queries);
    $result->execute();
    return $result;
  }

  public function select_all(string $query)
  {
    $this->queries = $query;
    $result = $this->conexion->prepare($this->queries);
    $result->execute();
    $return = $result->fetchall(PDO::FETCH_ASSOC);
    return $return;
  }

  public function update(string $query, array $worth)
  {
    $this->queries = $query;
    $this->values = $worth;
    $update = $this->conexion->prepare($this->queries);
    $return = $update->execute($this->values);
    return $return;
  }

  public function delete(string $query)
  {
    $this->queries = $query;
    $result = $this->conexion->prepare($this->queries);
    $result->execute();
    return $result;
  }
}
