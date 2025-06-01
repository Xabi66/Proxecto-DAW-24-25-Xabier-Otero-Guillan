<?php
include_once("Controller.php");
include_once(PATH_MODEL."ServiceModel.php");

class ServiceController extends Controller{

    public function get($id){
        $model = new ServiceModel();

        if(count($id)!=1){
            Controller::sendNotFound("Los servicios se identifican por un solo id");
            die();
        }
        $service = $model->get($id[0]);
        
        if($service==null){
            Controller::sendNotFound("El id no se corresponde con ningun servicio");
            die();
        }

        echo $service->toJson();
    }

    public function getAll(){
        $model = new ServiceModel();
        $service = $model->getAll();
        echo json_encode($service,JSON_PRETTY_PRINT);
    }

    public function insert($object){
        $model = new ServiceModel();
        $service = Service::fromJson($object);

        //Iniciamos la sesión
        session_start();
        //Pilla el rol de la sesión
        $rol = $_SESSION['user_rol'];
        //Comprobamos que quien borrar el servicio es un administrador
        if($rol!=1){
            Controller::sendNotFound("Error, solo pueden crear servicios los administradores");
            die();  
        }

        //Segun si fue insertado envia un mensaje u otro
        if($model->insert($service)){
            Controller::sendSuccess("Servicio Creado");
        }else{
            Controller::sendNotFound("Error. No se ha podido crear el servicio");
        }            
    }

    public function delete($id) {
        //Si hay mas de un id dará error
        if(count($id)!=1){
            Controller::sendNotFound("Los servicios se identifican por un solo id");
            die();
        }

        //Iniciamos la sesión
        session_start();
        //Pilla el rol de la sesión
        $rol = $_SESSION['user_rol'];
        //Comprobamos que quien borrar el servicio es un administrador
        if($rol!=1){
            Controller::sendNotFound("Error, solo pueden borrar servicios los administradores");
            die();  
        }
    
        $model = new ServiceModel();
        if($model->delete($id[0])){
            echo "Servicio eliminado";
        }else{
            Controller::sendNotFound("No se ha podido eliminar el servicio");
        }
    }

    //Permite editar un servicio existente
    public function update($id, $object){
        if(count($id)!=1){
            Controller::sendNotFound("Los servicios se identifican por un solo id");
            die();
        }

        //Iniciamos la sesión
        session_start();
        //Pilla el rol de la sesión
        $rol = $_SESSION['user_rol'];
        //Comprobamos que quien borrar el servicio es un administrador
        if($rol!=1){
            Controller::sendNotFound("Error, solo pueden editar servicios los administradores");
            die();  
        }

        $model = new ServiceModel();
        $datos = Service::fromJson($object);

        if($model->update($datos,$id[0])){
            Controller::sendSuccess("Se ha modificado el servicio");
        }else{
            Controller::sendNotFound("Error, tiene que cambiar algun dato del servicio antes de confirmar");
        }  
    }
}