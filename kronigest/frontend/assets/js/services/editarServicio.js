import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $formulario_servicio=$d.querySelector("#formulario_servicio"),
    $mensaje_error=$d.querySelector("#mensaje_error"),
    $section_botonera=$d.querySelector(".section_botonera")

//Enlace a la API
const urlServices = "http://localhost/backend/route.php/service"
const urlUser = "http://localhost/backend/route.php/user"
//Pilla la url y sus parametros
const urlInfo = new URLSearchParams(document.location.search)

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

//Funcion asincrona que manda los datos al servidor para editar el servicio
async function editarServicio(datos) {
    try{
        //Comprueba si el usuario sigue conectado
        const rol=await comprobarSesion();

        if(rol==1){
            const resp= await fetch(`${urlServices}/${urlInfo.get("servicio")}`,{
                method: "PUT",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(datos),
                credentials: "include" //Para que deje manejar la cookie de sesion
            })
            //Se espera a que la api responda con un mensaje, ya sea exitoso o no
            const mensaje= await resp.json()
    
            //En base a la respuesta renderiza un mensaje u otro. Puede renderizar el mensaje de error propio de la api o por defecto uno genérico
            if (resp.ok) {
                $mensaje_error.textContent = mensaje.success || "Servicio editado correctamente.";
                window.location.href = `/servicios/servicio.html?servicio=${urlInfo.get("servicio")}`;
            } else {
                $mensaje_error.textContent = mensaje.error || "Error. No se pudo editar el servicio.";
            }
        } else {
            $mensaje_error.textContent = "Solo pueden editar servicios los administradores";
        }
    } catch (error) {
        $mensaje_error.textContent = "Error de conexión con el servidor.";
    }
}

//Funcion que obtiene un servicio concreto de la BD
async function getData(id) {
    try {
        const resp=await fetch(`${urlServices}/${id}`, {
            method: "GET"
        });

        const resultado= await resp.json();

        if(resp.ok){
            $formulario_servicio.nombre.value=resultado.nombre;
            $formulario_servicio.duracion.value=resultado.duracion_estimada;
            $formulario_servicio.precio.value=resultado.precio;
            $formulario_servicio.descripcion.value=resultado.informacion;
            $formulario_servicio.precio_reserva.value=resultado.precio_reserva;
            
        } else {
            //Redirije al usuario si no se encontró ningun servicio para ese id
            window.location.href = "/servicios/index.html";
        }
    } catch (error) {
        //Redirije al usuario si no se pudo obtener el servicio
        window.location.href = "/servicios/index.html";
    }
}

//Inicializa el codigo
async function iniciar() {
    try {
        const rol=await comprobarSesion();
        //Cargamos el header y footer
        loadHeaderFooter;
        //Pilla el id de la url
        let id=urlInfo.get("servicio");

        //Si el usuario no es administrador o no pillo id le redirige
        if (rol!=1 || !id){
            window.location.href = "/servicios/index.html";
        } else {
            await getData(id)
            $section_botonera.innerHTML=`<button type="button" onclick="window.location.href = './servicio.html?servicio=${id}'">Volver atrás</button>`
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

        //Se hace un try catch de la llamada a la funcion para que si falla se muestre un mensaje de error
        try {
            await editarServicio(datos);
        } catch (error) {
            $mensaje_error.textContent = "Error de conexión con el servidor.";
        }
    })
})
