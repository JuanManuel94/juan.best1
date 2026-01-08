<?php

class BuilderQueryDelete
{
  function __construct($mysql, $alias = "")
  {
    $this->mysql = $mysql;
    $this->arrayTable = ["{$mysql->tableName} {$alias}"];
    $this->arrayWhere = [];
    $this->arrayTableDelete = [];
    $this->arrayJoin = [];
  }

  public function from($name, $alias = "")
  {
    $this->arrayTable = ["{$name} {$alias}"];
    return $this;
  }

  public function setTableDelete(array $arrayTableDelete)
  {
    $this->arrayTableDelete = $arrayTableDelete;
    return $this;
  }

  public function addFrom($name, $alias = "")
  {
    array_push($this->arrayTable, "{$name} {$alias}");
    return $this;
  }


  public function innerJoin($table, $condition)
  {
    array_push($this->arrayJoin, "INNER JOIN {$table} ON {$condition}");
    return $this;
  }

  public function leftJoin($table, $condition)
  {
    array_push($this->arrayJoin, "LEFT JOIN {$table} ON {$condition}");
    return $this;
  }

  public function where($condition)
  {
    $this->arrayWhere = ["WHERE {$condition}"];
    return $this;
  }

  public function andWhere($condition)
  {
    $counter = count($this->arrayWhere);
    $signo = $counter == 0 ? "WHERE" : "AND";
    array_push($this->arrayWhere, "{$signo} {$condition}");
    return $this;
  }

  public function orWhere($condition)
  {
    $counter = count($this->arrayWhere);
    $signo = $counter == 0 ? "WHERE" : "OR";
    array_push($this->arrayWhere, "{$signo} {$condition}");
    return $this;
  }

  function getSql($log = false)
  {
    $query = "DELETE " . implode(",", $this->arrayTableDelete);
    $query .= " FROM " . implode(", ", $this->arrayTable) . "\n";

    if (count($this->arrayJoin)) {
      $query .= implode("\n", $this->arrayJoin) . "\n";
    }

    if (isset($this->arrayWhere)) {
      $query .= implode("\n", $this->arrayWhere) . "\n";
    }

    if ($log) {
      echo $query;
    }
    return $query;
  }

  function execute($log = false)
  {
    return $this->mysql->delete($this->getSql($log));
  }
}