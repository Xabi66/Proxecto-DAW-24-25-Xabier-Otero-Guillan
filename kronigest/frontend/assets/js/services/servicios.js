import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $buscador=$d.querySelector("#buscador"),
    $contenedor_servicios=$d.querySelector("#contenedor_servicios"),
    $botonera=$d.querySelector("#botonera")

//Enlace a la API
const urlServices = "http://localhost/backend/route.php/service"
const urlUser = "http://localhost/backend/route.php/user"
//Almacena los servicios obtenidos
const services = []

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

//Funcion que renderiza los servicios en base a cuales se le proporcionan
function renderServices(services){
    $contenedor_servicios.innerHTML=services.map(el=>`   
        <section onclick="location.href='servicio.html?servicio=${el.id}'" class="card">
            <figure class="card_figure">
                <img src="/assets/img/services/${el.imagen || 'services.jpg'}" alt="${el.nombre}"
                onerror="this.onerror=null; this.src='/assets/img/services/services.jpg';">
            </figure>
            <h2>${el.nombre}</h2>
            <p>${el.duracion_estimada} minutos</p>
            <p>${el.precio} €</p>
        </section>      
    `).join('') || "<p>No existe ningun servicio con ese nombre</p>"
}

//Funcion que obtiene todos los servicios de la BD
async function getData() {
    try {
        const resp=await fetch(`${urlServices}`, {
            method: "GET"
        });

        const resultado= await resp.json();

        if(resp.ok){
            services.splice(0,services.length,...resultado);
            renderServices(services);
        } else {
            console.log("No se pudo renderizar")
        }
    } catch (error) {
        console.log(error)
    }
}

$d.addEventListener("DOMContentLoaded", async ev => {
    ev.preventDefault()
    //Cargamos el header y footer
    loadHeaderFooter();
    //Si el usuario es administrador renderizamos el boton para ir a añadir servicio
    const rol= await comprobarSesion();
    if(rol==1){
        $botonera.innerHTML+=`<button type="button" onclick="location.href='altaServicio.html'">Añadir servicio</button>`
    }

    getData();

    $buscador.addEventListener("input",ev=>{

        if(ev.target.value!=""){
            renderServices(services.filter(el=>el.nombre.toLowerCase().includes(ev.target.value.toLowerCase())))
        } else {
            renderServices(services)
        }
    })
})
