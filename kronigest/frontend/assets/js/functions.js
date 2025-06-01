//Funcion que carga tanto el header como el footer
export async function loadHeaderFooter() {
    try {
        //Pilla tanto el header como el footer
        const respHeader = await fetch('../assets/fragments/header.html');
        const respFooter = await fetch('../assets/fragments/footer.html');
        

        //Si el header cargó pilla su contenido y lo renderiza en la etiqueta header
        if (respHeader.ok) {
            const contenidoHeader = await respHeader.text();
            document.querySelector('#header').innerHTML = contenidoHeader;
        } else {
            document.querySelector('#header').innerHTML = 'No se ha podido cargar el header'; 
        }

        //Si el footer cargó pilla su contenido y lo renderiza en la etiqueta footer
        if (respFooter.ok) {
            const contenidoFooter = await respFooter.text();
            document.querySelector('#footer').innerHTML = contenidoFooter;
        } else{
            document.querySelector('#footer').innerHTML = 'No se ha podido cargar el footer'; 
        }
    //En caso de error renderiza un mensaje de error en las cabeceras
    } catch (error) {
        console.log(error)
        document.querySelector('#header').innerHTML = '<p>Ha habido un error con el servidor</p>';
        document.querySelector('#footer').innerHTML = '<p>Ha habido un error con el servidor</p>';
    }
}

document.addEventListener('DOMContentLoaded', loadHeaderFooter);
  