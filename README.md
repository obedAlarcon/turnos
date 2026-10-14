# Sistema de Control de Turnos

Aplicación web desarrollada en PHP para la administración y gestión de turnos.

## Tecnologías

- PHP 8.2
- Apache
- MySQL / MariaDB
- HTML, CSS y JavaScript
- Docker para el despliegue en Render

## Funcionalidades

- Inicio y cierre de sesión.
- Gestión de usuarios y roles.
- Administración de turnos.
- Gestión de sedes.
- Acceso según los permisos del usuario.

## Requisitos para desarrollo local

- XAMPP o un entorno compatible con PHP y Apache.
- MySQL o MariaDB.
- Git.

## Instalación local

1. Clonar el repositorio:

   ```bash
   git clone URL_DEL_REPOSITORIO
   ```

2. Copiar el proyecto a la carpeta `htdocs` de XAMPP.

3. Iniciar Apache y MySQL desde el panel de XAMPP.

4. Crear o importar la base de datos `turnos` en phpMyAdmin.

5. Configurar las credenciales de conexión a la base de datos en el archivo de configuración correspondiente.

6. Abrir la aplicación en el navegador:

   `http://localhost/turnos/`

## Despliegue en Render

La aplicación se prepara para ejecutarse en Render utilizando Docker, PHP y Apache.

1. Guardar el archivo `Dockerfile` en la raíz del repositorio.
2. Conectar el repositorio de GitHub con Render.
3. Crear un servicio web seleccionando el entorno Docker.
4. Configurar la base de datos y las variables de entorno necesarias.
5. Desplegar y verificar el funcionamiento de la aplicación.

## Configuración de seguridad

- No subir contraseñas ni credenciales al repositorio.
- Utilizar variables de entorno para los secretos cuando la aplicación esté preparada para leerlas.
- Configurar la conexión a la base de datos para el entorno de producción.
- Verificar los permisos y los roles de usuario después del despliegue.

## Estado del proyecto

En preparación para el despliegue en Render.

## Autor

Obed Albeiro Alarcón
