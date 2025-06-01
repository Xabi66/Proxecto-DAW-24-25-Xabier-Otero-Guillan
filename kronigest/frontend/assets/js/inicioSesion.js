import { loadHeaderFooter } from './functions.js';

const $d=document,
    $formulario_inicio_sesion=$d.querySelector("#formulario_inicio_sesion"),
    $mensaje_error=$d.querySelector("#mensaje_error")

//Enlace a la API
const urlUsuarios = "http://localhost/backend/route.php/user";

//Si ya hay una sesion activa redirige a inicio 
async function comprobarSesion() {
    const resp = await fetch(`${urlUsuarios}/sesion`, {
        method: "POST",
        credentials: "include"
    });

    if (resp.ok) {
        //Redirije al usuario si ya hay una sesion activa
        window.location.href = "/perfil/index.html";
    }
}

//Funcion para iniciar sesion
async function iniciarSesion(datos) {
    const resp = await fetch(`${urlUsuarios}/login`, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(datos),
        credentials: "include" //Para que deje manejar la cookie de sesion
    });

    const mensaje = await resp.json();

    if (resp.ok) {
        window.location.href = "/perfil/index.html";
    } else {
        $mensaje_error.textContent = mensaje.error || "Error. No se pudo iniciar sesión.";
    }
}

$d.addEventListener("DOMContentLoaded", async ev => {
    ev.preventDefault();

    try {
        await comprobarSesion();
    } catch (error) {
        window.location.href = "/index.html";   
    }

    //Cargamos el header y footer
    loadHeaderFooter;

    //Al hacer submit
    $formulario_inicio_sesion.addEventListener("submit", async ev => {
        ev.preventDefault();

        //Pillamos los datos del formulario y eliminamos espacios innecesarios
        const email = $formulario_inicio_sesion.email.value.trim();
        const contrasena = $formulario_inicio_sesion.contrasena.value.trim();

        //Validamos los campos
        if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)){
            $mensaje_error.textContent="El correo no sigue el formato adecuado."
            return
        }

        if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$&%\\.])[A-Za-z\d@#$&%\\.]{6,16}$/.test(contrasena)){
            $mensaje_error.textContent="La contraseña no sigue el formato adecuado"
            return
        }

        //Creamos el objeto a pasar.
        const datos = { email, contrasena };

        //Se hace un try catch de la llamada a la funcion para que si falla se muestre un mensaje de error
        try {
            //Vuelve a comprobar que no haya una sesion iniciada por si se inicio en otra ventana
            await comprobarSesion();
            await iniciarSesion(datos);
        } catch (error) {
            $mensaje_error.textContent = "Error de conexión con el servidor.";
            console.error(error);
        }
    });
});
