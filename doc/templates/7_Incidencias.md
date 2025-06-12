# INCIDENCIAS E TAREFAS
- [INCIDENCIAS E TAREFAS](#incidencias-e-tarefas)
  - [1- Incidencias](#1--incidencias)
  - [2- Tarefas](#2--tarefas)

## 1- Incidencias

1. Por defecto en PHP los dias de la semana se devuelven en ingles, por lo que para solucionarlo tuve que buscar una clase que formatea las fechas en base a un idioma que se le pasa en el constructor.

2. Al principio solo comprobaba los turnos posibles al renderizarlos, pero para evitar que alguien pueda manipular el frontend tuve que encapsular en funciones algunas partes del código para asi reutilizarlas al insertar y actualizar citas.

3. Antes manejaba las sesiones con cookies y sesiones unicamente, lo que provocaba que si no se hacia logout la sesion siguiese activa pese a caducar la cookie, por lo que para evitar esto cambie el manejo a una tabla en la BD que almacene un token para cada usuario.

4. Al principio localhost servia todo el proyecto, por lo que tuve que redirigirlo para que sirviese frontend/index.html

5. Para evitar que se pudiese editar una cita una vez esta comenzase tuve que comprobar en base a si estaba en curso pero todavia no se habia cambiado su estado a finalizada ya que este se cambia una vez acaba. 

## 2- Tarefas

[**<-Anterior**](../../README.md)
