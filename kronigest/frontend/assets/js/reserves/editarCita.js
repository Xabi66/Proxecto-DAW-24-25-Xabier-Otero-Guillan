import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $inputFecha=$d.querySelector("#fecha"),
    $selectHoras=$d.querySelector("#hora"),
    $informacion=$d.querySelector("#descripcion"),
    $botonReservar=$d.querySelector("#reservar"),
    $mensaje_error=$d.querySelector("#mensaje_error"),
    $section_botonera=$d.querySelector(".section_botonera")

//Enlaces a la API
const urlUser = "http://localhost/backend/route.php/user"
const urlReserve = "http://localhost/backend/route.php/reserve"
//Pilla la url y sus parametros
const urlInfo = new URLSearchParams(document.location.search)
//Almacena los turnos
const turnos=[];

//Pasamos la cookie de sesion para saber si el usuario esta logueado o no
async function comprobarSesion() {
    const resp = await fetch(`${urlUser}/sesion`, {
        method: "POST",
        credentials: "include"
    });

    if (resp.ok) {
        //Recibe los datos del usuario 
        const data = await resp.json();
        //Devuelve su id
        return data.user_id;
    } else {
        //Redirije a inicio de sesion si el usuario no esta autenticado
        window.location.href = "/inicioSesion.html";
    }
}

//Funcion para obtener turnos
async function getShifts(datos) {
    try {
        const resp = await fetch(`${urlReserve}/shifts`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos),
            credentials: "include" // Para que deje manejar la cookie de sesión
        });

        const resultado= await resp.json();
        
        if (resp.ok) {
            turnos.splice(0,turnos.length,...resultado.turnos);
        } else {
            turnos.splice(0, turnos.length);
        }
    } catch (error) {
        turnos.splice(0, turnos.length);
    }

    $botonReservar.disabled=turnos.length==0 ? true : false;
    renderSelectHoras();
}

//Funcion asincrona que manda los datos al servidor para editar la cita
async function editarCita(datos) {
    //Se realiza una consulta de tipo post a la api pasandole los datos
    try{
        const resp= await fetch(`${urlReserve}/${getParamURL()}}`,{
            method:"PUT",
            headers:{"Content-type":"application/json; utf-8"},
            body:JSON.stringify(datos)
        })
        //Se espera a que la api responda con un mensaje, ya sea exitoso o no
        const mensaje= await resp.json()

        //En base a la respuesta renderiza un mensaje u otro. Puede renderizar el mensaje de error propio de la api o por defecto uno genérico
        if (resp.ok) {
            $mensaje_error.textContent = mensaje.success || "Cita editada correctamente.";
            window.location.href = "/citas/index.html";
        } else {
            $mensaje_error.textContent = mensaje.error || "Error. No se pudo editar la cita.";
            renderizarDatos()
        }
    } catch (error) {
        $mensaje_error.textContent = "Error de conexión con el servidor.";
    }
}


//Renderiza los turnos en el select
function renderSelectHoras(){
    if(turnos.length==0){
        $selectHoras.innerHTML='<option value="-1" selected>No hay ningun turno disponible</option>'
    } else {
        $selectHoras.innerHTML='<option value="-1" selected>Escoja un horario...</option>'+turnos.map(el=>`
            <option value=${el.turno}>${el.turno}</option>
        `).join('')
    }
    
}

//Pilla el id de la url
function getParamURL(){
    //Pilla el id de la url
    let id=urlInfo.get("cita");
    //Si hay id comprueba a que cita corresponde. Si no hay id redirige.
    if(id){
        return id;
    } else {
        window.location.href = "/citas/index.html";
    }
}

//Pilla los datos para renderizar los turnos disponibles
async function renderizarDatos(){
    const reserve_fecha=$inputFecha.value
    const id_cita=getParamURL()

    const cita = await getCita(id_cita)

    const service_id=cita.id_servicio;
    const service_duracion= cita.servicio.duracion_estimada;

    const datos = {
        reserve_fecha,
        service_id,
        service_duracion,
        id_cita
    }

    await getShifts(datos)
}

//Funcion que obtiene una cita concreta de la BD
async function getCita(id) {
    try {
        const resp=await fetch(`${urlReserve}/${id}`, {
            method: "GET"
        });

        const resultado= await resp.json();
        //Devuelve la cita si la respuesta es correcta y la cita esta pendiente
        if(resp.ok && resultado.estado=="pendiente"){
            return resultado;
        } else {
            window.location.href = "/citas/index.html";
        }
    } catch (error) {
        window.location.href = "/citas/index.html";
    }
}


async function iniciar(){
    //Cargamos el header y footer
    loadHeaderFooter();
    //Comprobamos si hay una sesion iniciada o no
    await comprobarSesion()

    //Pilla el id de la url
    let id=getParamURL();

    //Comprueba si esa id corresponde a alguna cita
    const citaActual = await getCita(id)

    //Le da al input de fechas como minimo la fecha de hoy
    $inputFecha.min = new Date().toLocaleDateString('sv-SE');

    //Devuelve los datos de esa cita a los inputs correspondientes
    $informacion.value=citaActual.informacion_adicional
    $inputFecha.value=citaActual.fecha
    //Renderiza los horarios de ese dia
    await renderizarDatos()
    //Selecciona el horario de esa cita
    $selectHoras.value = citaActual.hora_inicio.slice(0, 5);
    //Crea el boton de volver atras dandole el id de la cita correspondiente
    $section_botonera.innerHTML=`<button type="button" onclick="window.location.href = './cita.html?cita=${id}'">Volver atrás</button>`
}
    
$d.addEventListener("DOMContentLoaded", ev => {
    ev.preventDefault()
    //Inicializa el codigo
    iniciar();
    
    //Al cambiar de fecha renderiza los datos para esa fecha
    $inputFecha.addEventListener("change",async ev=>{
        ev.preventDefault()

        renderizarDatos()
    })

    //Al pulsar el boton de reservar obtiene los datos
    $botonReservar.addEventListener("click", async ev=>{
        ev.preventDefault()

        const id_usuario = await comprobarSesion();
        const fecha = $inputFecha.value;
        const hora_inicio= $selectHoras.value;
        const informacion_adicional = $informacion.value || "";

        if (!fecha) {
            $mensaje_error.textContent="Debe seleccionar una fecha";
            return;
        }

        if (!hora_inicio || hora_inicio === "-1") {
            $mensaje_error.textContent="Debe seleccionar una hora valida";
            return;
        }

        //Vuelve a obtener la cita actual
        const citaActual = await getCita(getParamURL())
        const duracion_estimada=citaActual.servicio.duracion_estimada;

        //Divide la hora en horas y minutos y los pasa a numeros
        const [h_inicio, m_inicio] = hora_inicio.split(':').map(Number);
        //Calcula en minutos el inicio y suma la duracion del servicio
        const fin= h_inicio * 60 + m_inicio + duracion_estimada;
        //Obtiene la hora a la que acaba como un string con 0 a la izquierda
        const hora_fin = `${String(Math.floor(fin / 60)).padStart(2, '0')}:${String(fin % 60).padStart(2, '0')}`;

        //Creamos el objeto.
        const datos = {
            id_usuario,
            duracion_estimada,
            fecha,
            hora_inicio,
            hora_fin,
            informacion_adicional
        }

        await editarCita(datos);
    })
})
    