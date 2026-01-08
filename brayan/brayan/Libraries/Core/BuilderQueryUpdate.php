<?php

class BuilderQueryUpdate
{
  function __construct($mysql, $alias = "")
  {
    $this->mysql = $mysql;
    $this->arrayTable = ["{$mysql->tableName} {$alias}"];
    $this->arrayWhere = [];
    $this->arrayData = [];
  }

  public function from($name, $alias = "")
  {
    $this->arrayTable = ["{$name} {$alias}"];
    return $this;
  }

  public function addFrom($name, $alias = "")
  {
    array_push($this->arrayTable, "{$name} {$alias}");
    return $this;
  }

  function set($data = [])
  {
    $this->arrayData = $data;
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
    $query = "UPDATE " . implode(", ", $this->arrayTable) . "\n";
    $arraySet = [];
    foreach ($this->arrayData as $attr => $value) {
      array_push($arraySet, "{$attr} = ?");
    }
    $query .= "SET " . implode(", ", $arraySet) . "\n";
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
    return $this->mysql->update($this->getSql($log), array_values($this->arrayData));
  }
}