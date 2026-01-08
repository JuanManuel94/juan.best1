<?php
    class Conexion{
        private $conect;
        public function __construct(){
            $conexion = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
            try{
                $this->conect = new PDO($conexion,DB_USER,DB_PASSWORD);
                $this->conect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                $this->conect = "Error en la conexion";
                echo "Error: ".$e->getMessage();
            }
        }
        public function conect(){
            return $this->conect;
        }
    }
