# Uso de Docker para desarrollo (Windows y Arch Linux)

Esta repo incluye una configuración mínima para desarrollo con Docker.

Requisitos
- Docker Engine (o Docker Desktop en Windows con WSL2)
- docker-compose (v2 integrada en el cliente `docker compose`)

Inicio rápido
1. Copia el ejemplo de variables de entorno y edítalo si hace falta:

```bash
cp .env.example .env
# Ajusta puertos/credenciales si lo necesitas
```

2. Levanta los servicios (desde la raíz del repo):

```bash
docker compose up --build -d
```

3. Importa la base de datos si aún no está en la imagen/volumen:
- phpMyAdmin: http://localhost:8081 (usa las credenciales definidas en `.env`)
- o desde host: `mysql -h 127.0.0.1 -P 3306 -u root -p` (puerto según `.env`)

4. Abre la aplicación en el navegador:

- Público: http://localhost:8080/
- Admin: http://localhost:8080/?r=auth/admin_login

Notas
- Para desarrollo, el repo se monta como volumen dentro del contenedor en `/var/www/milipet` y
  Apache apunta a `/var/www/milipet/public`.
- En Windows con Docker Desktop + WSL2, usa WSL para editar archivos y evita problemas de permisos.
- En Linux (Arch), si los archivos creados por contenedor se muestran con UID root, puedes
  ajustar `HOST_UID`/`HOST_GID` en tu `.env` y descomentar la opción `user:` en `docker-compose.yml`.

Si quieres que implemente una versión optimizada para producción (separando build/context,
cache de Composer, o usando nginx+php-fpm), dime y lo preparo.
