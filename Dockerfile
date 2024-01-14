# Utilisez une image officielle de PHP en tant que base
FROM php:8.2-fpm

# Installez les dépendances nécessaires (par exemple, Composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip

# Configurez PHP et installez Composer
RUN docker-php-ext-install pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    
# Définissez le répertoire de travail
WORKDIR /var/www/html

# Copiez les fichiers de votre application Symfony dans le conteneur
COPY . .

# Installez les dépendances de Symfony
RUN composer install --no-scripts

# Exposez le port 9000 (ou tout autre port utilisé par votre serveur PHP-FPM)
EXPOSE 8000

# Commande par défaut pour démarrer le serveur PHP-FPM
CMD ["php-fpm"]
