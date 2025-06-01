<?php
include_once("Model.php");
include_once("ModelObject.php");

class User extends ModelObject{
    public ?int $id = null;
    public string $nombre;
    public ?string $apellidos = null;
    public string $email;
    public string $contrasena;
    public int $rol_id;
    //Constructor que codifica la contraseña y acepta como nulos el id y los apellidos
    public function __construct($nombre,$email,$contrasena,$rol_id,$id=null,$apellidos=null)
    {
        $this->nombre=$nombre;
        $this->email=$email;
        $this->contrasena= sha1($contrasena);
        $this->rol_id=$rol_id ?? 2;
        $this->id=$id ?? null;
        $this->apellidos=$apellidos ?? null;
    }

    public static function fromJson($json):ModelObject{
        $data = json_decode($json);
        return new User($data->nombre, $data->email, $data->contrasena, $data->rol_id, $data->id ?? null, $data->apellidos ?? null);
    }

    public function toJson():String{
        return json_encode($this);
    }
}

class UserModel extends Model{

    public function getAll()
    {
        $sql = "SELECT * FROM usuarios";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            $resultado = array();
            foreach($statement as $u){
                $user = new User($u['nombre'],$u['email'],$u['contrasena'], $u['rol'] ?? 2, $u['id'], $u['apellidos']);
                $resultado[] = $user;
            }
        } catch (PDOException $th) {
            error_log("Error UserModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function get($user_id):User|null
    {
        $sql = "SELECT * FROM usuarios WHERE id=?";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $user_id, PDO::PARAM_INT);
            $statement->execute();
            if($u = $statement->fetch()){
                $resultado = new User($u['nombre'],$u['email'],$u['contrasena'], $u['rol'], $u['id'], $u['apellidos']);
            }  
        } catch (Throwable $th) {
            error_log("Error UserModel->get($user_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    //Inserta un nuevo usuario en la BD. Fuerza a que su rol sea el numero 2
    public function insert($user)
    {
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, contrasena, rol) 
            VALUES (:nombre, :apellidos, :email, :contrasena, '2' )";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":nombre", $user->nombre, PDO::PARAM_STR);
            $statement->bindValue(":apellidos", $user->apellidos ?? "", PDO::PARAM_STR);
            $statement->bindValue(":email", $user->email, PDO::PARAM_STR);
            $statement->bindValue(":contrasena", $user->contrasena, PDO::PARAM_STR);

            if ($statement->execute()) {
                $resultado = $pdo->lastInsertId();
            }
        } catch (PDOException $th) {
            error_log("Error UserModel->insert(" . $user->toJson(). ")");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Edita la información del usuario en base a su ID.
    public function update($user, $user_id)
    {
 
        $sql = "UPDATE usuarios SET
            nombre = :nombre,
            apellidos = :apellidos,
            email = :email
            WHERE id = :user_id";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":nombre", $user->nombre, PDO::PARAM_STR);
            $statement->bindValue(":apellidos", $user->apellidos ?? "", PDO::PARAM_STR);
            $statement->bindValue(":email", $user->email, PDO::PARAM_STR);
            $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);

            $resultado = $statement->execute();
            $resultado = $statement->rowCount() == 1;
        } catch (PDOException $th) {
            error_log("Error UserModel->update(" . implode(",", $user) . ", $user_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Cambia la contraseña del usuario por una nueva siempre y cuando coincida la proporcionada con la actual
    public function changePassword($contrasena_actual, $contrasena_nueva , $user_id)
    {
 
        $sql = "UPDATE usuarios SET
            contrasena = :contrasena_nueva
            WHERE id = :user_id AND contrasena = :contrasena_actual";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":contrasena_actual", $contrasena_actual, PDO::PARAM_STR);
            $statement->bindValue(":contrasena_nueva", $contrasena_nueva, PDO::PARAM_STR);
            $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);

            $resultado = $statement->execute();
            $resultado = $statement->rowCount() == 1;
        } catch (PDOException $th) {
            error_log("Error UserModel->changePassword");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    //Borra un usuario de la BD segun su id
    public function delete($user_id)
    {
        $sql = "DELETE FROM usuarios WHERE id=:id";

        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error UserModel->delete($user_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    //Comprueba si un correo ya existe en la BD
    public function checkEmail($user){
        $sql = "SELECT count(*) FROM usuarios WHERE email=:email";
        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":email", $user->email, PDO::PARAM_STR);
            $statement->execute();
            //Recogemos el resultado del count y lo comparamos
            $resultado = $statement->fetchColumn() > 0;
        } catch (Throwable $th) {
            error_log("Error UserModel->checkEmail($user->email)");
            error_log($th->getMessage());
            $resultado=false;
        } finally {
            $statement = null;
            $pdo = null;
        }
        return $resultado;
    }    
    
    //Obtiene un usuario pasandole su email y contraseña
    public function login($email, $password){
        $sql = "SELECT * FROM usuarios WHERE email=:email AND contrasena=:contrasena LIMIT 1";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":email", $email, PDO::PARAM_STR);
            $statement->bindValue(":contrasena", sha1($password), PDO::PARAM_STR);
            $statement->execute();
            if($u = $statement->fetch()){
                $resultado = new User($u['nombre'],$u['email'],$u['contrasena'], $u['rol'], $u['id'], $u['apellidos']);
            }  
        } catch (Throwable $th) {
            error_log("Error UserModel->login($email)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    } 
}
?>