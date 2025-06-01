import { loadHeaderFooter } from './functions.js';

const $d=document,
    $confirm_logout = $d.querySelector("#confirm_logout"),
    $confirm_delete = $d.querySelector("#confirm_delete"),
    $button_delete = $d.querySelector("#button_delete");

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
        document.getElementById("nombre").value = `${datos.user_name} ${datos.user_apellidos}`
        document.getElementById("email").value = `${datos.user_email}` 

        if (datos.user_rol!=2){
            $button_delete.disabled=true;
        } else {
            $button_delete.classList.add("btn-danger")
        }
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/inicioSesion.html";
    }
}

//Manda una peticion para cerrar la sesion y redirige a la página de inicio
async function cerrarSesion() {
    const resp = await fetch(`${urlUser}/logout`, {
        method: "POST",
        credentials: "include",
        cache: "no-store"
    });

    if (resp.ok) {
        window.location.href = "/index.html";
    }
}
//Borra al usuario de la BD (incluyendo su cookie de sesion) y lo redirige a inicio. Antes comprueba la cookie de sesion
async function borrarUsuario() {
    try {
        const datos=await comprobarSesion();
        //Comprueba que se trata de un usuario normal
        if(datos.user_rol==2){
            //Manda delete para la id de la sesion
            const resp = await fetch(`${urlUser}/${datos.user_id}`, {
                method: "DELETE",
                credentials: "include"
            });

            if (resp.ok) {
                window.location.href = "/index.html";
            }            
        } else {
            console.log("Solo se pueden borrar usuarios normales")
        }
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/index.html";
    }
}

$d.addEventListener("DOMContentLoaded", ev => {
    ev.preventDefault()
    //Comprueba si hay sesion activa o no
    iniciar();

    //Al clickar en el boton de confirmar, cierra la sesion
    $confirm_logout.addEventListener("click", ev =>{
        ev.preventDefault();

        cerrarSesion()
    });

    $confirm_delete.addEventListener("click", ev =>{
        ev.preventDefault();

        borrarUsuario()
    });
})
