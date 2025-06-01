<?php
include_once("Model.php");
include_once("ModelObject.php");

class Blockdate extends ModelObject{
    public ?int $id = null;
    public string $fecha;
    public ?bool $repetir = false;

    //Constructor que acepta algunos parametros como nulos
    public function __construct($fecha, $repetir = false, $id = null) {
        $this->fecha = $fecha;
        $this->repetir = $repetir ?? false;
        $this->id = $id;
    }

    public static function fromJson($json):ModelObject {
        $data = json_decode($json);
        return new Blockdate($data->fecha, $data->repetir ?? false, $data->id ?? null);
    }
    
    public function toJson():String{
        return json_encode($this);
    }
}

class BlockdateModel extends Model{

    public function getAll()
    {
        $sql = "SELECT * FROM fechas_bloqueadas";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            $resultado = array();
            foreach($statement as $s){
                $blockdate = new Blockdate($s['fecha'],$s['repetir'] ?? false, $s['id'] ?? null);
                $resultado[] = $blockdate;
            }
        } catch (PDOException $th) {
            error_log("Error BlockdateModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function get($blockdate_id):Blockdate|null
    {
        $sql = "SELECT * FROM fechas_bloqueadas WHERE id=?";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $blockdate_id, PDO::PARAM_INT);
            $statement->execute();
            if($s = $statement->fetch()){
                $resultado = new Blockdate($s['fecha'],$s['repetir'] ?? false, $s['id'] ?? null);
            }  
        } catch (Throwable $th) {
            error_log("Error BlockdateModel->get($blockdate_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function insert($blockdate)
    {
        $sql = "INSERT INTO fechas_bloqueadas (fecha, repetir) 
        VALUES (:fecha, :repetir)";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":fecha", $blockdate->fecha, PDO::PARAM_STR);
            $statement->bindValue(":repetir", $blockdate->repetir ? 1 : 0, PDO::PARAM_BOOL);

            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error BlockdateModel->insert(" . $blockdate->toJson(). ")");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function update($blockdate, $blockdate_id)
    {
 
        $sql = "UPDATE fechas_bloqueadas SET fecha = :fecha, repetir = :repetir WHERE id = :id";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":fecha", $blockdate->fecha, PDO::PARAM_STR);
            $statement->bindValue(":repetir", $blockdate->repetir ? 1 : 0, PDO::PARAM_BOOL);
            $statement->bindValue(":id", $blockdate_id, PDO::PARAM_INT);

            $resultado = $statement->execute();
            $resultado = $statement->rowCount() == 1;
        } catch (PDOException $th) {
            error_log("Error BlockdateModel->update(" . implode(",", $blockdate) . ", $blockdate_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    public function delete($blockdate_id)
    {
        $sql = "DELETE FROM fechas_bloqueadas WHERE id=:id";

        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":id", $blockdate_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error BlockdateModel->delete($blockdate_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

    //Comprueba si una fecha esta bloqueada en la BD
    public function checkBlock($fecha)
    {
        //Si repetir es true entonces comprueba solamente el dia y mes
        $sql = "SELECT COUNT(*) FROM fechas_bloqueadas 
            WHERE fecha = :fecha OR (repetir = 1 AND DAY(fecha) = DAY(:fecha) AND MONTH(fecha) = MONTH(:fecha))";

        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":fecha", $fecha, PDO::PARAM_STR);
            $statement->execute();

            $count = $statement->fetchColumn();
            $resultado = $count > 0;
        } catch (PDOException $th) {
            error_log("Error BlockdateModel->checkBlock($fecha)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }

}
?>