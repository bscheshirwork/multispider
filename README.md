# multispider
Тестовое задание и возможность изучить Docker


###[Текст задания](/docs/taskinfo.md) 
###[Дополнительное заданиe](/docs/additionaltastk.md)

Данный репозиторий наполнялся по мере того, как у меня появлялось время для выполнения тестового задания. 

План разработки | todo check
--- | --- 
Добыть технику для изучения Docker, установки операционки для работы, среды разработки, прочего такого | &#9745;
Изучить Docker в достаточной мере для оформления в виде контейнера / композиции | &#9745;
Установить необходимые инструменты, среду разработки | &#9745;
Создать контейнеры для кода, подружить с средой разработки | &#9745;
Написать код, отладить | &#9745;

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
```

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


##Заметки по коду (кратко)

Что сделано: 

Первичный функционал был добавлен - работа с потоками, удаление посредством вызова rm.


Обновлена работа с логом - добавлен сторонний механизм.


Добавлена база для хранения таблицы "очереди" между вызовами. Изменена логика вызовов.


Что может быть улучшено (в рамках примера): Реализация удаления средствами php

##Заметки по тестированию.

Тесты для данного кода будут служить тем же целям - научится запускать их на Docker-окружении

Первое - подготовить `Codeception` для тестирования этого проекта. Так как официальный образ `codeception/codeception` 
создан на образе `php-cli`, придётся его перекомпилить.

```
git submodule add https://github.com/Codeception/Codeception.git zts-codeception/build
cd zts-codeception/build
git checkout 2.2 
sed -i -e "s/^FROM.*/FROM bscheshir\/php:7.0.12-zts/" Dockerfile
docker build -t bscheshir/codeception:zts .
```

Второе - мануал по запуску докера не соответствует образу. Используем бинд на папку проекта.
```
  codecept:
    image: bscheshir/codeception:zts
    depends_on:
      - php
    environment:
      XDEBUG_CONFIG: "remote_host=192.168.88.241 remote_port=9005 remote_enable=On"
      PHP_IDE_CONFIG: "serverName=codeception"
    volumes:
#      - ../src:/src
#      - ../tests:/tests
#      - ./codeception.yml:/codeception.yml
      - ..:/project
```

Запускаем сервис `codecept`
```
~/projects/multispider/zts-xdebug$ docker-compose run --rm --entrypoint bash codecept
root@e870b32bc227:/project# codecept bootstrap
```


Третье - для использования плюшек автодополнения и, главное, чтобы IDE не ругалась на неизвестные классы, от которых
наследуется актёр, можно извлечь из образа исходный код фреймворка тестирования и зависимостей.
Если codeception уже является подмодулем проекта, то его классы IDE должна подхватить. Однако, зависимости, 
загруженные при билде образа по прежнему будут скрыты от IDE.

Копируем, например, в `test/.repo` 
```
bogdan@bogdan-php:~/projects/multispider$ rm -R tests/.repo
bogdan@bogdan-php:~/projects/multispider$ mkdir -p tests/.repo/vendor

root@e870b32bc227:/project# cp -R /repo/vendor/* /project/tests/.repo/vendor

bogdan@bogdan-php:~/projects/multispider$ sudo chown -R bogdan tests/
```
после генерации bootstrap классы актёров IDE найдёт в любом случае - они будут собраны из расширений, 
модулей и хелперов в `test/_supported`. 


Сгенерирвоав согласно мануалам пример теста, заполним его и выполним.
```
~/projects/multispider/zts-xdebug$ docker-compose run --rm --entrypoint bash codecept
root@e870b32bc227:/project# codecept run unit
```

Полезная фишка docker'a - вместо того, чтобы создавать отдельные конфиги для соединения с тестовой базой и изменять файл
точки входи по типу `config.codeception.php` и объячлять переменные окружения
```
return [
    'threads' => 2,
    'multiplier' => 1,
    'db' => [
        'connectionString' => 'pgsql:host=db;port=5432;dbname=multispider_test',
        'user' => 'multispider_test',
        'password' => 'multispider_test',
    ],
];
```
`src/entrypoint.php`
```
...
if (file_exists($configFilename = __DIR__ . '/config.' . (getenv('ENVIRONMENT') ?? '') . '.php')) {
    $options = require_once $configFilename;
} else {
    $options = require_once __DIR__ . '/config.php';
}
...
```
... Вместо этого просто заменяем место хранения файлов базы в конфигурационном файле `docker-compose.yml`, 
используемом для тестов.
```
    volumes:
      - ../.db_test:/var/lib/postgresql/data #DB-data for testing is separate
```
(тут надо проверить, что все образы корректно остановлены/удалены, а то подключит к существующему,
запущенному на боевой базе, сервису db)

> Для создания дампа был использован `pg_dump`,который не хотел ставится на новую версию базы и в итоге, к тому же,
подложил свинью в виде неподдерживаемого содержания.
```
'\Codeception\Module\Db',
"To run 'COPY' commands 'pgsql' extension should be installed"
```

### Покрытие

При "удалённом" покрытии кода после выполнения тестов 
```
codecept run --coverage --coverage-xml --coverage-html
```
Настройки веб берутся, как описано, из 
> ```
coverage:
    # url of file which includes c3 router.
    c3_url: 'http://127.0.0.1:8000/index-test.php/'
```

или "умно" из `acceptance.suite.yml` `(PhpBrowser:)url: http://localhost/myapp`. 
 
И эта же настройка подразумевает принудительный вызов `c3` после выполнения теста - для сбора информации о покрытии.
Консольное приложение, соответственно, о таком не знает, поэтому `PhpBrowser` нужно явно отключить.

Использование для запуска `Cli` не даёт информации о покрытии кода внутри запущеного приложения. Функциональные тесты
Не показывают покрытия. Использовать вместе с ними эмуляцию из юнит-теста?

Отчёты действительно удобно смотреть в браузере. `tests/_output/coverage/index.html` (PHPStorm открывает своим веб-сервером %))

HTML отчётыв разделены на группы, если нет классов и трейтов - зелёненьким весело подсвечивается соответствующие разделы...
Что внушает ложные надежды)
