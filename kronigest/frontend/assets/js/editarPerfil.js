import { loadHeaderFooter } from './functions.js';

const $d=document,
    $formulario_edicion=$d.querySelector("#formulario_edicion"),
    $mensaje_error=$d.querySelector("#mensaje_error")

//Enlace a la API
const urlUser = "http://localhost/backend/route.php/user"

//Pasamos la cookie de sesion para saber si el usuario esta logueado o no y asi devolver sus datos
async function comprobarSesion() {
    const resp = await fetch(`${urlUser}/sesion`, {
        method: "POST",
        credentials: "include"
    });

    if (resp.ok) {
        //Recibe los datos del usuario 
        const data = await resp.json();
        //Devuelve los datos
        return data;
    } else {
        //Redirije al usuario si la cookie no estaba o era incorrecta
        window.location.href = "/inicioSesion.html";
    }
}

//Inicializa los datos de perfil tras comprobar la sesion
async function iniciar() {
    try {
        const datos=await comprobarSesion();
        //Cargamos el header y footer
        loadHeaderFooter;
        //Renderiza los datos de los inputs si la sesion se inicio
        $formulario_edicion.nombre.value= `${datos.user_name}` 
        $formulario_edicion.apellidos.value= `${datos.user_apellidos}` 
        $formulario_edicion.email.value= `${datos.user_email}` 

    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/inicioSesion.html";
    }
}

//Funcion para editar el perfil
async function editarPerfil(datosFormulario) {
    try {
        const datos=await comprobarSesion();
            
        //Manda put para la id de la sesion
        const resp = await fetch(`${urlUser}/${datos.user_id}`, {
            method: "PUT",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(datosFormulario),
            credentials: "include" //Para que deje manejar la cookie de sesion
        });
    
        const mensaje = await resp.json();
    
        if (resp.ok) {
            window.location.href = "/perfil/index.html";
        } else {
            $mensaje_error.textContent = mensaje.error || "Error. No se pudo editar los datos.";
        }     
    } catch (error) {
        console.log(error)
    }
}

$d.addEventListener("DOMContentLoaded", ev => {
    ev.preventDefault()
    //Comprueba si hay sesion activa o no
    iniciar();

    //Al clickar en el boton de confirmar, llama a la funcion
    $formulario_edicion.addEventListener("submit", async ev =>{
        ev.preventDefault();

        //Pillamos los datos del formulario y eliminamos espacios innecesarios
        const nombre=$formulario_edicion.nombre.value.trim()
        const apellidos = $formulario_edicion.apellidos.value.trim();
        const email = $formulario_edicion.email.value.trim();

        //Validamos los campos
        if(!/^[A-ZÁÉÍÓÚÑ][A-Za-záéíóúÁÉÍÓÚñÑ]*(?: [A-Za-záéíóúÁÉÍÓÚñÑ]+)*$/.test(nombre)){
            $mensaje_error.textContent="El nombre no sigue el formato adecuado"
            return
        }

        if(apellidos!="" && !/^[A-ZÁÉÍÓÚÑ][A-Za-záéíóúÁÉÍÓÚñÑ]*(?: [A-Za-záéíóúÁÉÍÓÚñÑ]+)*$/.test(apellidos)){
            $mensaje_error.textContent="Los apellidos no siguen el formato adecuado"
            return
        }

        if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)){
            $mensaje_error.textContent="El correo no sigue el formato adecuado."
            return
        }

        //Creamos el objeto a pasar.
        const datos = {
            nombre,
            apellidos,
            email
        }

        //Se hace un try catch de la llamada a la funcion para que si falla se muestre un mensaje de error
        try {
            await editarPerfil(datos);
        } catch (error) {
            $mensaje_error.textContent = "Error de conexión con el servidor.";
        }
    });
})
