FROM php:8.2-apache

# Instala extensiones comunes usadas por apps PHP (ajusta según tus necesidades)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev zip unzip libpng-dev libonig-dev libicu-dev git \
    && docker-php-ext-install pdo_mysql mbstring intl zip gd \
    && rm -rf /var/lib/apt/lists/*

# Habilitar módulos útiles de Apache
RUN a2enmod rewrite headers

# Document root por defecto; `docker-compose.yml` sobreescribe vía environment
ENV APACHE_DOCUMENT_ROOT=/var/www/milipet/public

# Actualiza la configuración de Apache para usar APACHE_DOCUMENT_ROOT
RUN sed -ri "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
 && sed -ri "s!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}>!g" /etc/apache2/apache2.conf || true

WORKDIR /var/www/milipet

# No copiamos los archivos durante el build para facilitar desarrollo con volúmenes;
# docker-compose montará el proyecto desde el host.

# Ajuste de permisos por si acaso (no falla si no existe aún)
RUN chown -R www-data:www-data /var/www/milipet || true

EXPOSE 80

CMD ["apache2-foreground"]
