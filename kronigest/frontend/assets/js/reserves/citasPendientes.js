import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $inputFecha=$d.querySelector("#fecha"),
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
        <section class="card_reserve" onclick="location.href='cita.html?cita=${el.id}'">
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
    `).join('') || "<p class='parrafo_grande'>No hay ninguna cita para ese dia</p>"
}

//Funcion que obtiene todos los servicios de la BD
async function getData(datos) {
    try {
        const resp=await fetch(`${urlReserve}/pending`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos),
            credentials: "include" // Para que deje manejar la cookie de sesión
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
        //Le da al input de fechas como minimo la fecha de hoy
        $inputFecha.min = new Date().toLocaleDateString('sv-SE');
        $inputFecha.value = new Date().toLocaleDateString('sv-SE'); 
        //Renderizamos las citas
        renderizarDatos();
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/index.html";
    }
}

async function renderizarDatos(){
    const fecha=$inputFecha.value

    const datos = {fecha}

    getData(datos)
}

$d.addEventListener("DOMContentLoaded", async ev => {
    ev.preventDefault()

    iniciar();

    $inputFecha.addEventListener("change",async ev=>{
        ev.preventDefault()

        renderizarDatos()
    })
})
