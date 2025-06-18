<?php
include_once("Model.php");
include_once("ModelObject.php");

class Service extends ModelObject{
    public ?int $id = null;
    public string $nombre;
    public float $precio;
    public int $duracion_estimada;
    public ?string $informacion = "";
    public ?float $precio_reserva= 0.0;
    public ?string $imagen = "services.jpg";

    //Constructor que acepta algunos parametros como nulos
    public function __construct($nombre, $precio, $duracion_estimada, $informacion = "", $precio_reserva = null, $imagen = null, $id = null) {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->duracion_estimada = $duracion_estimada;
        $this->informacion = $informacion ?? "";
        $this->precio_reserva = $precio_reserva ?? 0.0;
        $this->imagen = $imagen ?? "services.jpg";
        $this->id = $id ?? null;
    }

    public static function fromJson($json):ModelObject{
        $data = json_decode($json);
        //Sanitiza los campos con htmlspecialchars.
        $data->nombre = htmlspecialchars($data->nombre, ENT_QUOTES, 'UTF-8');
        $data->informacion = htmlspecialchars($data->informacion ?? "", ENT_QUOTES, 'UTF-8');
        $data->imagen = htmlspecialchars($data->imagen ?? "services.jpg", ENT_QUOTES, 'UTF-8');
        
        return new Service($data->nombre, $data->precio, $data->duracion_estimada, $data->informacion ?? "", $data->precio_reserva ?? 0.0, $data->imagen ?? "services.jpg", $data->id ?? null);
    }

    public function toJson():String{
        return json_encode($this);
    }
}

class ServiceModel extends Model{
    //Obtiene todos los servicios de la BD
    public function getAll()
    {
        $sql = "SELECT * FROM servicios";
        $pdo = self::getConnection();
        $resultado = [];

        try {
            $statement = $pdo->query($sql);
            $resultado = array();
            foreach($statement as $s){
                $service = new Service($s['nombre'],$s['precio'],$s['duracion_estimada'],$s['informacion'] ?? "",$s['precio_reserva'] ?? 0.0,$s['imagen'] ?? null, $s['id']);
                $resultado[] = $service;
            }
        } catch (PDOException $th) {
            error_log("Error ServiceModel->getAll()");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Obtiene un servicio concreto en base a su id
    public function get($service_id):Service|null
    {
        $sql = "SELECT * FROM servicios WHERE id=?";
        $pdo = self::getConnection();
        $resultado = null;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(1, $service_id, PDO::PARAM_INT);
            $statement->execute();
            if($s = $statement->fetch()){
                $resultado = new Service($s['nombre'],$s['precio'],$s['duracion_estimada'],$s['informacion'] ?? "",$s['precio_reserva'] ?? 0.0,$s['imagen'] ?? null, $s['id']);
            }  
        } catch (Throwable $th) {
            error_log("Error ServiceModel->get($service_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Inserta un servicio empleando los datos proporcionados
    public function insert($service)
    {
        $sql = "INSERT INTO `servicios`(`nombre`, `precio`, `duracion_estimada`, `informacion`, `precio_reserva`, `imagen`) 
            VALUES (:nombre, :precio, :duracion_estimada, :informacion, :precio_reserva, :imagen )";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);

            $statement->bindValue(":nombre", $service->nombre, PDO::PARAM_STR);
            $statement->bindValue(":precio", $service->precio, PDO::PARAM_STR);
            $statement->bindValue(":duracion_estimada", $service->duracion_estimada, PDO::PARAM_INT);
            $statement->bindValue(":informacion", $service->informacion ?? "", PDO::PARAM_STR);
            $statement->bindValue(":precio_reserva", $service->precio_reserva ?? 0.0, PDO::PARAM_STR);
            $statement->bindValue(":imagen", $service->imagen ?? "services.jpg", PDO::PARAM_STR);

            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ServiceModel->insert(" . $service->toJson(). ")");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Actualiza la información de x servicio
    public function update($service, $service_id)
    {
 
        $sql = "UPDATE servicios SET
            nombre = :nombre,
            precio = :precio,
            duracion_estimada = :duracion_estimada,
            informacion = :informacion,
            precio_reserva = :precio_reserva
            WHERE id = :service_id";

        $pdo = self::getConnection();
        $resultado = false;
        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":nombre", $service->nombre, PDO::PARAM_STR);
            $statement->bindValue(":precio", $service->precio, PDO::PARAM_STR);
            $statement->bindValue(":duracion_estimada", $service->duracion_estimada, PDO::PARAM_INT);
            $statement->bindValue(":informacion", $service->informacion ?? "", PDO::PARAM_STR);
            $statement->bindValue(":precio_reserva", $service->precio_reserva ?? 0.0, PDO::PARAM_STR);
            
            $statement->bindValue(":service_id", $service_id, PDO::PARAM_INT);

            $resultado = $statement->execute();
            $resultado = $statement->rowCount() == 1;
        } catch (PDOException $th) {
            error_log("Error ServiceModel->update(" . implode(",", $service) . ", $service_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
    //Borra el servicio asociado a una id proporcionada
    public function delete($service_id)
    {
        $sql = "DELETE FROM servicios WHERE id=:id";

        $pdo = self::getConnection();
        $resultado = false;

        try {
            $statement = $pdo->prepare($sql);
            $statement->bindValue(":id", $service_id, PDO::PARAM_INT);
            $resultado = $statement->execute();
        } catch (PDOException $th) {
            error_log("Error ServiceModel->delete($service_id)");
            error_log($th->getMessage());
        } finally {
            $statement = null;
            $pdo = null;
        }

        return $resultado;
    }
}
?>