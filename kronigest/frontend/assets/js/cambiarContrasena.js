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
        await comprobarSesion();
        //Cargamos el header y footer
        loadHeaderFooter;
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
            $mensaje_error.textContent = mensaje.error || "Error. No se pudo editar la contraseña.";
        }     
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
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
        const contrasena_actual=$formulario_edicion.contrasena_actual.value.trim()
        const contrasena = $formulario_edicion.contrasena.value.trim();
        const contrasena2 = $formulario_edicion.contrasena2.value.trim();

        if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$&%\\.])[A-Za-z\d@#$&%\\.]{6,16}$/.test(contrasena_actual)  && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$&%\\.])[A-Za-z\d@#$&%\\.]{6,16}$/.test(contrasena)){
            $mensaje_error.textContent="Las contraseñas no siguen el formato adecuado"
            return
        }

        if (contrasena!=contrasena2){
            $mensaje_error.textContent="Las contraseñas nuevas no coinciden."
            return
        }

        //Creamos el objeto a pasar.
        const datos = {
            contrasena_actual,
            contrasena
        }

        //Se hace un try catch de la llamada a la funcion para que si falla se muestre un mensaje de error
        try {
            await editarPerfil(datos);
        } catch (error) {
            $mensaje_error.textContent = "Error de conexión con el servidor.";
        }
    });
})
