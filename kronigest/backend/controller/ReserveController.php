<?php
include_once("Controller.php");
include_once(PATH_MODEL."ReserveModel.php");
include_once(PATH_MODEL."BlockdateModel.php");
include_once(PATH_MODEL."ScheduleModel.php");
include_once(PATH_MODEL."ServiceModel.php");
include_once(PATH_MODEL."UserModel.php");

class ReserveController extends Controller{

    public function get($id){
        $model = new ReserveModel();
        $userModel = new UserModel();
        $serviceModel = new ServiceModel();

        if(count($id)!=1){
            Controller::sendNotFound("Las citas se identifican por un solo id");
            die();
        }

        $reserve = $model->get($id[0]);
        
        if($reserve==null){
            Controller::sendNotFound("El id no se corresponde con ninguna cita");
            die();
        }

        //Iniciamos la sesión
        session_start();
        //Pilla el id de la sesión
        $idUsuario = $_SESSION['user_id'];
        $rol = $_SESSION['user_rol'];

        if($idUsuario!=$reserve->id_usuario && $rol!=1){
            Controller::sendNotFound("No esta autorizado para obtener esta cita");
            die();
        }
        //Pilla el usuario y el servicio al que pertenece esa cita y comprueba que los obtuvo
        $usuario = $userModel->get($reserve->id_usuario);
        $servicio = $serviceModel->get($reserve->id_servicio);

        if($usuario==null || $servicio==null){
            Controller::sendNotFound("No se encontro el servicio o usuario correspondiente");
            die();
        }

        // Añade propiedades nuevas al objeto reserva
        /** @var stdClass $reserve */
        $reserve->usuario = [
            'nombre' => $usuario->nombre,
            'apellidos' => $usuario->apellidos, 
        ];
        
        $reserve->servicio = [
            'nombre' => $servicio->nombre,
            'precio' => $servicio->precio,
            'imagen' => $servicio->imagen,
            'duracion_estimada' => $servicio->duracion_estimada
        ];

        echo json_encode($reserve,JSON_PRETTY_PRINT);
    }

    public function getAll(){
        $model = new ReserveModel();
        $reserve = $model->getAll();
        echo json_encode($reserve,JSON_PRETTY_PRINT);
    }

    //Permite crear una cita
    public function insert($object){
        $model = new ReserveModel();
        $serviceModel= new ServiceModel();
        $blockdateModel= new BlockdateModel();

        $data= json_decode($object);
        $reserve = Reserve::fromJson($object);
        
        //Iniciamos la sesión
        session_start();
        //Pilla el id de la sesión
        $idUsuario = $_SESSION['user_id'];
        //Comprobamos que id_usuario pertenece al usuario de la sesion actual
        if($data->id_usuario!=$idUsuario){
            Controller::sendNotFound("Error, el id proporcionado no coincide con el usuario de la sesión actual.");
            die();  
        }

        //Obtenemos la fecha y hora actual
        $today = new DateTime('now', new DateTimeZone('Europe/Madrid'));
        //Las formateamos a dia y hora
        $todayDate = $today->format('Y-m-d'); 
        $todayTime = $today->format('H:i');

        //Comprobamos que no se intenta coger un turno que ya comenzo
        if ($data->fecha == $todayDate && $data->hora_inicio <= $todayTime) {
            Controller::sendNotFound("Error, no se pudo pedir cita por que ese turno ya ha comenzado.");
            die();
        }

        //Obtiene la lista de reservas para ese dia y pasa la hora de inicio a minutos
        $reservas = $model->getByDate($data->fecha);
        $hora_inicio = explode(':', $data->hora_inicio);
        $inicioMinutos = intval($hora_inicio[0]) * 60 + intval($hora_inicio[1]);
        $duracion=$serviceModel->get($data->id_servicio)->duracion_estimada;

        if(!$duracion){
            Controller::sendNotFound("Error, no se encontro ningun servicio asociado a esta cita ");
            die();  
        }
        
        //Comprueba que ese dia se puede pedir cita
        if($blockdateModel->checkBlock($data->fecha)){
            Controller::sendNotFound("No hay citas disponibles para este dia");
            die();
        }

        //Comprueba que el turno esta dentro del horario permitido
        if (!$this->checkSchedule($data->fecha, $inicioMinutos, $duracion)) {
            Controller::sendNotFound("Error, este turno no está en el horario disponible ");
            die();
        }

        //Comprueba si ese turno ya se ocupó
        if ($this->taken($inicioMinutos, $duracion, $reservas)) {
            Controller::sendNotFound("Error, este turno ya se ocupó ");
            die();
        }
        
        //Segun si fue insertado envia un mensaje u otro
        if($model->insert($reserve)){
            Controller::sendSuccess("Cita Creada");
        }else{
            Controller::sendNotFound("Error. No se ha podido crear la cita");
        }            
    }

    public function update($id, $object){
        if(count($id)!=1){
            Controller::sendNotFound("Las citas se identifican por un solo id");
            die();
        }

        $model = new ReserveModel();
        $blockdateModel= new BlockdateModel();

        $data= json_decode($object);
        //Sanitiza los campos con htmlspecialchars.
        $data->informacion_adicional = htmlspecialchars($data->informacion_adicional ?? "", ENT_QUOTES, 'UTF-8');
        //Iniciamos la sesión
        session_start();
        //Pilla el id de la sesión
        $idUsuario = $_SESSION['user_id'];
        //Comprobamos que id_usuario pertenece al usuario de la sesion actual
        if($data->id_usuario!=$idUsuario){
            Controller::sendNotFound("Error, el id proporcionado no coincide con el usuario de la sesión actual.");
            die();  
        }
        //Recuperamos esa cita de la BD
        $cita=$model->get($id[0]);
        //Comprobamos que sea una cita pendiente
        if($cita->estado!='pendiente'){
            Controller::sendNotFound("Error, no se puede cambiar una cita una vez finalizada.");
            die();  
        }

        //Obtenemos la fecha y hora actual
        $today = new DateTime('now', new DateTimeZone('Europe/Madrid'));
        //Las formateamos a dia y hora
        $todayDate = $today->format('Y-m-d'); 
        $todayTime = $today->format('H:i');
        //Comprobamos que la cita no esta en curso actualmente
        if ($cita->fecha == $todayDate && $cita->hora_inicio <= $todayTime) {
            Controller::sendNotFound("Error, no se puede cambiar una cita una vez ha comenzado.");
            die();
        }
        //Comprobamos que no se intenta cambiar la cita por un turno que ya comenzo
        if ($data->fecha == $todayDate && $data->hora_inicio <= $todayTime) {
            Controller::sendNotFound("Error, no se puede cambiar la cita a un turno que ya ha comenzado.");
            die();
        }

        //Comprueba que ese dia se puede pedir cita
        if($blockdateModel->checkBlock($data->fecha)){
            Controller::sendNotFound("No hay citas disponibles para este dia");
            die();
        }

        //Obtiene la lista de reservas para ese dia y pasa la hora de inicio a minutos
        $reservas = $model->getByDate($data->fecha, $id[0]);
        $hora_inicio = explode(':', $data->hora_inicio);
        $inicioMinutos = intval($hora_inicio[0]) * 60 + intval($hora_inicio[1]);

        //Comprueba que el turno esta dentro del horario permitido
        if (!$this->checkSchedule($data->fecha, $inicioMinutos, $data->duracion_estimada)) {
            Controller::sendNotFound("Error, este turno no está en el horario disponible ");
            die();
        }

        //Comprueba si ese turno ya se ocupó
        if ($this->taken($inicioMinutos, $data->duracion_estimada, $reservas)) {
            Controller::sendNotFound("Error, este turno ya se ocupó");
            die();
        }

        if($model->update($data,$id[0])){
            Controller::sendSuccess("Se ha modificado la cita");
        }else{
            Controller::sendNotFound("Error, tiene que cambiar algun dato de la cita antes de confirmar");
        }  
    }

    //Funcion que comprueba al momento de crear o editar una cita que esta se encuentre dentro del horario disponible
    function checkSchedule($fecha, $inicio, $duracion) {
        $scheduleModel= new ScheduleModel();
        /*Llamamos a un formateador de fechas e indicamos que el dia de la semana sea en español, con toda la informacion de la fecha, 
        sin informacion de la hora, la zona horaria, el tipo de calendario y el pattern de la fecha*/  
        $formateadorFecha = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'eeee');
        //Devuelve a que dia de la semana corresponde esa fecha
        $dia_semana = strtolower($formateadorFecha->format(new DateTime($fecha)));
        //Se obtiene el array de horarios para ese dia
        $horarios = $scheduleModel->getByDay($dia_semana);

        if (!$horarios) {
            Controller::sendNotFound("No hay citas disponibles para este dia");
            return false;
        }

        //Recorre cada tanda del horario
        foreach ($horarios as $horario) {
            //Divide en horas y minutos el inicio y fin de la tanda actual
            list($hora_i, $minuto_i) = explode(':', $horario->hora_inicio);
            list($hora_f, $minuto_f) = explode(':', $horario->hora_fin);
            //Calcula en minutos el inicio y fin de esa tanda
            $inicio_horario= intval($hora_i) * 60 + intval($minuto_i);
            $fin_horario= intval($hora_f) * 60 + intval($minuto_f);
            //Comprueba si la cita esta dentro de ese horario
            if ($inicio >= $inicio_horario && ($inicio+$duracion) <= $fin_horario) {
                return true;
            }
        }
    
        return false;
    }
    
    public function delete($id) {
        //Si hay mas de un id dará error
        if(count($id)!=1){
            Controller::sendNotFound("Las citas se identifican por un solo id");
            die();
        }

        $model = new ReserveModel();

        $reserve = $model->get($id[0]);
        
        if($reserve==null){
            Controller::sendNotFound("El id no se corresponde con ninguna cita");
            die();
        }
        
        //Iniciamos la sesión
        session_start();
        //Pilla el id de la sesión
        $idUsuario = $_SESSION['user_id'];
        $rol = $_SESSION['user_rol'];

        if($idUsuario!=$reserve->id_usuario && $rol!=1){
            Controller::sendNotFound("No tiene permiso para borrar esta cita");
            die();
        }
 
        if($model->delete($id[0])){
            echo "Cita eliminada";
        }else{
            Controller::sendNotFound("No se ha podido eliminar la cita");
        }
    }

    public function shifts($object) {
        // Crea los modelos y decodifica el objeto
        $blockdateModel = new BlockdateModel();
        $scheduleModel = new ScheduleModel();
        $reserveModel = new ReserveModel();
        // Pilla los datos que se le pasan
        $datos = json_decode($object);
    
        // Obtenemos los datos
        $fecha = $datos->reserve_fecha;
        $service_id = $datos->service_id;
        $duracion = $datos->service_duracion;
        $id_cita= $datos->id_cita;
        //Comprueba si se pasaron todos los datos excepto id_cita que es opcional
        if(!$fecha || !$service_id || !$duracion){
            Controller::sendNotFound("Falta algun dato");
            die();
        }
        //Comprueba si esa fecha esta bloqueada
        if($blockdateModel->checkBlock($fecha)){
            Controller::sendNotFound("No hay citas disponibles para este dia");
            die();
        }
 
        /*LLamamos a un formateador de fechas e indicamos que el dia de la semana sea en español, con toda la informacion de la fecha, 
        sin informacion de la hora, la zona horaria, el tipo de calendario y el pattern de la fecha*/
        $formateadorFecha = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'eeee');
        
        //Devuelve a que dia de la semana corresponde esa fecha
        $dia_semana = strtolower($formateadorFecha->format(new DateTime($fecha)));

        //Se obtiene el array de horarios para ese dia
        $horarios = $scheduleModel->getByDay($dia_semana);

        //Si no hay horario para ese día
        if (!$horarios) {
            Controller::sendNotFound("No hay citas disponibles para este dia");
            return;
        }
        //En base a si se paso un id de cita o no pilla las reservas de ese dia ignorando o no la que tenga ese id
        if ($id_cita){
            $reservas = $reserveModel->getByDate($fecha, $id_cita);
        } else {
            $reservas = $reserveModel->getByDate($fecha);
        }

        //Obtenemos el array de turnos para ese dia de la semana
        $turnos = $this->calculateShifts($horarios, $duracion, $fecha, $reservas);
        //Devolvemos un json con los turnos
        echo json_encode([
            "turnos" => $turnos
        ]);

    }
    //Función que calcula los turnos disponibles comparando los horarios con la las reservas y la fecha y hora actual
    function calculateShifts($horarios, $duracion, $fecha, $reservas){
        //Almacenara los turnos en los que pedir cita
        $turnos=[];
        //Obtenemos la fecha y hora actual
        $today = new DateTime('now', new DateTimeZone('Europe/Madrid'));
        //Las formateamos a dia y hora
        $todayDate = $today->format('Y-m-d'); 
        $todayTime = $today->format('H:i');

        //Recorre cada tanda del horario
        foreach ($horarios as $horario) {
            //Divide en horas y minutos el inicio y fin de la tanda actual
            list($hora_i, $minuto_i) = explode(':', $horario->hora_inicio);
            list($hora_f, $minuto_f) = explode(':', $horario->hora_fin);
            //Calcula en minutos el inicio y fin de esa tanda
            $inicio= intval($hora_i) * 60 + intval($minuto_i);
            $fin= intval($hora_f) * 60 + intval($minuto_f);

            //Calcula el ultimo turno disponible en base a lo que dura ese servicio
            $ultimo_turno = $fin - $duracion;

            //Va creando turnos de 10 en 10 minutos
            for ($i = $inicio; $i <= $ultimo_turno; $i += 10) {
                //Calcula la hora y min de ese turno
                $hora = floor($i / 60);
                $min = $i % 60;
                //Formatea la hora como HH:MM
                $turno = sprintf("%02d:%02d", $hora, $min);
                //Comprueba si el turno esta ocupado
                $ocupado = $this->taken($i, $duracion, $reservas);
                //Guarda en turnos ese valor si no esta ocupado. Si la fecha es la de hoy, comprueba que aun no haya pasado esa hora.
                if ($ocupado==false && !($fecha == $todayDate && $turno <= $todayTime)) {
                    $turnos[] = [
                        "turno" => $turno
                    ];
                }
            }
        }
        return $turnos;
    }

    //Funcion que comprueba si un turno esta ocupado o no
    function taken($inicio, $duracion, $reservas){
        foreach ($reservas as $reserva) {
            // Pilla la hora y minutos a la que empieza y acaba esa reserva
            list($hora_reserva, $minuto_reserva) = explode(':', $reserva->hora_inicio);
            list($hora_reserva_fin, $minuto_reserva_fin) = explode(':', $reserva->hora_fin);
            //Calcula en minutos el inicio y fin de la reserva
            $inicio_reserva = intval($hora_reserva) * 60 + intval($minuto_reserva);
            $fin_reserva = intval($hora_reserva_fin) * 60 + intval($minuto_reserva_fin);
    
            //Si el turno empieza antes de que acabe la reserva y acaba despues de que empiece la reserva, esta ocupado
            if ($inicio < $fin_reserva && ($inicio + $duracion) > $inicio_reserva) {
                return true;
            }
        }
        return false;
    }

    //Obtiene el historial de citas de todos los usuarios si lo solicita un admin o solo del propio usuario
    public function history(){
        $model = new ReserveModel();
        $userModel = new UserModel();
        $serviceModel = new ServiceModel();
        //Antes de nada actualiza el estado de las citas que ya han finalizado
        $model->updatePendingToFinished();

        session_start();
        //Pilla el id y rol de la sesión
        $idUsuario = $_SESSION['user_id'];
        $rol = $_SESSION['user_rol'];
        
        if (!$idUsuario || !$rol) {
            Controller::sendNotFound("No hay ninguna sesion activa");
            die();
        }
        // Si es admin obtiene todas las citas, si no solo las de ese usuario
        if($rol == 1){
            $reservas = $model->getAllForHistory();
        } else {
            $reservas = $model->getByUserForHistory($idUsuario);
        }

        //Se añaden algunos datos del usuario y el servicio a cada reserva
        /** @var stdClass*/
        foreach ($reservas as $reserva) {
            $usuario = $userModel->get($reserva->id_usuario);
            $servicio = $serviceModel->get($reserva->id_servicio);
        
            // Añade propiedades nuevas al objeto reserva
            $reserva->usuario = [
                'nombre' => $usuario->nombre,
                'apellidos' => $usuario->apellidos, 
            ];
        
            $reserva->servicio = [
                'imagen' => $servicio->imagen,
                'nombre' => $servicio->nombre,
                'precio' => $servicio->precio
            ];
        }
        
        echo json_encode($reservas, JSON_PRETTY_PRINT);
    }
    
    //Obtiene las citas pendientes para ese dia
    public function pending($object){
        $model = new ReserveModel();
        $userModel = new UserModel();
        $serviceModel = new ServiceModel();

        $datos = json_decode($object);
        //Antes de nada actualiza el estado de las citas que ya han finalizado
        $model->updatePendingToFinished();

        session_start();
        //Pilla el id y rol de la sesión
        $idUsuario = $_SESSION['user_id'];
        $rol = $_SESSION['user_rol'];
        
        if (!$idUsuario || !$rol) {
            Controller::sendNotFound("No hay ninguna sesion activa");
            die();
        }
        // Si es admin obtiene todas las citas, si no solo las de ese usuario
        if($rol == 1){
            $reservas = $model->getByDate($datos->fecha);
        } else {
            $reservas = $model->getByDateAndUserPending($datos->fecha,$idUsuario);
        }

        //Se añaden algunos datos del usuario y el servicio a cada reserva
        /** @var stdClass*/
        foreach ($reservas as $reserva) {
            $usuario = $userModel->get($reserva->id_usuario);
            $servicio = $serviceModel->get($reserva->id_servicio);
        
            // Añade propiedades nuevas al objeto reserva
            $reserva->usuario = [
                'nombre' => $usuario->nombre,
                'apellidos' => $usuario->apellidos, 
            ];
        
            $reserva->servicio = [
                'imagen' => $servicio->imagen,
                'nombre' => $servicio->nombre,
                'precio' => $servicio->precio
            ];
        }
        
        echo json_encode($reservas, JSON_PRETTY_PRINT);
    }
}
