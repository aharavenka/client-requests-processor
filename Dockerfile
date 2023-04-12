FROM php:8.2-cli

RUN docker-php-ext-install pcntl

WORKDIR /app

COPY . /app

CMD php app.php && tail -f log.txt
