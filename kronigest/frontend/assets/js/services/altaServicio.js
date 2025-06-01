import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $formulario_servicio=$d.querySelector("#formulario_servicio"),
    $mensaje_error=$d.querySelector("#mensaje_error")

//Enlace a la API
const urlServices = "http://localhost/backend/route.php/service"
const urlUser = "http://localhost/backend/route.php/user"

//Pasamos la cookie de sesion para saber si el usuario esta logueado o no y asi devolver su rol
async function comprobarSesion() {
    const resp = await fetch(`${urlUser}/sesion`, {
        method: "POST",
        credentials: "include"
    });

    if (resp.ok) {
        //Recibe los datos del usuario 
        const data = await resp.json();
        //Devuelve los datos
        return data.user_rol;
    } else {
        //Redirije a servicios si el usuario no esta autenticado
        window.location.href = "/servicios/index.html";
    }
}

//Funcion asincrona que manda los datos al servidor para crear el usuario
async function registrarServicio(datos) {
    //Se realiza una consulta de tipo post a la api pasandole los datos
    try{
        //Comprueba si el usuario sigue conectado
        const rol=await comprobarSesion();

        if(rol==1){
            const resp= await fetch(`${urlServices}`,{
                method:"POST",
                headers:{"Content-type":"application/json; utf-8"},
                body:JSON.stringify(datos)
            })
            //Se espera a que la api responda con un mensaje, ya sea exitoso o no
            const mensaje= await resp.json()
    
            //En base a la respuesta renderiza un mensaje u otro. Puede renderizar el mensaje de error propio de la api o por defecto uno genérico
            if (resp.ok) {
                $mensaje_error.textContent = mensaje.success || "Servicio creado correctamente.";
                window.location.href = "/servicios/index.html";
            } else {
                $mensaje_error.textContent = mensaje.error || "Error. No se pudo crear el servicio.";
            }
        } else {
            $mensaje_error.textContent = "Solo pueden crear servicios los administradores";
        }
    } catch (error) {
        $mensaje_error.textContent = "Error de conexión con el servidor.";
    }
}

//Inicializa el codigo
async function iniciar() {
    try {
        const rol=await comprobarSesion();
        //Cargamos el header y footer
        loadHeaderFooter;
        //Si el usuario no es administrador le redirige
        if (rol!=1){
            window.location.href = "/servicios/index.html";
        }
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/servicios/index.html";
    }
}

$d.addEventListener("DOMContentLoaded", ev => {
    ev.preventDefault()

    iniciar();

    //Al hacer submit
    $formulario_servicio.addEventListener('submit',async ev=>{
        ev.preventDefault();

        //Pillamos los datos del formulario y eliminamos espacios innecesarios
        const nombre=$formulario_servicio.nombre.value.trim()
        const duracion_estimada = $formulario_servicio.duracion.value.trim();
        const precio = $formulario_servicio.precio.value.trim();
        const informacion = $formulario_servicio.descripcion.value.trim() || null;
        const precio_reserva = $formulario_servicio.precio_reserva.value.trim() || null;

        //Creamos el objeto.
        const datos = {
            nombre,
            precio,
            duracion_estimada,
            informacion,
            precio_reserva
        }

        //Se hace una llamada a la funcion
        await registrarServicio(datos);

    })
})
