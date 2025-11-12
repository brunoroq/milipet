# Debug y Entornos

## APP_ENV
- Usa la variable de entorno `APP_ENV` (`dev` por defecto). Valores sugeridos: `dev`, `prod`.
- En `dev`: se muestran errores y trazas; la ruta `/ ?r=health` está disponible.
- En `prod`: se ocultan errores al usuario y se muestra una página 500 amigable.

Configura en `.env` o en el entorno del contenedor:

```
APP_ENV=prod
```

## Ruta de salud (solo dev)
`/?r=health` intenta conectar a la base de datos y ejecutar `SELECT 1`.
Muestra `DB: OK` o `DB: FAIL - <mensaje>`.

## Manejo global de errores
- Todas las excepciones y errores no capturados se registran con `error_log`.
- En `prod`, se responde con `app/views/static/500.php` sin detalles internos.
