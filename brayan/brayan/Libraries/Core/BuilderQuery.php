<?php

class BuilderQuery
{

  public static $mysql;
  public $arrayTable = [];
  public $arraySelect = [];
  public $arrayJoin = [];
  public $arrayWhere = [];
  public $arrayGroup = [];
  public $arrayOrder = [];
  public $arrayHaving = [];
  public $limit = "";

  public function __construct($mysql, $alias = "")
  {
    BuilderQuery::$mysql = $mysql;
    $this->arrayTable = ["{$mysql->tableName} {$alias}"];
    $this->arraySelect = ["*"];
    $this->arrayJoin = [];
    $this->arrayWhere = [];
    $this->arrayHaving = [];
    $this->arrayGroup = [];
    $this->arrayOrder = [];
    $this->limit = "";
  }

  public static function copy(BuilderQuery $instance)
  {
    $object = new BuilderQuery(BuilderQuery::$mysql);
    $object->arrayTable = $instance->arrayTable;
    $object->arraySelect = $instance->arraySelect;
    $object->arrayJoin = $instance->arrayJoin;
    $object->arrayWhere = $instance->arrayWhere;
    $object->arrayGroup = $instance->arrayGroup;
    $object->arrayOrder = $instance->arrayOrder;
    $object->arrayHaving = $instance->arrayHaving;
    $object->limit = $instance->limit;
    return $object;
  }

  public function select($column, $alias = "")
  {
    $this->arraySelect = ["{$column} {$alias}"];
    return $this;
  }

  public function addSelect($column, $alias = "")
  {
    array_push($this->arraySelect, "{$column} {$alias}");
    return $this;
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

  public function groupBy($groupStr)
  {
    $this->arrayGroup = [$groupStr];
    return $this;
  }

  public function addGroupBy($groupStr)
  {
    array_push($this->arrayGroup, $groupStr);
    return $this;
  }

  public function having($condition)
  {
    $this->arrayHaving = ["HAVING {$condition}"];
    return $this;
  }

  public function andHaving($condition)
  {
    $counter = count($this->arrayHaving);
    $signo = $counter == 0 ? "HAVING" : "AND";
    array_push($this->arrayHaving, "{$signo} {$condition}");
    return $this;
  }

  public function orHaving($condition)
  {
    $counter = count($this->arrayHaving);
    $signo = $counter == 0 ? "HAVING" : "OR";
    array_push($this->arrayHaving, "{$signo} {$condition}");
    return $this;
  }

  public function orderBy($column, $order = "ASC")
  {
    $this->arrayOrder = ["ORDER BY {$column} {$order}"];
    return $this;
  }

  public function addOrderBy($column, $order = "ASC")
  {
    $counter = count($this->arrayOrder);
    $signo = $counter == 0 ? "ORDER BY" : "";
    array_push($this->arrayOrder, "{$signo} {$column} {$order}");
    return $this;
  }

  public function setLimit(int $limit)
  {
    $this->limit = $limit;
    return $this;
  }

  public function getSql($logger = false)
  {
    $tableStr = implode(", ", $this->arrayTable);
    $selectStr = implode(", ", $this->arraySelect);
    $query = "SELECT {$selectStr} FROM {$tableStr}\n";
    if (count($this->arrayJoin)) {
      $query .= implode("\n", $this->arrayJoin) . "\n";
    }
    if (count($this->arrayWhere)) {
      $query .= implode("\n", $this->arrayWhere) . "\n";
    }
    if (count($this->arrayGroup)) {
      $query .= "GROUP BY " . implode(", ", $this->arrayGroup) . "\n";
    }
    if (count($this->arrayHaving)) {
      $query .= implode("\n", $this->arrayHaving) . "\n";
    }
    if (count($this->arrayOrder)) {
      $query .= implode(", ", $this->arrayOrder) . "\n";
    }
    if ($this->limit) {
      $query .= "LIMIT {$this->limit}";
    }
    if ($logger) {
      echo $this->getSql();
    }
    return $query;
  }

  public function getMany($logger = false)
  {
    return BuilderQuery::$mysql->select_all($this->getSql($logger));
  }

  public function getOne($logger = false)
  {
    return BuilderQuery::$mysql->select($this->getSql($logger));
  }

  public function update($alias = "")
  {
    return new BuilderQueryUpdate(BuilderQuery::$mysql, $alias);
  }

  public function delete($alias = "")
  {
    return new BuilderQueryDelete(BuilderQuery::$mysql, $alias);
  }
}