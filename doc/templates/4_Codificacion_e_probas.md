# FASE DE CODIFICACIÓN E PROBAS

- [FASE DE CODIFICACIÓN E PROBAS](#fase-de-codificación-e-probas)
  - [1- Codificación](#1--codificación)
  - [2- Prototipos](#2--prototipos)
  - [3- Innovación](#3--innovación)
  - [4- Probas](#4--probas)

## 1- Codificación

Actualmente Kronigest cuenta con las siguientes funciones:
> - Registrar usuarios
> - Iniciar sesión
> - Visualizar el perfil si se ha iniciado sesión
> - Cambiar datos del perfil
> - Cambiar contraseña
> - Cerrar sesión
> - Borrar cuenta (solo si es un usuario común)
> - Ver los servicios disponibles
> - Añadir, editar y eliminar servicios (solo si es el administrador)
> - Pedir cita
> - Ver citas pendientes (en el caso del administrador las de todos los usuarios)
> - Ver una cita pendiente en concreto, cambiarla y eliminarla (en el caso del administrador las de todos los usuarios)
> - Ver el historial de citas finalizadas (en el caso del administrador las de todos los usuarios)

Cambios realizados sobre el prototipo original:
> - Al editar el perfil del usuario este divide entre nombre y apellidos los inputs correspondientes
> - En vez de eliminar el boton de borrar cuenta en la cuenta de la propia empresa, este se encuentra desactivado y aun si estuviese activado la aplicación detecta que esa cuenta no se puede eliminar debido a su rol.
> - El boton de eliminar un servicio está dentro de la página de ese servicio en concreto en vez de en la página de editar ese servicio

## 2- Prototipos

[Ver Figma](https://www.figma.com/design/kikFpU4xkhWabWEbbvvzY4/ProyectoDAW?node-id=2-2&t=e9g2RXawkTYaEqbH-1)

## 3- Innovación

## 4- Probas

Pruebas:

1. Registrar usuario: 
> - Valida que el nombre y los apellidos empiecen por mayúsculas, permitiendo espacios para escribir mas de un nombre o apellido. 
> - Permite que el campo apellidos sea opcional.
> - Valida que el correo tenga formato texto@texto.texto
> - Valida que las contraseña tenga como mínimo una mayuscula, una minuscula, un número y un caracter especial.
> - Valida que ambas contraseñas coincidan.
> - Devuelve un mensaje de error si se intenta registrar un correo ya registrado.
> - Redirige a iniciar sesion al registrar correctamente un usuario.
> - Redirige al perfil si hay una sesión activa.

2. Iniciar sesión:
> - Devuelve un mensaje de error si el correo y la contraseña no coinciden.
> - Si se inicia sesión de nuevo cancela la anterior, permitiendo solo una activa por usuario
> - Redirige al perfil al iniciar sesion o si ya hay una sesión activa.

3. Perfil de usuario:
> - Permite cerrar la sesión.
> - Permite eliminar la cuenta junto a sus citas asociadas.
> - Evita que el administrador borre su cuenta deshabilitando el boton correspondiente y aunque se habilite tampoco realiza el borrado.
> - Permite editar la información del perfil como apellidos o correo
> - Valida que al cambiar contraseña la actual sea correcta, que la nueva no sea igual que la actual y que el campo de repetir contraseña coincida con el de nueva contraseña
> - Cierra la sesión al cambiar la contraseña
> - Redirige a iniciar sesión si no hay ninguna sesión iniciada

4. Servicios:
> - Permite ver los servicios disponibles
> - Permite filtrarlos por nombre si contiene x serie de letras ya sean en mayúscula o minúscula.
> - Permite ver la información de x servicio
> - Redirige a servicios si se escribe en la url el id de uno inexistente
> - Permite crear uno nuevo si se es administrador, dividiendo su informacion en parrafos al introducir un salto de linea y dejando opcionales los campos informacion y coste de la reserva. La imagen por el momento no deja introducirla y coge una por defecto.
> - Permite editar un servicio concreto si se es administrador. La imagen por el momento no deja cambiarla.
> - Permite eliminar un servicio si se es administrador, eliminando tambien sus citas asociadas.
> - Redirije a servicios si se intentar acceder a editar un servicio sin ser administrador

5. Pedir cita:
> - Permite pedir cita para un servicio, incluyendo o no información adicional
> - Renderiza solo los turnos disponibles segun la duración de cada servicio
> - Comprueba que el turno no comenzó ya o ya está ocupado antes de crear la cita
> - Comprueba que la cita no comienza o finaliza fuera del horario disponible
> - Comprueba que no se intenta pedir cita para un dia que no está disponible.
> - Redirige a inicio de sesion si no hay ninguna sesión iniciada

6. Citas pendientes:
> - Actualiza el estado de las citas que ya acabaron de pendientes a finalizadas antes de visualizarlas
> - Permite visualizar las citas pendientes para cada dia
> - Permite visualizar la información de cada cita en concreto
> - Permite eliminar una cita.
> - Verifica que el turno no se ocupó ya antes de editar una cita.
> - Verifica que no se intenta editar una cita que ya ha comenzado
> - Verifica que no se intenta cambiar la cita a un turno que ya ha comenzado
> - Verifica que no se intenta mover una cita a una fecha no disponible
> - Verifica que al editar la cita esta esté dentro del horario disponible para ese dia
> - Verifica que no se intenta editar una cita ya finalizada
> - Redirige a inicio de sesion si no hay ninguna sesión iniciada
> - Redirige a citas pendientes si se intenta acceder a una cita de otra persona y no se es el administrador.

7. Historial:
> - Actualiza el estado de las citas que ya acabaron de pendientes a finalizadas antes de visualizarlas
> - Permite visualizar las citas ya finalizadas, ya sean propias o todas en el caso del administrador.
> - Permite buscar citas segun el nombre del servicio
> - Redirige a inicio de sesion si no hay ninguna sesión iniciada

Problemas encontrados al realizar las pruebas:

1. Al crear o editar una cita no se verificaba de nuevo que estuviese dentro del horario disponible para ese dia
> - Solución: reutilizando parte del código usado al momento de crear los turnos disponibles, cree una función que verifique que la cita este dentro del horario disponible para ese dia. Luego llame a la funcion dentro de los metodos insert y update y ademas les añadi que tambien verifiquen de nuevo que no se está intentado pedir cita para un dia no disponible.

[**<-Anterior**](../../README.md)
