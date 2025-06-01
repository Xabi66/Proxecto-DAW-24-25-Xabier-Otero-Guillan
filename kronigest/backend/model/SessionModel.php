<?php
include_once("Model.php");
include_once("ModelObject.php");

class Session extends ModelObject{
    public int $usuario_id;
    public string $token;

    public function __construct($usuario_id, $token) {
        $this->usuario_id = $usuario_id;
        $this->token = $token;
    }

    public static function fromJson($json):ModelObject {
        $data = json_decode($json);
        return new Session($data->usuario_id, $data->token);
    }
    
    public function toJson():String{
        return json_encode($this);
    }
}

class SessionModel extends Model{
    //Obtiene todos los horarios de la BD
    public function getAll()
    {
        $sql = "SELECT * FROM sesiones";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            foreach ($statement as $s) {
                $session = new Session($s['usuario_id'], $s['token']);
                $resultado[] = $session;
            }
        } catch (PDOException $th) {
            error_log("Error SessionModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    
    public function get($usuario_id):Session|null
    {
        $sql = "SELECT * FROM sesiones WHERE usuario_id=?";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $usuario_id, PDO::PARAM_INT);
            $statement->execute();
            if($s = $statement->fetch()){
                $resultado = new Session($s['usuario_id'], $s['token']);
            }  
        } catch (Throwable $th) {
            error_log("Error SessionModel->get($usuario_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function insert($usuario_id) {
        $sql = "INSERT INTO sesiones(usuario_id) 
                VALUES (:usuario_id)";
        $pdo = self::getConnection();
        $resultado = false;
    
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error SessionModel->insert(" . json_encode($usuario_id) . ")");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }
    
        return $resultado;
    }
    
    public function update($usuario_id, $token) {
        $sql = "UPDATE sesiones SET token=:token WHERE usuario_id = :usuario_id";
        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $statement->bindValue(":token", $token, PDO::PARAM_STR);

            $statement->execute();
            $resultado = $statement->rowCount() === 1;
        } catch (PDOException $th) {
            error_log("Error SessionModel->update(" . $usuario_id . ", $token)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function delete($usuario_id) {
        $sql = "DELETE FROM sesiones WHERE usuario_id = :usuario_id";
        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error SessionModel->delete($usuario_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

}
?>