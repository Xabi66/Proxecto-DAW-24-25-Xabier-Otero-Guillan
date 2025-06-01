<?php
include_once("globals.php");
include_once("controller/Controller.php");

//Obtiene los id de la uri como un array
function getIds(array $uri):array{
    $ids = [];
    for($i=count($uri)-1;$i>=0;$i--){
        if(intval($uri[$i])){
            $ids[] = $uri[$i]; 
        }
    }
    return array_reverse($ids);
}

/**
 * Este fichero captura todas la peticiones a nuestra aplicación.
 * Aqui se parsea la uri para decidir el controlador y la acción que debemos ejecutar.
 */
//Metodo del crud escogido
$metodo = $_SERVER["REQUEST_METHOD"];
$uri = $_SERVER["REQUEST_URI"];
$uri = explode("/", $uri);
//La ruta será localhost/backend/route.php/user por ejemplo, asi que user es el endpoint;
$endpoint = $uri[3];
$id = null;

/**
 * Crea el controlador en base al endpoint a partir de la clase Controller
 */
try {
    $controlador = Controller::getController($endpoint);
} catch (ControllerException $th) {
    Controller::sendNotFound("Error obteniendo el endpoint " . $endpoint);
    die();
}

//Si despues del endpoint hay solo un elemento y se trata de alguno de los metodos esperados que no son crud
if (count($uri) == 5 && ($uri[4] === 'login' || $uri[4] === 'sesion' || $uri[4] === 'logout' || $uri[4] === 'shifts' || $uri[4] === 'history' || $uri[4] === 'pending')) {
    //Si se realizó la llamada desde el endpoint user
    if ($endpoint === 'user') {
        $controlador = new UserController();
        switch ($uri[4]) {
            case 'login':
                $json = file_get_contents('php://input');
                $controlador->login($json);
                break;
            case 'logout':
                $json = file_get_contents('php://input');
                $controlador->logout();
                break;
            case 'sesion':
                $controlador->sesion();
                break;
        }
    }
    //Si se realizó la llamada desde el endpoint reserve
    if ($endpoint === 'reserve') {
        $controlador = new ReserveController();
        switch ($uri[4]) {
            case 'shifts':
                $json = file_get_contents('php://input');
                $controlador->shifts($json);
                break;
            case 'history':
                $controlador->history();
                break;
            case 'pending':
                $json = file_get_contents('php://input');
                $controlador->pending($json);
                break;
        }
    }
} else {
/*Si la uri mide mas de lo esperado y no es ninguno de los metodos anteriores significa que tendrá uno o mas id, 
por lo que se llama a la funcion correspondiente*/
if (count($uri) >= 5) {
    try {
        $id = getIds($uri);
    } catch (Throwable $th) {
        Controller::sendNotFound("Error en la peticion. El parámetro debe ser un id correcto.");
        die();
    }
}

//Segun el metodo de CRUD seleccionado
switch ($metodo) {
    case 'POST':
        $json = file_get_contents('php://input');
        $controlador->insert($json);
        break;
    case 'GET':
            if (isset($id)) {
                $controlador->get($id);
            } else {
                $controlador->getAll();
            }           
        break;
    case 'DELETE':
        if (isset($id)) {
            $controlador->delete($id);
        } else {
            Controller::sendNotFound("Es necesario indicar el id correcto");
        }
        break;
    case 'PUT':
        if (isset($id)) {
            $json = file_get_contents('php://input');
            $controlador->update($id, $json);
        } else {
            Controller::sendNotFound("Es necesario indicar el id");
        }
        break;
    default:
        Controller::sendNotFound("Método HTTP no disponible.");
}    
}