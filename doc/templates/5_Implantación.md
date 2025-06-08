# FASE DE IMPLANTACIÓN

- [FASE DE IMPLANTACIÓN](#fase-de-implantación)
  - [1- Manual técnico](#1--manual-técnico)
    - [1.1- Instalación](#11--instalación)
    - [1.2- Administración do sistema](#12--administración-do-sistema)
  - [2- Manual de usuario](#2--manual-de-usuario)
  - [3- Melloras futuras](#3--melloras-futuras)

## 1- Manual técnico

### 1.1- Instalación

Para continuar desenvolviendo el proyecto es necesario contar con lo siguiente:

>-Un entorno de desarrollo como por ejemplo Visual Studio
>- Tener Docker instalado
>- Un navegador web como por ejemplo Google Chrome

Para comenzar debe descargarse el fichero zip del proyecto y descomprimirlo en la ubicación deseada. Tras esto hay que acceder desde una terminal a la carpeta del proyecto y levantar los contenedores con docker-compose up. Una vez levantados, hay que acceder desde un navegador a localhost:8000, introduciendo como nombre de usuario root y como contraseña bitnami, y ejecutar el codigo del archivo sql que se encuentra dentro del proyecto en la carpeta bases_de_datos. Tras esto la aplicación ya estará funcionando con todos sus datos y funcionalidades disponibles.

Los usuarios a los que va dirigida la aplicación son por un lado los clientes, que la implementaran en su negocio para recibir las reservas a traves de ella, y por otro lado los propios usuarios, quienes serán las personas que consuman estos negocios. 

En ambos casos es necesario contar con algunos conocimientos básicos a la hora de manejarse en internet, tales como saber registrarse e iniciar sesión empleando una cuenta de correo electrónico o saber como realizar pagos online. En el caso de los usuarios clientes, es conveniente que tambien comprendan parcialmente el funcionamiento de la aplicación, para que puedan gestionar los servicios, citas,... de forma óptima sin causar problemas tales como cambiar una cita o eliminar un servicio por error.

En cuanto a como poner en marcha el proyecto para su uso por parte de los clientes, tanto de la instalación como mantenimiento generalmente se encargará Kronigest. La instalación se hará subiendo el proyecto al servicio de hosting y poniendolo en marcha para acceder a el desde el dominio seleccionado. Una vez hecho esto el cliente podrá acceder a la aplicación escribiendo la URL correspondiente en el navegador.

### 1.2- Administración do sistema

Una vez el sistema este funcionando correctamente es conveniente realizar las siguientes tareas:

> - Copias de seguridad del proyecto: Se recomienda tener una copia de seguridad del código fuente del proyecto, así como realizar periodicamente copias de seguridad de los archivos añadidos al mismo, tales como por ejemplo las imágenes de los servicios.

> - Copias de seguridad de la BD: Es conveniente realizar de forma diaria una copia incremental de la información de la base de datos para registrar todas las citas realizadas. Sería conveniente automatizar esta copia mediante la realización de una tarea programada en la terminal y almacenarla en una memoria externa o en la nube.

> - Gestión de usuarios: Una vez la aplicación este en funcionamiento toda la gestión de usuarios se realizará desde la misma. En caso de perdida de la contraseña los usuarios tendrán la opcion de recibir en el correo asociado un enlace de recuperación donde cambiar la contraseña por una nueva.

> - Gestion de la seguridad: Para mayor seguridad convendría cambiar de forma periodica las contraseñas tanto de la base de datos como del perfil de usuario. Ademas una vez la aplicación sea puesta en producción se empleará HTTPS para el acceso a ella, configurando la cookie de sesión como secure.

> - Gestion de incidencias: 

> - - De sistema: Se monitorizará cualquier posible ataque o intento de acceso no autorizado, solucionandose inmediatamente una vez detectado y restaurando la copia de seguridad más reciente de ser necesario.

> - - De software: Se registrará cualquier incidencia en un documento o aplicación de tickets para su posterior resolución, ademas de facilitar la comunicación con los clientes y usuarios para que estos puedan reportar cualquier problema. 

## 2- Manual de usuario

La aplicación busca ser intuitiva y sencilla de manjear incluso para aquellos usuarios que carecen de conocimientos informáticos avanzados. No es necesario formarlos como tal, pero si que estos comprendan algunos conceptos como que es un correo electrónico o como realizar pagos por internet. 

En cuanto a los clientes de la aplicación, es decir los empleados de la empresa que la implemente, estos si que necesitaran contar con algunos conocimientos mas avanzados para poder saber como gestionarla apropiadamente. Para ello contarán con un manual de usuario donde se explicará todas las funciones de la aplicación y como emplearlas correctamente.

Este manual detallará lo siguiente:

>- Como acceder a la cuenta de la empresa y gestionar la información de la misma.

>- Como acceder al apartado de servicios y gestionarlos. Esto incluye desde que significa cada campo hasta en que afectan los cambios realizados a las citas actuales.

>- Como visualizar las citas pendientes y gestionarlas, indicando ademas como les afecta cualquier posible cambio.

>- Como acceder al historial de citas de la aplicación y filtrar por servicio.

## 3- Melloras futuras

Respecto a la versión actual de la aplicación, existe una amplia gama de mejoras que podrían ser implementadas para optimizar su rendimiento e incrementar las funciones disponibles. Algunas de ellas son:

> - Implementar un sistema de notificaciones que informe tanto al cliente como a los usuarios cuando se cree o modifique una cita
> - Añadir la posibilidad de subir una imagen propia directamente desde la aplicación a la hora de crear o editar un servicio
> - Optimizar el código de la aplicación para reutilizar mas partes del mismo
> - Añadir la posibilidad de seleccionar que la sesión se mantenga activa durante x tiempo
> - Añadir un sistema de verificación a la hora del cambiar el correo electrónico asociado a una cuenta, enviando tanto un email de aviso al correo antiguo con la opción de revertir el cambio como uno de confirmación al nuevo correo para confirmar el cambio.
> - Integrar una plataforma de pago externa para aquellas reservas que incluyan algun coste al momento de realizarlas
> - Implementar la posibilidad de recuperar la contraseña mediante un mensaje al correo electrónico con un enlace para cambiar la misma
> - Cancelar y/o redistribuir adecuadamente las citas cuando se modifique el horario o la disponibilidad de un dia.
> - Implementar informes y estadisticas para que el cliente conozca cuales son los servicios mas populares y que franja horaria es la mas activa

[**<-Anterior**](../../README.md)
