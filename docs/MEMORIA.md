# Memoria · Práctica 2: gestores de contenido

**Alumno/a:** [Nombre y apellidos]  
**Curso:** [Grupo]  
**Fecha:** [Fecha de entrega]  
**Repositorio:** [Enlace de GitHub]

## 1. Objetivo

Se ha instalado Moodle 4.5 LTS en contenedores Docker y se ha configurado un aula virtual titulada «Fotografía digital desde cero». El proyecto permite repetir la instalación a partir del código publicado, sin guardar contraseñas ni datos persistentes en Git.

## 2. Requisitos identificados

- Docker y Docker Compose.
- Un mínimo recomendado de 4 GB de RAM libre.
- Puerto local 8080 disponible.
- Conexión a Internet durante la primera construcción.
- Navegador moderno.
- Credenciales seguras y listado de alumnos en formato CSV.

La arquitectura consta de Moodle con Apache/PHP, MariaDB para la base de datos y un segundo contenedor que ejecuta las tareas programadas. La base de datos está en una red interna y no publica puertos al equipo anfitrión.

> **Evidencia 1:** insertar captura de `docker compose ps` con los tres servicios saludables.

## 3. Instalación y configuración

Se creó `.env` a partir de `.env.example`, se cambiaron las contraseñas y se ejecutó `docker compose up -d --build`. La instalación automática creó el administrador, el sitio y el curso inicial. Los datos se conservan en volúmenes Docker aunque se reinicien los contenedores.

> **Evidencia 2:** insertar captura de la portada de Moodle con el nombre del sitio.

## 4. Usuarios y roles

Los usuarios se definen en `data/usuarios.csv`. Se incluyen los roles `student` y `editingteacher`; el administrador se configura mediante variables de entorno. Las contraseñas iniciales son temporales y Moodle solicita cambiarlas en el primer acceso.

> **Evidencia 3:** insertar captura de la lista de participantes mostrando profesor y alumnos. Ocultar correos o datos que no deban aparecer.

## 5. Personalización de la interfaz

El sitio utiliza nombre, nombre corto y portada propios. El curso está dividido por temas, tiene descripción específica y activa el seguimiento de finalización. Como mejora visual manual se puede seleccionar el tema Boost, cambiar el color principal y añadir una imagen de cabecera desde Administración del sitio → Apariencia.

> **Evidencia 4:** insertar captura del curso y sus bloques o secciones.

## 6. Módulos, actividades y menú

Se habilitaron cuatro actividades:

1. Una página de lectura y apoyo a vídeo sobre el triángulo de exposición.
2. Una tarea calificable de 0 a 10 con fecha límite.
3. Un foro de participación con normas de convivencia.
4. Una elección tipo test para comprobar conceptos básicos.

> **Evidencia 5:** insertar captura del índice del curso con las cuatro actividades.  
> **Evidencia 6:** insertar captura del libro de calificaciones o de una actividad evaluada.

## 7. Seguridad

- Política de contraseñas de al menos 12 caracteres, con mayúsculas, minúsculas, cifras y símbolos.
- Cambio obligatorio de la contraseña inicial del alumnado.
- Base de datos sin puerto público y red interna de Docker.
- Puerto web enlazado únicamente a la interfaz local.
- Cookies HTTP-only y cookies seguras cuando se configura HTTPS.
- Tareas cron separadas para mantenimiento.
- Secretos en `.env`, excluidos del repositorio.
- Copia de seguridad previa a cada actualización.

En producción se debe usar HTTPS con un proxy inverso, correo real, dominio válido, cortafuegos y una política de conservación de copias.

> **Evidencia 7:** insertar captura de Administración del sitio → Seguridad → Políticas del sitio.

## 8. Foro y reglas de acceso

El foro exige mensajes respetuosos, comentarios constructivos y permiso para publicar imágenes de terceros. Solo las personas matriculadas pueden participar. Los estudiantes pueden publicar y responder; el profesor modera y evalúa cuando corresponda.

> **Evidencia 8:** insertar captura del foro y sus instrucciones.

## 9. Pruebas realizadas

| Prueba | Resultado esperado | Resultado |
|---|---|---|
| Acceso web | La pantalla de identificación devuelve HTTP 200 | [OK/Pendiente] |
| Base de datos | MariaDB responde y aparece saludable | [OK/Pendiente] |
| Inicio de administrador | Acceso al panel de administración | [OK/Pendiente] |
| Inicio de estudiante | Solicita cambiar la contraseña temporal | [OK/Pendiente] |
| Permisos | El estudiante no puede editar el curso | [OK/Pendiente] |
| Actividades | El curso contiene al menos cuatro actividades | [OK/Pendiente] |
| Entrega | El alumno entrega y el profesor califica de 0 a 10 | [OK/Pendiente] |
| Foro | El alumno publica y responde; un visitante no puede hacerlo | [OK/Pendiente] |
| Persistencia | Los datos siguen presentes tras reiniciar | [OK/Pendiente] |
| Copia | Se generan base de datos y fichero de datos comprimidos | [OK/Pendiente] |

La comprobación automática se ejecuta con `./scripts/smoke-test.sh`. Las pruebas de permisos y flujo educativo se completan manualmente con cuentas de profesor y estudiante.

## 10. Actualización

`scripts/update.sh` crea primero una copia, reconstruye Moodle 4.5 con las correcciones disponibles, aplica la actualización de base de datos y vacía las cachés. Tras ello se repite la prueba automática y el inicio de sesión de los roles principales.

## 11. Copias de seguridad

`scripts/backup.sh` genera una exportación comprimida de MariaDB y otra de `moodledata`. Para una recuperación completa hacen falta ambos elementos y la misma versión del código. No se publican en Git porque pueden contener información personal.

> **Evidencia 9:** insertar captura del resultado del script y de los dos archivos, sin mostrar secretos.

## 12. Conclusión

La práctica demuestra la instalación y mantenimiento de un LMS, la gestión de roles, la creación de contenidos y actividades, la aplicación de controles de seguridad, las pruebas funcionales y la estrategia de copias y actualizaciones. La configuración como código facilita repetir, revisar y entregar el trabajo mediante GitHub.

