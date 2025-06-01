<?php
include_once("Model.php");
include_once("ModelObject.php");

class Schedule extends ModelObject{
    public ?int $id = null;
    public string $dia_semana;
    public string $hora_inicio;
    public string $hora_fin;
    public bool $activo= true;

    //Constructor que acepta algunos parametros como nulos
    public function __construct($dia_semana, $hora_inicio, $hora_fin, $activo = true, $id = null) {
        $this->dia_semana = $dia_semana;
        $this->hora_inicio = $hora_inicio;
        $this->hora_fin = $hora_fin;
        $this->activo = $activo;
        $this->id = $id;
    }

    public static function fromJson($json):ModelObject {
        $data = json_decode($json);
        return new Schedule($data->dia_semana, $data->hora_inicio, $data->hora_fin, $data->activo ?? true, $data->id ?? null);
    }
    
    public function toJson():String{
        return json_encode($this);
    }
}

class ScheduleModel extends Model{
    //Obtiene todos los horarios de la BD
    public function getAll()
    {
        $sql = "SELECT * FROM horarios";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            foreach ($statement as $s) {
                $schedule = new Schedule($s['dia_semana'], $s['hora_inicio'], $s['hora_fin'], $s['activo'] ?? true, $s['id'] ?? null);
                $resultado[] = $schedule;
            }
        } catch (PDOException $th) {
            error_log("Error ScheduleModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    
    public function get($schedule_id):Schedule|null
    {
        $sql = "SELECT * FROM horarios WHERE id=?";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $schedule_id, PDO::PARAM_INT);
            $statement->execute();
            if($s = $statement->fetch()){
                $resultado = new Schedule($s['dia_semana'], $s['hora_inicio'], $s['hora_fin'], $s['activo'] ?? true, $s['id'] ?? null);
            }  
        } catch (Throwable $th) {
            error_log("Error ScheduleModel->get($schedule_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function insert($schedule) {
        $sql = "INSERT INTO horarios(dia_semana, hora_inicio, hora_fin, activo) 
                VALUES (:dia, :inicio, :fin, :activo)";
        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":dia", $schedule->dia_semana, PDO::PARAM_STR);
            $statement->bindValue(":inicio", $schedule->hora_inicio, PDO::PARAM_STR);
            $statement->bindValue(":fin", $schedule->hora_fin, PDO::PARAM_STR);
            $statement->bindValue(":activo", $schedule->activo ? 1 : 0, PDO::PARAM_BOOL);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ScheduleModel->insert(" . $schedule->toJson() . ")");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function update($schedule, $schedule_id) {
        $sql = "UPDATE horarios SET dia_semana = :dia, hora_inicio = :inicio, hora_fin = :fin, activo = :activo WHERE id = :id";
        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":dia", $schedule->dia_semana, PDO::PARAM_STR);
            $statement->bindValue(":inicio", $schedule->hora_inicio, PDO::PARAM_STR);
            $statement->bindValue(":fin", $schedule->hora_fin, PDO::PARAM_STR);
            $statement->bindValue(":activo", $schedule->activo ? 1 : 0, PDO::PARAM_BOOL);
            $statement->bindValue(":id", $schedule_id, PDO::PARAM_INT);

            $statement->execute();
            $resultado = $statement->rowCount() === 1;
        } catch (PDOException $th) {
            error_log("Error ScheduleModel->update(" . $schedule->toJson() . ", $schedule_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function delete($schedule_id) {
        $sql = "DELETE FROM horarios WHERE id = :id";
        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":id", $schedule_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ScheduleModel->delete($schedule_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    // Obtiene los horarios para X dia
    public function getByDay($dia) {
        $sql = "SELECT * FROM horarios WHERE dia_semana = :dia AND activo = 1";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":dia", $dia, PDO::PARAM_STR);
            $statement->execute();
            foreach ($statement as $s) {
                $schedule = new Schedule($s['dia_semana'], $s['hora_inicio'], $s['hora_fin'], $s['activo'] ?? true, $s['id'] ?? null);
                $resultado[] = $schedule;
            }
        } catch (PDOException $th) {
            error_log("Error ScheduleModel->getByDay($dia)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

}
?>