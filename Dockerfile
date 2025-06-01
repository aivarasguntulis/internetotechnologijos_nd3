FROM php:8.2-apache

# Įdiegiam SSL library ir kitus reikalingus paketus
RUN apt-get update \
    && apt-get install -y libssl-dev pkg-config gnupg2 curl vim iputils-ping net-tools dnsutils \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# (Papildomai: įdėk čia, jei reikia daugiau ext, pvz. mysqli)
# RUN docker-php-ext-install mysqli
