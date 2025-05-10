FROM php:8.3-fpm

# Instala extensões do PHP necessárias
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Node.js 22.x
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

# Diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto
COPY . .

# Instala dependências
RUN composer install && npm install

# Build inicial dos assets (útil para produção ou SSR)
RUN rm -rf public/build && npm run build

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
