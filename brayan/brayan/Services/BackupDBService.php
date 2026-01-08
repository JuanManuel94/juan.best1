<?php

class BackupDBService
{
  private Mysql $mysql;
  private object $business;

  public function __construct()
  {
    $this->mysql = new Mysql();
  }

  public function execute()
  {
    if (empty($this->business)) {
      $this->business = $this->findBusiness();
    }

    $date = date('d-m-Y');
    $path = "Assets/backups/";
    $backupName = str_replace("-", "", $this->business->business_name);
    $database = "backup_" . $backupName . "_" . $date . ".sql";
    $database_zip = "backup_" . $backupName . "_" . $date . ".zip";

    $exists = $this->findBackup($database_zip);

    if ($exists) {
      return [
        "success" => false,
        "message" => "El backup ya existe!!!"
      ];
    }

    $path = "Assets/backups/";
    $tables = array();
    $query = "SHOW TABLES";
    $request = $this->mysql->select_all($query);
    if (empty($request)) {
      return [
        "success" => false,
        "message" => "No se pudo generar el backup"
      ];
    }

    // recorrer tablas
    foreach ($request as $row) {
      $tables[] = $row[TABLES_NAME];
    }

    $sql = 'SET FOREIGN_KEY_CHECKS=0;' . "\n\n";
    $sql .= 'CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ";\n\n";
    $sql .= 'USE ' . DB_NAME . ";\n\n";

    foreach ($tables as $table) {
      $query = "SELECT * FROM " . $table;
      $request = $this->mysql->run_simple_query($query);

      if (empty($request)) {
        return [
          "success" => false,
          "message" => "No se pudo generar el backup"
        ];
      }

      $numFields = $request->columnCount();
      $sql .= 'DROP TABLE IF EXISTS ' . $table . ';';
      $query2 = "SHOW CREATE TABLE " . $table;
      $request2 = $this->mysql->select($query2);
      $sql .= "\n\n" . $request2['Create Table'] . ";\n\n";

      for ($i = 0; $i < $numFields; $i++) {
        while ($row = $request->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
          $sql .= 'INSERT INTO ' . $table . ' VALUES(';
          for ($j = 0; $j < $numFields; $j++) {
            $row[$j] = addslashes(empty($row[$j]) ? "" : $row[$j]);
            $row[$j] = str_replace("\n", "\\n", $row[$j]);
            if (isset($row[$j])) {
              $sql .= '"' . $row[$j] . '"';
            } else {
              $sql .= '""';
            }
            if ($j < ($numFields - 1)) {
              $sql .= ',';
            }
          }
          $sql .= ");\n";
        }
      }

      $sql .= "\n\n\n";
    }

    $sql .= 'SET FOREIGN_KEY_CHECKS=1;';
    $handle = fopen($database, 'w+');

    $saved = fwrite($handle, $sql);
    if (!$saved) {
      return [
        "success" => false,
        "message" => "No se pudo generar el backup"
      ];
    }

    fclose($handle);
    $zip = new ZipArchive();
    $name_zip = "backup_" . $backupName . "_" . $date . ".zip";
    $saved_zip = $zip->open($name_zip, ZipArchive::CREATE);

    if (!$saved_zip) {
      return [
        "success" => false,
        "message" => "No se pudo guardar el ZIP"
      ];
    }

    $zip->addFile($database);
    $zip->close();
    chmod($path, 0777);
    $moved = rename($name_zip, $path . $name_zip);

    if (!$moved) {
      return [
        "success" => false,
        "message" => "No se pudo mover el ZIP"
      ];
    }

    unlink($database);
    $filesize = filesize_formatted($path . $name_zip);
    $datetime = date("Y-m-d H:i:s");
    $query_insert = "INSERT INTO backups(archive,size,registration_date) VALUES(?,?,?)";
    $data = array($name_zip, $filesize, $datetime);
    $insert = $this->mysql->insert($query_insert, $data);

    if (!$insert) {
      return [
        "success" => false,
        "message" => "No se pudo guardar el backup"
      ];
    }

    return [
      "success" => true,
      "message" => "Backup generado!!!"
    ];
  }

  public function setMysql(Mysql $mysql)
  {
    $this->mysql = $mysql;
  }

  public function setBusiness(object $business)
  {
    $this->business = $business;
  }

  public function findBusiness()
  {
    return (object) $this->mysql->createQueryBuilder()
      ->from("business")
      ->getOne();
  }

  public function findBackup(string $name)
  {
    return $this->mysql->createQueryBuilder()
      ->from("backups")
      ->where("archive = '{$name}'")
      ->getOne();
  }
}