<?php

class PlanillaWspSaveService
{
  private Mysql $mysql;

  public function __construct()
  {
    $this->mysql = new Mysql("business_wsp");
  }

  public function execute($id, $payload = [])
  {
    $plantilla = $this->find($id);
    if ($plantilla) {
      // actualizar
      return $this->update($id, $payload);
    }
    // crear
    return $this->create([...$payload, "id" => $id]);
  }

  private function find(string $id)
  {
    return (Object) $this->mysql->createQueryBuilder()
      ->from("business_wsp", "bw")
      ->where("bw.id = '{$id}'")
      ->getOne();
  }

  private function update(string $id, array $payload)
  {
    return $this->mysql->createQueryBuilder()
      ->update()
      ->from("business_wsp")
      ->set($payload)
      ->where("id = '{$id}'")
      ->execute();
  }

  private function create(array $payload)
  {
    $columns = array_keys($payload);
    $values = array_values($payload);
    return $this->mysql->insertObject($columns, $values);
  }
}