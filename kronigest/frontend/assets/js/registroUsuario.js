const $d=document,
    $formulario_registro=$d.querySelector("#formulario_registro"),
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
        window.location.href = "/frontend/index.html";
    }
}

//Funcion asincrona que manda los datos al servidor para crear el usuario
async function registrarUsuario(datos) {
    //Se realiza una consulta de tipo post a la api pasandole los datos
    const resp= await fetch(`${urlUsuarios}`,{
        method:"POST",
        headers:{"Content-type":"application/json; utf-8"},
        body:JSON.stringify(datos)
    })
    //Se espera a que la api responda con un mensaje, ya sea exitoso o no
    const mensaje= await resp.json()

    //En base a la respuesta renderiza un mensaje u otro. Puede renderizar el mensaje de error propio de la api o por defecto uno genérico
    if (resp.ok) {
        $mensaje_error.textContent = mensaje.success || "Usuario registrado correctamente.";
    } else {
        $mensaje_error.textContent = mensaje.error || "Error. No se pudo registrar el usuario.";
    }
}

$d.addEventListener("DOMContentLoaded",async ev=>{
    ev.preventDefault()

    try {
        await comprobarSesion();
    } catch (error) {
        window.location.href = "/frontend/index.html";   
    }

    //Al hacer submit
    $formulario_registro.addEventListener('submit',async ev=>{
        ev.preventDefault();

        //Pillamos los datos del formulario y eliminamos espacios innecesarios
        const nombre=$formulario_registro.nombre.value.trim()
        const apellidos = $formulario_registro.apellidos.value.trim();
        const email = $formulario_registro.email.value.trim();
        const contrasena = $formulario_registro.contrasena.value.trim();
        const contrasena2 = $formulario_registro.contrasena2.value.trim();

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

        if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$&%\\.])[A-Za-z\d@#$&%\\.]{6,16}$/.test(contrasena)){
            $mensaje_error.textContent="La contraseña no sigue el formato adecuado"
            return
        }

        if (contrasena!=contrasena2){
            $mensaje_error.textContent="Las contraseñas no coinciden."
            return
        }

        //Creamos el objeto. El rol siempre será 2 que corresponde a un usuario normal
        const datos = {
            nombre,
            apellidos,
            email,
            contrasena,
            rol_id: 2
        }

        //Se hace un try catch de la llamada a la funcion para que si falla se muestre un mensaje de error
        try {
            await registrarUsuario(datos);
        } catch (error) {
            $mensaje_error.textContent = "Error de conexión con el servidor.";
            console.error(error);
        }
    })
})