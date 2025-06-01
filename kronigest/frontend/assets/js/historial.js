import { loadHeaderFooter } from './functions.js';

const $d=document,
    $buscador=$d.querySelector("#buscador"),
    $contenedor_citas=$d.querySelector("#contenedor_citas")

//Enlace a la API
const urlReserve = "http://localhost/backend/route.php/reserve"
const urlUser = "http://localhost/backend/route.php/user"
//Almacena las citas
const reserves = []

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
        //Redirije a inicio de sesion si el usuario no esta autenticado
        window.location.href = "/inicioSesion.html";
    }
}

//Funcion que renderiza los servicios en base a cuales se le proporcionan
function renderReserves(reserves){
    $contenedor_citas.innerHTML=reserves.map(el=>`   
        <section class="card_reserve disabled">
            <figure class="figure_reserve">
                <img src="/assets/img/services/${el.servicio.imagen || 'services.jpg'}" alt="${el.servicio.nombre}">
            </figure>
            <section class="info_reserve">
                <h2>${el.servicio.nombre}</h2>
                <p><strong>Cliente:</strong>  ${el.usuario.nombre} ${el.usuario.apellidos}</p>
                <p><strong>Fecha:</strong>  ${el.fecha}</p>
                <p><strong>Hora:</strong>  ${el.hora_inicio} - ${el.hora_fin}</p>
                <p><strong>Precio:</strong> ${el.servicio.precio} €</p>
            </section>
        </section>      
    `).join('') || "<p class='parrafo_grande'>No has realizado ninguna reserva</p>"
}

//Funcion que obtiene todos los servicios de la BD
async function getData() {
    try {
        const resp=await fetch(`${urlReserve}/history`, {
            method: "GET"
        });

        const resultado= await resp.json();

        if(resp.ok){
            reserves.splice(0,reserves.length,...resultado);
            renderReserves(reserves);
        } else {
            console.log("No se pudo renderizar")
        }
    } catch (error) {
        console.log(error)
    }
}

//Inicializa el codigo
async function iniciar() {
    try {
        await comprobarSesion();
        //Cargamos el header y footer
        loadHeaderFooter;
        //Renderizamos las citas
        getData();
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/index.html";
    }
}

$d.addEventListener("DOMContentLoaded", async ev => {
    ev.preventDefault()

    iniciar();
    //El buscador filtra el historial en base al nombre del servicio
    $buscador.addEventListener("input",ev=>{
        renderReserves(reserves.filter(el=>el.servicio.nombre.toLowerCase().includes(ev.target.value.toLowerCase())))
    })
})
