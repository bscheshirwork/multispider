FROM php:7.0.12-zts

MAINTAINER BSCheshir <bscheshir.work@gmail.com>

RUN apt-get update && apt-get install -y \
        coreutils \
        re2c \
        autoconf \
        git \
        --no-install-recommends \
  && rm -r /var/lib/apt/lists/* \
  && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false

RUN git clone https://github.com/krakjoe/pthreads.git \
    && ( \
        cd pthreads \
        && phpize \
        && ./configure --enable-pthreads \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r pthreads \
    && docker-php-ext-enable pthreads

