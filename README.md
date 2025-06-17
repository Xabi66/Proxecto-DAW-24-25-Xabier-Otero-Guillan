# Proxecto fin de ciclo

- [Proxecto fin de ciclo](#proxecto-fin-de-ciclo)
  - [Taboleiro do proyecto](#taboleiro-do-proyecto)
  - [Descrición](#descrición)
  - [Instalación / Posta en marcha](#instalación--posta-en-marcha)
  - [Uso](#uso)
  - [Sobre o autor](#sobre-o-autor)
  - [Licenza](#licenza)
  - [Índice](#índice)
  - [Guía de contribución](#guía-de-contribución)
  - [Links](#links)
    
## Taboleiro do proyecto

Actualmente el proyecto se encuentra en fase de desarrollo.

## Descrición

Kronigest es un sistema de gestión de citas diseñado para pequeños negocios, adaptandose a sus necesidades. 
Su objetivo principal es facilitar la organización de citas y reservas, permitiendo a los usuarios reservarlas y gestionarlas de forma simple e intuitiva. Para ello cuentan con opciones como saber la duración estimada de cada servicio y la posibilidad de incluir informacion adicional al hacer la reserva.
Por su parte, las empresas pueden consultar y modificar todas las citas realizadas, ademas de tener la capacidad de gestionar su horario de apertura y los servicios que ofertan. 
Esta aplicación emplea una mezcla de varias tecnologias, entre las que se incluyen HTML, CSS, JavaScript, PHP y MySQL

## Instalación / Posta en marcha

Pasos para la instalación del proyecto:

1. Descargar y descomprimir el zip del proyecto
2. Entrar desde una terminal en la carpeta kronigest y ejecutar el comando sudo docker-compose up. Cuando pida una contraseña introducir la contraseña del administrador.
3. Desde un navegador acceder a localhost:8000 y iniciar sesión. El usuario es root y la contraseña bitnami
4. Ejecutar todo el archivo sql dentro de kronigest/bases_de_datos/qkorte.sql
5. Desde un navegador acceder a localhost para utilizar la aplicación. En caso de querer usar la cuenta del administrador el correo es qkorte@gmail.com y la contraseña es Abc123.

## Uso

Kronigest permite consultar y gestionar los servicios y citas de tú negocio desde cualquier dispositivo, de forma rápida y sencilla. Tras registrarse, los usuarios podrán ver los servicios disponibles, escoger el día y la hora de su cita y gestionarla como mejor les convenga.

## Sobre o autor

Soy un estudiante de Desenvolvimiento de Aplicaciones Web que domina tecnologías tales como HTML, CSS, JavaScript, PHP y bases de datos SQL. Tengo experiencia elaborando tanto el frontend como el backend de aplicaciones web, empleando arquitecturas cliente-servidor y APIs REST para desenvolver diversos proyectos.

Mis puntos fuertes son la estructuración y el manejo eficaz del código, tanto en el backend como en el frontend, la elaboración de diseños responsivos y la comunicación asíncrona mediante AJAX.

Este proyecto nace de la necesidad de integrar a los negocios mas pequeños en el actual mercado digital, buscando que se puedan adaptar facilmente sin que esto les suponga un gran coste. Esta dirigido a aquellas empresas que todavia gestionan sus citas de forma manual mediante canales mas tradicionales, ofreciendoles una manera de gestionarlas más cómoda tanto para ellos como para sus clientes. A diferencia de otras plataformas de ámbito mas general, este proyecto se centra en las necesidades propias de cada negocio, ofreciendoles solo las funcionalidades que realmente necesiten y evitandoles costes extra a cambio de funciones innecesarias.

En caso de ser necesario contactarme para realizar alguna pregunta o resolver alguna duda, estoy disponible mediante el siguiente correo electrónico: oteroxabier@gmail.com

## Licenza

Este proyecto esta licenciado con GNU Free Documentation License Version 1.3*. Los detalles de la misma pueden ser consultados en el fichero [LICENSE](LICENSE)

## Índice

1. [Anteproyecto](doc/templates/1_Anteproxecto.md)
2. [Análise](doc/templates/2_Analise.md)
3. [Deseño](doc/templates/3_Deseño.md)
4. [Codificación e probas](doc/templates/4_Codificacion_e_probas.md)
5. [Implantación](doc/templates/5_Implantación.md)
6. [Referencias](doc/templates/6_Referencias.md)
7. [Incidencias](doc/templates/7_Incidencias.md)

## Guía de contribución

Si quieres contribuir al proyecto eres libre de crear tu propia rama y realizar los cambios que consideres oportunos. Algunas formas de contribuir son:

>- Optimizar el código, haciendo especial hincapie en reutilizar aquellas partes del mismo que se repitan.

>- Implementar nuevas funcionalidades relacionadas con el objetivo del proyecto, como por ejemplo un sistema de reseñas de los servicios. 

>- Realizar test automáticos de diversas partes del código para demostrar como se desenvuelve frente a los errores.

>- Analizar el proyecto en busca de posibles bugs o brechas de seguridad y como solucionarlos.

## Links

1. [Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y garantía de los derechos digitales.](https://www.boe.es/buscar/act.php?id=BOE-A-2018-16673)

2. [Ley 34/2002 de Servicios de la Sociedad de la Información y del Comercio Electrónico.](https://www.boe.es/buscar/doc.php?id=BOE-A-2002-13758)
