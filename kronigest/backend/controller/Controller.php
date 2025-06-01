<?php
include_once("UserController.php");
include_once("ServiceController.php");
include_once("ReserveController.php");

/**
 * Definicion de los nombres asociados a cada controlador en la URI.
 */
define("CONTROLLER_USER", "user");
define("CONTROLLER_SERVICE", "service");
define("CONTROLLER_RESERVE", "reserve");

class ControllerException extends Exception{
    function __construct()
    {
        parent::__construct("Error obteniendo el controlador solicitado.");
    }
}

abstract class Controller
{
    //Permite mandar un mensaje personalizado de error al json
    public static function sendNotFound($mensaje)
    {
        error_log($mensaje);
        header("HTTP/1.1 404 Not Found");
        $mensaje = ["error" => $mensaje];
        echo json_encode($mensaje, JSON_PRETTY_PRINT);
    }

    //Permite mandar un mensaje personalizado de que la operacion fue exitosa al json
    public static function sendSuccess($mensaje)
    {
        $mensaje = ["success" => $mensaje];
        echo json_encode($mensaje, JSON_PRETTY_PRINT);
    }
    
    //Crea el controlador especifico
    public static function getController($nombre): Controller
    {
        $controller = null;
        switch ($nombre) {
            case CONTROLLER_USER:
                $controller = new UserController();
                break;
            case CONTROLLER_SERVICE:
                $controller = new ServiceController();
                break;
            case CONTROLLER_RESERVE:
                $controller = new ReserveController();
                break;
            default:
                throw new ControllerException();
        }
        return $controller;
    }
    
    //Metodos crud a implementar
    public abstract function get($id);
    public abstract function getAll();
    public abstract function delete($id);
    public abstract function update($id, $object);
    public abstract function insert($object);
}