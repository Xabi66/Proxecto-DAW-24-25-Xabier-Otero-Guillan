<?php
include_once("Controller.php");
include_once(PATH_MODEL."UserModel.php");

class UserController extends Controller{

    public function get($id){
        $model = new UserModel();

        if(count($id)!=1){
            Controller::sendNotFound("Los usuarios se identifican por un solo id");
            die();
        }
        $user = $model->get($id[0]);
        
        if($user==null){
            Controller::sendNotFound("El id no se corresponde con ningun usuario");
            die();
        }

        echo $user->toJson();
    }

    public function getAll(){
        $model = new UserModel();
        $disco = $model->getAll();
        echo json_encode($disco,JSON_PRETTY_PRINT);
    }

    //Crea un nuevo usuario a partir del objeto proporcionado y intenta insertarlo
    public function insert($object){
        $model = new UserModel();
        $user = User::fromJson($object);

        //Antes de intentar insertarlo comprueba si el email ya esta registrado. Si lo esta manda un mensaje de error y no sigue
        if ($model->checkEmail($user)) {
            Controller::sendNotFound("Error. Este correo ya esta registrado.");
        } else {
            //Segun si fue insertado envia un mensaje u otro
            if($model->insert($user)){
                Controller::sendSuccess("Usuario Creado");
            }else{
                Controller::sendNotFound("Error. No se ha podido crear una cuenta");
            }            
        }
    }

    public function delete($id) {
        //Si hay mas de un id dará error
        if(count($id)!=1){
            Controller::sendNotFound("Los usuarios se identifican por un solo id");
            die();
        }
        //Iniciamos la sesión
        session_start();
        //Pilla el id de la sesión
        $idUsuario = $_SESSION['user_id'];
        //Comprobamos que no se este intentando borrar un usuario que no sea uno normal, ya que los admin no deben poder ser borrados
        if($_SESSION['user_rol']!=2){
            Controller::sendNotFound("Error, solo se pueden borrar usuarios normales");
            die();  
        }
        //Comprobamos si la id proporcionada coincide con la de la sesión
        if($id[0]!=$idUsuario){
            Controller::sendNotFound("Error, el id proporcionado no coincide con el usuario de la sesión actual");
            die();  
        }
    
        $model = new UserModel();

        if($model->delete($idUsuario)){
            $this->logout();
            echo "Usuario eliminado";
        }else{
            Controller::sendNotFound("No se ha podido eliminar el usuario");
        }
    }

    //Permite editar un usuario existente
    public function update($id, $object){
        if(count($id)!=1){
            Controller::sendNotFound("Los usuarios se indetifican por un solo id");
            die();
        }

        //Iniciamos la sesión
        session_start();
        //Pilla el id de la sesión
        $idUsuario = $_SESSION['user_id'];

        //Comprobamos si la id proporcionada coincide con la de la sesión
        if($id[0]!=$idUsuario){
            Controller::sendNotFound("Error, el id proporcionado no coincide con el usuario de la sesión actual");
            die();  
        }

        $model = new UserModel();
        $datos = json_decode($object);

        //En base al json enviado realiza un cambio de contraseña o una edicion del perfil
        if(isset($datos->contrasena) && isset($datos->contrasena_actual)){

            $contrasena_actual=sha1($datos->contrasena_actual);
            $contrasena=sha1($datos->contrasena);

            if($model->changePassword($contrasena_actual,$contrasena,$id[0])){
                Controller::sendSuccess("Se ha cambiado la contraseña");
                $this->logout();
            }else{
                Controller::sendNotFound("Error, ambas contraseñas son iguales o la actual es incorrecta");
            }  

        } else if (isset($datos->email) && isset($datos->nombre)) {
            //Se crea un nuevo usuario con los datos proporcionados o vacios segun cada caso
            $user = new User($datos->nombre, $datos->email, "", null, $idUsuario ?? null, $datos->apellidos ?? "");
            //Si se cambió el email y ya esta registrado se envia un mensaje de error
            if($datos->email!=$_SESSION['user_email'] && $model->checkEmail($user)){
                Controller::sendNotFound("Error. Este correo ya esta registrado.");
                die();
            }

            //Si funcionó entonces
            if($model->update($user,$id[0])){
                Controller::sendSuccess("Se ha modificado el perfil");
                //Guardamos los nuevos datos en la sesion
                $_SESSION['user_name'] = $datos->nombre;
                $_SESSION['user_apellidos'] = $datos->apellidos;
                $_SESSION['user_email'] = $datos->email;
            }else{
                Controller::sendNotFound("Error, tiene que cambiar algun datos de su perfil antes de confirmar");
            }  
        } else {
            Controller::sendNotFound("Error, falta algun dato");
        }
    }

    //Hace login de un usuario segun el objeto recibido
    public function login($object){ 
        // Inicia la sesión
        session_start();
    
        // Crea el modelo de Usuario y decodifica el objeto
        $model = new UserModel();
        $datos = json_decode($object);
    
        // Obtenemos los datos, que serán email y contraseña
        $email = $datos->email;
        $password = $datos->contrasena;
    
        // Intentamos autenticar al usuario
        $user_authenticated = $model->login($email, $password);
    
        if ($user_authenticated) {
            // Se guardan los datos en la sesión
            $_SESSION['user_id'] = $user_authenticated->id;
            $_SESSION['user_name'] = $user_authenticated->nombre;
            $_SESSION['user_apellidos'] = $user_authenticated->apellidos;
            $_SESSION['user_email'] = $user_authenticated->email;
            $_SESSION['user_rol'] = $user_authenticated->rol_id;
    
            // Devolvemos que la sesión fue exitosa
            echo json_encode(["success" => true]);
        } else {
            // Si no se puede hacer login, enviamos error
            Controller::sendNotFound("Correo o contraseña incorrectos.");
        }
    }
    

    //Devuelve los datos de la sesión actual si está logueado
    public function sesion() {
        //Iniciamos la sesion
        session_start();
        //Si no hay sesion activa se devuelve un mensaje de error y se para la ejecucion
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return;
        }
        //Devuelve un json_encode de los datos
        echo json_encode([
            "user_id" => $_SESSION['user_id'],
            "user_name" => $_SESSION['user_name'],
            "user_apellidos" => $_SESSION['user_apellidos'],
            "user_email" => $_SESSION['user_email'],
            "user_rol" => $_SESSION['user_rol']
        ]);
    }

    //Cierra la sesion del usuario
    public function logout() {
        //Iniciamos la sesion
        session_start();
        //Vaciamos la sesion
        session_unset();
        //Destruimos la sesion
        session_destroy();
    }
}