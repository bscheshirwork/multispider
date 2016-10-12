# multispider
Тестовое задание и возможность изучить Docker


###[Текст задания](/docs/taskinfo.md) 

Данный репозиторий будет наполнятся по мере того, как у меня будет появлятся время для выполнения тестового задания. К сожалению, первые пару дней обещает быть пустым.

План разработки | todo check
--- | --- 
Добыть технику для изучения Docker, установки операционки для работы, среды разработки, прочего такого | &#9745;
Изучить Docker в достаточной мере для оформления в виде контейнера / композиции | &#9745;
Установить необходимые инструменты, среду разработки | &hellip;
Создать контейнеры для кода, подружить с средой разработки | &hellip;
Написать код, отладить | 

Планирую использовать источники информации: 
* [Про пранировщик на yield](https://habrahabr.ru/post/164173/)
* [Про многопоточность](https://habrahabr.ru/post/300952/)

Продолжаю использовать md как блог какой-то. Забавно.

[Наобщавшись](https://github.com/docker/docker.github.io/issues/created_by/BSCheshir) [вдоволь](https://github.com/docker/docker.github.io/pulls?utf8=%E2%9C%93&q=is%3Apr%20author%3Abscheshir) с составителями документации по Docker'у  решил занятся установкой среды. Для cli паука достаточно будет одного контейнера с php, однако нужно будет добавить парочку расширений.

После недолгого поиска пакетов вне докера с прицелом добавить в образ - нашёл [готовые образы](https://hub.docker.com/_/php/) на [гитхабе](https://github.com/docker-library/docs/tree/master/php) в том числе и нужной сборки. И очень свежие. Ура.

Попробовал установить pthreads, упомянутый в статье про многопоточность на php:7.1-zts-alpine

Dockerfile получился такой
```
FROM php:7.1-zts-alpine
MAINTAINER BSCheshir <bscheshir.work@gmail.com>
ENV XDEBUG_VERSION 2.4.1
RUN apk --update --no-progress add build-base autoconf git && rm -rf /var/cache/apk/*

#https://github.com/krakjoe/pthreads/archive/master.zip

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
```

Образ раздулся в 5 раз.
```
REPOSITORY           TAG                 IMAGE ID            CREATED             SIZE
my-test-php-alpine   1                   697c02574fc9        48 minutes ago      271.6 MB
php                  7.1-zts-alpine      c2231924b0cc        11 days ago         63.86 MB
```s

К тому же, как выяснилось, последняя версия xdebug не работает с самой свежей версией php, представленной в этой сборке.
Также смущают ошибки компиляции, видно базового набора не хватило. Печально. Пока что оставим, но запомним.
Следующая проба - на 7.0.11 образе  
```
FROM php:7.0.11-zts-alpine
MAINTAINER BSCheshir <bscheshir.work@gmail.com>
ENV XDEBUG_VERSION 2.4.1
RUN apk --update --no-progress add build-base autoconf git && rm -rf /var/cache/apk/*

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

RUN curl -fsSL "http://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz" -o xdebug-$XDEBUG_VERSION.tar.gz \
    && mkdir -p xdebug-$XDEBUG_VERSION \
    && tar -xf xdebug-$XDEBUG_VERSION.tar.gz -C xdebug-$XDEBUG_VERSION --strip-components=1 \
    && rm xdebug-$XDEBUG_VERSION.tar.gz \
    && ( \
        cd xdebug-$XDEBUG_VERSION \
        && phpize \
        && ./configure --enable-xdebug \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug
```
Сравнение. Версии 2 и 2.1 отличаются тем установлени или нет xdebug (установлен в 2.1)
```
REPOSITORY           TAG                 IMAGE ID            CREATED             SIZE
my-test-php-alpine   2.1                 e4a65b6b3aaf        36 seconds ago      266.3 MB
my-test-php-alpine   2                   c0b9d98b5e59        3 minutes ago       265.2 MB
my-test-php-alpine   1                   697c02574fc9        48 minutes ago      271.6 MB
php                  7.1-zts-alpine      c2231924b0cc        11 days ago         63.86 MB
```
Для проверки возьмём образ соседней сборки.
```
FROM php:7.0.11-zts
MAINTAINER BSCheshir <bscheshir.work@gmail.com>
ENV XDEBUG_VERSION 2.4.1
RUN apt-get update && apt-get install -y git && apt-get clean

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

RUN curl -fsSL "http://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz" -o xdebug-$XDEBUG_VERSION.tar.gz \
    && mkdir -p xdebug-$XDEBUG_VERSION \
    && tar -xf xdebug-$XDEBUG_VERSION.tar.gz -C xdebug-$XDEBUG_VERSION --strip-components=1 \
    && rm xdebug-$XDEBUG_VERSION.tar.gz \
    && ( \
        cd xdebug-$XDEBUG_VERSION \
        && phpize \
        && ./configure --enable-xdebug \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable xdebug
```

Предупреждений компиляции не видно. красный цвет в консоли перестал часто мелькать. Уже в плюс. Что же по месту?
```
REPOSITORY           TAG                 IMAGE ID            CREATED             SIZE
my-test-php-debian   3                   bd116223d45b        4 minutes ago       407.3 MB
my-test-php-alpine   2.1                 e4a65b6b3aaf        18 minutes ago      266.3 MB
my-test-php-alpine   2                   c0b9d98b5e59        21 minutes ago      265.2 MB
my-test-php-alpine   1                   697c02574fc9        48 minutes ago      271.6 MB
php                  7.1-zts-alpine      c2231924b0cc        11 days ago         63.86 MB
```

Размер побольше, в полтора раза, если честно. С другой стороны дисковая память дёшева, а какой-нибудь php-фреймворк сам столько занимает. Можно попробовать перекомпилить без ошибок, добавив упоминаемые пакеты... nproc (coreutils) re2c... И... Нет. Те же предупреждения. Насколько критично то, что компилятор нашёл "неверно" подключённые файлы? Пока что буду использовать как базу debian, ему же в плюс наличие банального /bin/bash.
