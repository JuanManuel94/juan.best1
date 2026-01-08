<?php

class Archivos extends Controllers {

  public function __construct() {
    parent::__construct();
    session_start();
    if (empty($_SESSION["login"])) {
      header('Location: '.base_url().'/login');
			die();
    }
  }

  private function getPath($pathRelative) {
    return __DIR__ . "/../{$pathRelative}";
  }

  public function list_records() {
    $data = $this->model->list_records($_GET);
    $this->json($data);
  }

  public function upload() {
    if (!isset($_FILES['archivo'])) {
        return $this->json([
            "success" => false,
            "message" => "El archivo es obligatorio"
        ]);
    } 

    $file_tmp = $_FILES['archivo']['tmp_name'];
    $tabla = $_POST['tabla'];
    $object_id = $_POST['object_id'];
    $name = $_FILES['archivo']['name'];
    $tipo = $_FILES['archivo']['type'];
    $ruta = "Uploads/{$tabla}/{$object_id}";

    // Validar si el archivo ya existe
    $existsFile = $this->model->exists($tabla, $object_id, $name);

    // Obtener la ruta real del directorio
    $fullPath = $this->getPath($ruta);

    // Verificar si la carpeta ya existe antes de crearla
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0777, true);
    }

    $ruta .= "/{$name}";

    if ($existsFile) {
        return $this->json([
            "success" => false, 
            "message" => "El archivo ya existe!",
        ]);
    }

    $payload = [
        "nombre" => $name,
        "size" => $_FILES['archivo']['size'],
        "tipo" => $tipo,
        "ruta" => $ruta,
        "tabla" => $tabla,
        "object_id" => $object_id
    ];

    try {
        if (!move_uploaded_file($file_tmp, $this->getPath($ruta))) {
            throw new Exception("No se pudo mover el archivo");
        } else {
            $this->model->create($payload); 
            $this->json([
                "success" => true, 
                "message" => "Archivo subido correctamente!",
            ]);
        }
    } catch (\Throwable $th) {
        $this->json([
            "success" => false, 
            "message" => "No se pudo subir el archivo",
            "err" => $th->getMessage()
        ]);
    }
}


  public function remove_record(string $id) {
    try {
      $data = $this->model->select_record($id);
      if (!$data) throw new Exception("No existe el registro");

      $result = $this->model->remove_record($id);
      $ruta = $data['ruta'];

      if (file_exists($this->getPath($ruta))) {
        unlink($this->getPath($ruta));
      }

      $this->json([
        "success" => true,
        "message" => "El archivo se eliminó correctamente!"
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "success" => false,
        "message" => "No se pudo eliminar el archivo",
        "err" => $th->getMessage()
      ]);
    }
  }

  public function download(string $id) {
    $data = $this->model->select_record($id);
    if (!$data) return $this->send("No se encontró el archivo");
    $file_binary = base64_decode($data['data']);
    $nombre = $data['nombre'];
    $tipo = $data['tipo'];
    $ruta = __DIR__ . "/../{$data['ruta']}";
    header("Content-type: $tipo"); 
    header('Content-Disposition: inline; filename="'.$nombre.'"');
    readfile($ruta);
  }
}