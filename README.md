# Práctica 2 · Moodle en Docker

Proyecto reproducible para desplegar un gestor de contenidos educativos Moodle con MariaDB. La instalación crea automáticamente un curso de fotografía, cuatro actividades, usuarios con roles diferentes, reglas de acceso y medidas básicas de seguridad.

## Requisitos

- Docker Desktop o Docker Engine con Docker Compose.
- 4 GB de RAM libres recomendados.
- Puerto `8080` disponible.

## Puesta en marcha

1. Copia `.env.example` como `.env`.
2. Sustituye todas las contraseñas de ejemplo de `.env`.
3. Edita `data/usuarios.csv` con los compañeros reales. Conserva la cabecera y usa `student` o `editingteacher` en la columna `role`.
4. Arranca el proyecto:

   ```bash
   docker compose up -d --build
   ```

5. Sigue el progreso:

   ```bash
   docker compose logs -f moodle
   ```

6. Abre <http://localhost:8080>. El primer arranque puede tardar varios minutos.

La cuenta administradora se toma de `.env`. Las cuentas del CSV reciben la contraseña temporal definida en `STUDENT_INITIAL_PASSWORD` y deben cambiarla en el primer acceso.

## Contenido creado

- Curso `FOTO101 · Fotografía digital desde cero`.
- Roles: administrador, profesor editor y estudiantes.
- Lectura sobre el triángulo de exposición.
- Tarea calificable sobre la regla de los tercios.
- Foro con normas de participación y límite de adjuntos.
- Test rápido de elección sobre velocidad de obturación.
- Seguimiento de finalización, política de contraseñas y ejecución de cron cada minuto.
- MariaDB aislada en una red interna y Moodle publicado solo en `127.0.0.1:8080`.

## Pruebas

Con Moodle arrancado:

```bash
set -a; . ./.env; set +a
./scripts/smoke-test.sh
```

También conviene comprobar manualmente los casos descritos en `docs/MEMORIA.md` y añadir capturas propias.

## Copia de seguridad

```bash
set -a; . ./.env; set +a
./scripts/backup.sh
```

Se guardan por separado la base de datos y `moodledata` en `backups/`. Los ficheros de copia están excluidos de Git porque pueden contener datos personales.

## Personalización visual

El tema Boost se personaliza sin modificar el núcleo de Moodle: paleta turquesa,
cabecera con degradado, tarjetas, botones y pantalla de acceso adaptados al curso.

```bash
sudo ./scripts/customize-theme.sh
```

## Actualización segura

El script hace una copia previa, reconstruye las imágenes usando la versión corregida más reciente de la rama Moodle 4.5 LTS, ejecuta la actualización de base de datos y limpia cachés:

```bash
set -a; . ./.env; set +a
./scripts/update.sh
./scripts/smoke-test.sh
```

Antes de una actualización importante se debe leer la documentación oficial, probar la copia en otro entorno y revisar la compatibilidad de complementos.

## Subida a GitHub

Inicializa un repositorio propio dentro de esta carpeta (no incluyas `.env` ni las copias), realiza un commit y crea un repositorio vacío en GitHub. Después añade su dirección como remoto y sube la rama principal. Incluye en la entrega el enlace del repositorio y la memoria completada.

## Estructura

```text
compose.yaml            Servicios Moodle, MariaDB y cron
Dockerfile              Moodle 4.5 LTS sobre PHP/Apache
docker/                 Configuración y arranque automático
data/usuarios.csv       Usuarios y roles que se matriculan
scripts/provision.php   Curso, matrículas y actividades
scripts/backup.sh       Copia de base de datos y ficheros
scripts/update.sh       Actualización con copia previa
scripts/smoke-test.sh   Pruebas automáticas de funcionamiento
scripts/customize-theme.sh Personalización visual del tema Boost
docs/MEMORIA.md         Memoria y evidencias de la práctica
```

## Referencias

- Material de la unidad: <https://github.com/franlu/AplicacionesWeb/tree/master/Unidad02>
- Documentación de instalación de Moodle: <https://docs.moodle.org/405/en/Installing_Moodle>
- Repositorio oficial de Moodle: <https://github.com/moodle/moodle>
- Entorno Docker oficial para desarrollo y pruebas: <https://github.com/moodlehq/moodle-docker>
