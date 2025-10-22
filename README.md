# MiliPet — con subida de imágenes
Cambios clave:
- Form admin con `enctype="multipart/form-data"`
- Campo `image_file` para JPG/PNG (máx 3MB)
- Controlador mueve el archivo a `public/assets/img/` y guarda URL
- Al editar, si el archivo nuevo es local y el anterior también era local, se elimina el anterior
- Campo `species` agregado (opcional) para filtrar por especie

Despliegue en XAMPP (Windows):
1) Crear BD `milipet_db` y `Importar` database/schema.sql
2) Copiar carpeta a `C:\xampp\htdocs\milipet_site`
3) Ajustar `config/config.php` si no usas root sin clave
4) Abrir `http://localhost/milipet_site/public`
