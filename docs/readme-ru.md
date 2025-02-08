# Yii2 logging

Мониторит лог файл nginx и при находжении новых данных сохраняет их базу данных

## Install

Добавить в composer.json проекта в секции require и repositories следующиее записи:

```json

{
  "require": {
    "aigletter/yii2-lib": "dev-master"
  },
  "repositories": [
    {
      "type": "github",
      "url": "git@github.com:aigletter/yii-logging.git"
    }
  ]
}
```

Добавить в конфиг приложения в секцию modules следующую запись:

```php
[
    'modules' => [
        'logging' => [
            'class' => \aigletter\logging\Module::class,
        ],
    ],
]
```

Если для логирования используются специфические соединения (не 'db'), нужно добавить запись

```php
[
    'modules' => [
        'logging' => [
            'class' => \aigletter\logging\Module::class,
            'db' => 'specific'
        ],
    ],
]
```

## Migrations

Для применения миграций использовать команды:

~~~
php yii logging/migrate
php yii logging/migrate/down
~~~

## Configuration

**defaultLogFile**

Файл, который будет мониториться без указания пути при запуске команды

По умолчанию: _/var/log/nginx/access.log_

**logFormat**

По умолчанию испозьзуется дефолтный формат nginx

`%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"`

**processMode**

Доступно 2 режима

* single - режим чтения и записи в базу построчно
* batch - пакетный режим чтения и записи в базу

По умолчанию: batch

Доступны константы класса aigletter\logging\components\Logging соответственно:

* Logging::PROCESS_MODE_SINGLE
* Logging::PROCESS_MODE_BATCH

**batchSize**

Розмер пакета данных при использовании режима batch (количество строк)

По умолчанию: 100

Переопределить значения этих параметров можно в конфигурации приложения, например:

```php
[
    'modules' => [
        'logging' => [
            'class' => \aigletter\logging\Module::class,
            // ... настройки модуля ...
            'params' => [
                'defaultLogFile' => '/home/user/app/logs/access.log',
                'logFormat' => '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"',
                'processMode' => \aigletter\logging\components\LoggingService::PROCESS_MODE_BATCH,
                'batchSize' => 200
            ],
        ],
    ],
]
```

## Usage

Доступно 3 команды:

1. Команда мониторинга и сохранения данных в базу

Команда может принимать параметр --logFile.
Если параметр не передан, будет мониториться файл по умолчанию

Пример запуска:
```
php yii logging/monitor
```
Или с параметром:
```
php yii logging/monitor /home/user/app/logs/access.log
```

1. Команда поиска и вывода логов по датам.

Эта команда требует 2 аргумента startDate и finishDate. Формат даты любой допустимый в php

```
php yii logging/monitor/find '2023-01-01 10:22:45' 2023-02-28
```

3. Команда подсчета записев за определенный период времени

Эта команда требует 2 аргумента startDate та finishDate. Формат даты любой допустимий в php

```
php yii logging/monitor/count 01.01.2023 28.02.2023
```