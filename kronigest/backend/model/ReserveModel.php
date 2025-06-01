<?php
include_once("Model.php");
include_once("ModelObject.php");

class Reserve extends ModelObject{
    public ?int $id = null;
    public int $id_usuario;
    public int $id_servicio;
    public string $fecha;
    public string $hora_inicio;
    public string $hora_fin;
    public ?string $informacion_adicional = "";
    public string $estado;

    //Constructor que acepta algunos parametros como nulos
    public function __construct($id_usuario, $id_servicio, $fecha, $hora_inicio, $hora_fin, $estado="pendiente", $informacion_adicional = "", $id = null) {
        $this->id_usuario = $id_usuario;
        $this->id_servicio = $id_servicio;
        $this->fecha = $fecha;
        $this->hora_inicio = $hora_inicio;
        $this->hora_fin = $hora_fin;
        $this->estado = $estado;
        $this->informacion_adicional = $informacion_adicional ?? "";
        $this->id = $id ?? null;
    }

    public static function fromJson($json): ModelObject {
        $data = json_decode($json);
        return new Reserve($data->id_usuario, $data->id_servicio, $data->fecha, $data->hora_inicio, $data->hora_fin, $data->estado ?? "pendiente" , $data->informacion_adicional ?? null, $data->id ?? null);
    }

    public function toJson():String{
        return json_encode($this);
    }
}

class ReserveModel extends Model{
    //Obtiene todas las citas 
    public function getAll()
    {
        $sql = "SELECT * FROM citas";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            $resultado = array();
            foreach($statement as $s){
                $reserve = new Reserve($s['id_usuario'],$s['id_servicio'],$s['fecha'],$s['hora_inicio'],$s['hora_fin'],$s['estado'], $s['informacion_adicional'], $s['id']);
                $resultado[] = $reserve;
            }
        } catch (PDOException $th) {
            error_log("Error ReserveModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Obtiene una cita concreta
    public function get($reserve_id):Reserve|null
    {
        $sql = "SELECT * FROM citas WHERE id=?";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $reserve_id, PDO::PARAM_INT);
            $statement->execute();
            if($s = $statement->fetch()){
                $resultado = new Reserve($s['id_usuario'],$s['id_servicio'],$s['fecha'],$s['hora_inicio'],$s['hora_fin'],$s['estado'], $s['informacion_adicional'], $s['id']);
            }  
        } catch (Throwable $th) {
            error_log("Error ReserveModel->get($reserve_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Inserta una cita en la BD
    public function insert($reserve)
    {
        $sql = "INSERT INTO citas (id_usuario, id_servicio, fecha, hora_inicio, hora_fin, informacion_adicional, estado)
                VALUES (:id_usuario, :id_servicio, :fecha, :hora_inicio, :hora_fin, :informacion_adicional, :estado)";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":id_usuario", $reserve->id_usuario, PDO::PARAM_INT);
            $statement->bindValue(":id_servicio", $reserve->id_servicio, PDO::PARAM_INT);
            $statement->bindValue(":fecha", $reserve->fecha, PDO::PARAM_STR);
            $statement->bindValue(":hora_inicio", $reserve->hora_inicio, PDO::PARAM_STR);
            $statement->bindValue(":hora_fin", $reserve->hora_fin, PDO::PARAM_STR);
            $statement->bindValue(":informacion_adicional", $reserve->informacion_adicional, PDO::PARAM_STR);
            $statement->bindValue(":estado", $reserve->estado, PDO::PARAM_STR);

            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ReserveModel->insert(" . $reserve->toJson(). ")");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Actualiza una cita concreta
    public function update($reserve, $reserve_id)
    {
 
        $sql = "UPDATE citas SET
            fecha = :fecha,
            hora_inicio = :hora_inicio,
            hora_fin = :hora_fin,
            informacion_adicional = :informacion_adicional
            WHERE id = :id";
        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":fecha", $reserve->fecha, PDO::PARAM_STR);
            $statement->bindValue(":hora_inicio", $reserve->hora_inicio, PDO::PARAM_STR);
            $statement->bindValue(":hora_fin", $reserve->hora_fin, PDO::PARAM_STR);
            $statement->bindValue(":informacion_adicional", $reserve->informacion_adicional, PDO::PARAM_STR);
            $statement->bindValue(":id", $reserve_id, PDO::PARAM_INT);

            $resultado = $statement->execute();
            $resultado = $statement->rowCount() == 1;
        } catch (PDOException $th) {
            error_log("Error ReserveModel->update(" . implode(",", $reserve) . ", $reserve_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Borra una cita concreta de la BD
    public function delete($reserve_id)
    {
        $sql = "DELETE FROM citas WHERE id=:id";

        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":id", $reserve_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ReserveModel->delete($reserve_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    //Obtiene todas las citas pendientes para un dia concreto, ignorando una cita en cocreto para manejar cuando se actualicen las citas.
    public function getByDate($date, $id="-1") {
        $sql = "SELECT * FROM citas WHERE fecha = ? AND id!=? AND estado='pendiente' ORDER BY hora_inicio";
        $pdo = self::getConnection();
        $resultados = [];
    
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $date, PDO::PARAM_STR);
            $statement->bindValue(2, $id, PDO::PARAM_INT);
            $statement->execute();
            foreach($statement as $s){
                $reserve = new Reserve($s['id_usuario'],$s['id_servicio'],$s['fecha'],$s['hora_inicio'],$s['hora_fin'],$s['estado'], $s['informacion_adicional'], $s['id']);
                $resultados[] = $reserve;
            }
        } catch (Throwable $th) {
            error_log("Error ReserveModel->getByDate($date)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }
    
        return $resultados;
    }  

    //Funcion que obtiene todas las citas pendientes de un usuario para x dia
    public function getByDateAndUserPending($date, $user_id) {
        $sql = "SELECT * FROM citas WHERE fecha = :fecha AND id_usuario=:id AND estado='pendiente' ORDER BY hora_inicio";
        $pdo = self::getConnection();
        $resultados = [];
    
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":fecha", $date, PDO::PARAM_STR);
            $statement->bindValue(":id", $user_id, PDO::PARAM_INT);
            $statement->execute();
            foreach($statement as $s){
                $reserve = new Reserve($s['id_usuario'],$s['id_servicio'],$s['fecha'],$s['hora_inicio'],$s['hora_fin'],$s['estado'], $s['informacion_adicional'], $s['id']);
                $resultados[] = $reserve;
            }
        } catch (Throwable $th) {
            error_log("Error ReserveModel->getByDateAndUser($date, $user_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }
    
        return $resultados;
    }

    //Obtiene todas las citas para el historial ordenadas por fecha y hora_inicio descendente.
    public function getAllForHistory()
    {
        $sql = "SELECT * FROM citas WHERE estado='finalizada' ORDER BY fecha DESC, hora_inicio DESC";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            $resultado = array();
            foreach($statement as $s){
                $reserve = new Reserve($s['id_usuario'],$s['id_servicio'],$s['fecha'],$s['hora_inicio'],$s['hora_fin'],$s['estado'], $s['informacion_adicional'], $s['id']);
                $resultado[] = $reserve;
            }
        } catch (PDOException $th) {
            error_log("Error ReserveModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    //Obtiene todas las citas para el historial de un usuario concreto ordenadas por fecha y hora_inicio descendente
    public function getByUserForHistory($user_id)
    {
        $sql = "SELECT * FROM citas WHERE id_usuario=? AND estado='finalizada' ORDER BY fecha DESC, hora_inicio DESC";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $user_id, PDO::PARAM_INT);
            $statement->execute();
            foreach($statement as $s){
                $reserve = new Reserve($s['id_usuario'],$s['id_servicio'],$s['fecha'],$s['hora_inicio'],$s['hora_fin'],$s['estado'], $s['informacion_adicional'], $s['id']);
                $resultado[] = $reserve;
            }
        } catch (PDOException $th) {
            error_log("Error ReserveModel->getByUser($user_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    } 
    /*Actualiza las citas que ya han acabado para que pasen de pendiente a finalizada. 
    Se le añade un intervalo de 2 hora por que la BD esta en UTC en vez de tener la hora de España*/
    public function updatePendingToFinished()
    {
        $sql = "UPDATE citas SET estado='finalizada' WHERE estado='pendiente' AND CONCAT(fecha, ' ', hora_fin) < NOW() + INTERVAL 2 HOUR;";
        $pdo = self::getConnection();

        try {
            $statement = $pdo->prepare($sql);
            $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ReserveModel->updatePendingToFinished()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }
    } 

}
?>