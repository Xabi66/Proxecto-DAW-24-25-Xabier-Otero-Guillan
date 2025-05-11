<?php

define("DB_DSN","mysql:host=mariadb;dbname=qkorte");
define("DB_USER","root");
define("DB_PASS","bitnami");

class Model{
    //Permite la conexion con la BD
    protected function getConnection(){
        $pdo = null;
        try {
            $pdo = new PDO(DB_DSN,DB_USER, DB_PASS);
        } catch (PDOException $e) {
            error_log("Error conectando a la base de datos...");
            error_log($e->getMessage());
        }
        return $pdo;
    }
}