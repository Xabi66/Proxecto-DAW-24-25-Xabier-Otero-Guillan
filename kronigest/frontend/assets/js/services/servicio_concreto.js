import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $main=$d.querySelector("main")

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
    }
}
//Borra el servicio si el usuario es un administrador
async function borrarServicio() {
    try {
        //Comprueba el rol al momento de borrar
        const rol=await comprobarSesion();
        //Comprueba que se trata de un usuario administrador
        if(rol==1){
            //Manda delete para el servicio de la url
            const resp = await fetch(`${urlServices}/${urlInfo.get("servicio")}`, {
                method: "DELETE",
                credentials: "include"
            });

            if (resp.ok) {
                window.location.href = "/servicios/index.html";
            }            
        } else {
            console.log("Solo pueden borrar servicios los administradores")
        }
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/servicios/index.html";
    }
}

//Funcion que renderiza los servicios en base a cuales se le proporcionan
async function renderServices(service){
    //Divide en parrafos cuando hay \n 
    const parrafos = service.informacion.split("\n").map(el => `<p>${el.trim()}</p>`).join("");
    //Comprueba si hay una sesion para asi obtener el rol
    const rol= await comprobarSesion();
    
    //Mete en el main los datos
    $main.innerHTML=`   
        <section class="section_imagen_fondo" style="background-image: url(/assets/img/services/${service.imagen || 'services.jpg'});">
            <h1>${service.nombre}</h1>     
        </section>

        ${rol==1 
            ? `<section class="section_botonera">
                <button type='button' onclick="location.href='editarServicio.html?servicio=${service.id}' ">Editar Servicio</button>
                <button class="btn-danger" type='button' id="button_delete" onclick="window.delete.showModal();">Borrar Servicio</button>
            </section>` 
            : ""}

        <p class="parrafo_grande"><strong>Duración:</strong> ${service.duracion_estimada} minutos</p>
        <p class="parrafo_grande"><strong>Precio:</strong> ${service.precio} €</p>
        <section>${parrafos}</section>
        <p><strong>Coste de la reserva:</strong> ${service.precio_reserva} €</p>
        <section class="section_botonera">
            <button type="button" onclick="location.href='reservaServicio.html?servicio=${service.id}'">Reservar</button>
        </section>
        <section class="section_botonera">
            <button type="button" onclick="window.location.href = './index.html'">Volver atrás</button>
        </section>

        ${rol==1 
        ? `<dialog id="delete">
            <section>
              <h2>ELIMINAR SERVICIO</h2>
              <p>Una vez eliminado no se podrá recuperar y todas las citas vinculadas se eliminarán.</p>
              <section class="section_botonera">
                <button type='button' class="btn-danger" onclick="window.delete.close();">Cancelar</button>
                <button type='button' class="btn-success" id="confirm_delete" >Confirmar</button>
              </section>        
            </section>
        </dialog>` 
        : ""}
        `
}

//Funcion que obtiene un servicio concreto de la BD
async function getData(id) {
    try {
        const resp=await fetch(`${urlServices}/${id}`, {
            method: "GET"
        });

        const resultado= await resp.json();

        if(resp.ok){
            renderServices(resultado);
        } else {
            $main.innerHTML=`<p>No se pudo cargar el servicio</p>`  
            window.location.href = "/servicios/index.html";
        }
    } catch (error) {
        $main.innerHTML=`<p>No se pudo conectar con el servidor</p>`  
    }
}

function iniciar(){
    //Cargamos el header y footer
    loadHeaderFooter;

    //Pilla el id de la url
    let id=urlInfo.get("servicio");

    //Si pillo id renderiza el servicio, si no redirige a index
    if(id){
        getData(id)
    } else {
        window.location.href = "/servicios/index.html";
    }
}

$d.addEventListener("DOMContentLoaded", ev => {
    ev.preventDefault()

    iniciar();

    //Cuando clickamos en main si tiene el id de confirmar borrado llama a la funcion
    $main.addEventListener("click",async ev=>{
        if (ev.target.id== "confirm_delete") {
            await borrarServicio();
        }
    })
})
