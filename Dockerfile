# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar módulo de Apache para rewrite
RUN a2enmod rewrite

# Configurar php.conf para manejar archivos PHP correctamente
RUN echo '<FilesMatch ".+\.ph(ar|p|tml)$">\n\
    SetHandler application/x-httpd-php\n\
</FilesMatch>\n\
<FilesMatch ".+\.phps$">\n\
    SetHandler application/x-httpd-php-source\n\
    Require all denied\n\
</FilesMatch>\n\
<FilesMatch "^\.ph(ar|p|ps|tml)$">\n\
    Require all denied\n\
</FilesMatch>' > /etc/apache2/mods-available/php.conf \
    && ln -sf /etc/apache2/mods-available/php.conf /etc/apache2/mods-enabled/php.conf

# Configurar Apache para permitir .htaccess
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Configurar zona horaria
RUN echo "date.timezone = Europe/Madrid" > /usr/local/etc/php/conf.d/timezone.ini

# Configurar errores para desarrollo
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/custom.ini

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html/

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer puerto 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
