# Requerimientos do sistema

- [Requerimientos do sistema](#requerimientos-do-sistema)
  - [1- Descrición Xeral](#1--descrición-xeral)
  - [2- Funcionalidades](#2--funcionalidades)
  - [3- Tipos de usuarios](#3--tipos-de-usuarios)
  - [4- Contorno operacional](#4--contorno-operacional)
  - [5- Normativa](#5--normativa)
  - [6- Melloras futuras](#6--melloras-futuras)

## 1- Descrición Xeral

Este proyecto consiste en el desarrollo de una aplicación web para la gestión de citas y reservas, dirigida a pequeños negocios tales como peluquerias o clínicas dentales. El objetivo es proporcionar una plataforma económica e intuitiva que no suponga un gran gasto a las empresas a la hora de incorporarla, permitiendo asi que aquellas empresas con menos recursos puedan aumentar su digitalización y reducir el tiempo invertido en gestionar las citas.

Los usuarios podrñan registrarse en la plataforma y a traves de la misma pedir cita y gestionar aquellas que tenga pendientes. Ademas contará con funciones tales como poder saber el tiempo aproximado que llevará el servicio que desea reservar y incluir información adicional al momento de la reserva para que así el negocio pueda organizarse mejor.

Por parte de los clientes, estos obtendran una forma sencilla de gestionar su calendario de citas, pudiendo realizar cualquier modificación sin ningun problema. Ademas, la plataforma está pensada para ajustarse a sus necesidades, pudiendo solicitar que incluya funciones adicionales al momento de contratar el servicio.

La aplicación estará diseñada de forma responsive, haciendola accesible desd ecualquier dispositivo, y combinará tecnologias tales como HTML, CSS; JavaScript y PHP, buscando ofrecer una experiencia lo suficientemente intuitiva como para que cualquier tipo de usuario pueda emplearla aun sin contar con conocimientos tecnológicos.

## 2- Funcionalidades

| Acción   |  Descrición        | Actores |
|----------|--------------------| --------|
| 1. Gestionar los servicios  | Se añaden, eliminan y modifican los servicios y su informacion | Empresa |
| 2. Gestionar la disponibilidad | Se gestionan que dias y a que horas la empresa está disponible | Empresa |
| 3. Consultar las citas pendientes | Se consulta que citas hay pedidas | Empresa |
| 4. Gestionar las citas | Se modifica o cancela una cita | Usuario o Empresa |
| 5. Alta de usuarios   | El usuario crea una cuenta proporcionando los datos que se le requieran. | Usuario |
| 6. Inicio de sesión | Se accede a una cuenta mediante el correo y la contraseña | Usuario o Empresa |
| 7. Consultar los servicios | Se consulta que servicios se ofertan y su información | Usuario |
| 8. Comprobar los horarios | Se comprueba a que horas es posible pedir cita para un servicio concreto | Usuario |
| 9. Pedir una cita | Se pide una cita para un servicio concreto, aportando información adicional si así se desea y realizando el pago en caso de ser necesario | Usuario |
| 10. Pagar | El usuario paga la reserva a traves de la aplicación externa | Usuario |
| 11. Consultar el historial | Se consulta el historial de citas | Usuario o Empresa |

## 3- Tipos de usuarios

Los tipos de usuarios que habrá disponibles en la aplicación son:

>-Usuario anónimo, que podrá navegar por la web, ver los servicios que puede reservar junto a su información y para que horarios tiene citas disponibles.

>-Usuario registrado, que podrá realizar las mismas tareas que un usuario anonimo y ademas podrá pedir la cita como tal añadiendole información adicional si asi lo quiere, ver su historial de citas, gestionar sus citas pendientes y recibir cualquier actualización relativa a las mismas.

>-Usuario bloqueado, que debido a X motivos no podrá iniciar sesion en su cuenta y por lo tanto carecerá de la posibilidad de realizar cualquier reserva.

>-Cliente, que será la cuenta de la propia empresa y podrá acceder al panel de control para ver que citas tiene pendientes, gestionar o cancelar las citas, acceder al historial de todas las citas y añadir o modificar los servicios disponibles.

>-Administrador, que será la propia cuenta del desarrollador de la aplicación y podrá modificarla para solucionar errores, incorporar cualquier función que le sea solicitada y **modificar el código????**.

## 4- Contorno operacional

Para operar con la aplicación web, el usuario solo necesita disponer de un dispositivo con conexión a internet y un navegador web actualizado.

## 5- Normativa

El proyecto se adaptará a la normativa vigente en España, al ser una aplicación a nivel nacional, con el objetivo de garantizar el cumplimiento de la ley.

**INDICA QUIEN ES EL RESPONSABLE, QUE DATOS SE ALMACENAS, DONDE ESTÁ ESTA INFORMACIÓN Y COMO PODEMOS CANDELARLOS O MODIFICAR EL ACCESO A ESTOS DATOS**
Debido a que la aplicación contará con cuentas de usuario estas contendrán algunos de sus datos personales, por lo que será necesario cumplir con la LOPDPGDD.
Además, debido al hecho de que la aplicación notificará al usuario el estado de sus citas cuando por ejemplo estas hayan sido reprogramadas, es necesario cumplir también la Ley 34/2002 de Servicios de la Sociedad de la Información y del Comercio Electrónico. Esta ley regula el contacto entre la empresa y el cliente a traves de un intermediario y el envio de notificaciones realizado a traves de la aplicación. Obliga a proporcionar información sobre el resposable de la aplicación y no enviar comunicaciones de caracter comercial sin permiso

Para cumplir con estas normativas, la aplicación incluirá:

>- Aviso legal: Incluyendo toda la información necesaria para identificar al responsable de la aplicación, ademas de explicar la finalidad de la aplicación, sus condiciones de uso, la propiedad intelectual de los contenidos de la aplicación, las responsabilidades y que la aplicación se ajusta a las leyes españolas.

>- Política de privacidad: Explicando qué datos se recogen, cómo y con que fin se emplean, a quien se comparten, que medidas se toman para protegerlos, que derechos tienen los usuarios sobre los mismos y como ejercer esos derechos.

>- Política de cookies: Explicando como se emplean las cookies recogidas, que información almacenan y ofreciendo la posibilidad de escoger cuales se aceptan y cuales se rechazan (pudiendo negarse el uso de todas).

## 6- Melloras futuras

Este proyecto busca centrarse en crear un gestor de citas adaptado a cada negocio en particular, incorporando aquellas caracteristicas que cada negocio requiera. Por este motivo, lo mas probable es que conforme más negocios contraten este servicio se necesite desenvolver nuevas características que se ajusten a sus necesidades.

Por ejemplo es posible que algun negocio solicitase que la aplicación incluya un apartado con estadísticas para saber que servicios son mas populares. Otra posibilidad sería que por ejemplo un negocio que cuenta con varios profesionales solicite que la aplicación permita agendar citas por separado a cada uno de forma automatica. Tambien es posible que se solicite una funcion que permita reservar citas automaticamente cada x tiempo en negocios donde se realicen varias sesiones para proporcionar un servicio.

[**<-Anterior**](../../README.md)
