import { loadHeaderFooter } from '../functions.js';

const $d=document,
    $main=$d.querySelector("main")

//Enlace a la API
const urlReserve = "http://localhost/backend/route.php/reserve"
const urlUser = "http://localhost/backend/route.php/user"
//Pilla la url y sus parametros
const urlInfo = new URLSearchParams(document.location.search)

//Pasamos la cookie de sesion para saber si el usuario esta logueado o no
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
        //Redirije a inicio de sesion si el usuario no esta autenticado
        window.location.href = "/inicioSesion.html";
    }
}

//Borra la cita
async function borrarCita() {
    try {
        //Comprueba que aun hay sesion
        await comprobarSesion();

        //Manda delete para la citade la url
        const resp = await fetch(`${urlReserve}/${urlInfo.get("cita")}`, {
            method: "DELETE",
            credentials: "include"
        });

        if (resp.ok) {
            window.location.href = "/citas/index.html";
        }             
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/citas/index.html";
    }
}

//Funcion que renderiza los servicios en base a cuales se le proporcionan
async function renderCitas(reserve){
    //Mete en el main los datos
    $main.innerHTML=`   
        <section class="section_imagen_fondo" style="background-image: url(/assets/img/services/${reserve.servicio.imagen || 'services.jpg'});">
            <h1>${reserve.servicio.nombre}</h1> 
        </section>

        <section class="section_botonera">
            <button type='button' onclick="location.href='editarCita.html?cita=${reserve.id}'" ${reserve.estado !== 'pendiente' ? 'disabled' : ''}>Editar Cita</button>
            <button class="btn-danger" type='button' id="button_delete" ${reserve.estado !== 'pendiente' ? 'disabled' : ''}>Borrar Cita</button>
        </section>

        <p class="parrafo_grande"><strong>Cliente:</strong> ${reserve.usuario.nombre} ${reserve.usuario.apellidos}</p>
        <p class="parrafo_grande"><strong>Fecha:</strong> ${reserve.fecha}</p>
        <p class="parrafo_grande"><strong>Hora:</strong> ${reserve.hora_inicio} - ${reserve.hora_fin}</p>
        <p class="parrafo_grande"><strong>Precio:</strong> ${reserve.servicio.precio} €</p>
        
        <label for="descripcion">Informacion adicional</label>
        <textarea name="descripcion" id="descripcion" rows="10" cols="50" disabled>${reserve.informacion_adicional}</textarea>
        <section class="section_botonera">
            <button type="button" onclick="window.location.href = './index.html'">Volver atrás</button>
        </section>
        `
}

//Funcion que obtiene una cita concreta de la BD
async function getData(id) {
    try {
        const resp=await fetch(`${urlReserve}/${id}`, {
            method: "GET"
        });

        const resultado= await resp.json();

        if(resp.ok){
            renderCitas(resultado);
        } else {
            window.location.href = "/citas/index.html";
        }
    } catch (error) {
        $main.innerHTML=`<p>No se pudo conectar con el servidor</p>`  
    }
}

async function iniciar(){
    try {
        await comprobarSesion();
        //Cargamos el header y footer
        loadHeaderFooter();
        //Pilla el id de la url
        let id=urlInfo.get("cita");

        //Si pillo id renderiza el servicio, si no redirige a index
        if(id){
            getData(id)
        } else {
            window.location.href = "/citas/index.html";
        }
    } catch (error) {
        //Redirije al usuario si no se pudo conectar con el servidor
        window.location.href = "/index.html";
    }
}

$d.addEventListener("DOMContentLoaded", ev => {
    ev.preventDefault()

    iniciar();

    //Cuando clickamos en main si tiene el id de button_delete genera su dialog para confirmar el borrado
    $main.addEventListener("click", async ev => {
        if (ev.target.id == "button_delete") {
            ev.preventDefault();
            Swal.fire({
                title: "ELIMINAR CITA",
                text: "Tras confirmar se cancelará la cita y se reembolsara cualquier pago",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: 'btn-success',
                    cancelButton: 'btn-danger'
                }
            }).then(result => {
                if (result.isConfirmed) {
                    borrarCita();
                }
            });
        }
    });
    
})
