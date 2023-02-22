# Yii2 logging

Моніторить лог файл ngix та при знаходженні нових даних зберігає їх в базу даних

## Install

Добавити запис в composer.json проекта в секції require та repositories наступні записи:

```json

{
  "require": {
    "aigletter/yii2-lib": "dev-master"
  },
  "repositories": [
    {
      "type": "git",
      "url": ""
    }
  ]
}
```

Добавити в конфіг додатку в секцію modules наступний запис:

```php
[
    'modules' => [
        'logging' => [
            'class' => \aigletter\logging\Module::class,
        ],
    ],
]
```

## Configuration

**defaultLogFile**

Файл, котрий буде моніторитись без вказання шляху при запуску команди

По замовчуванню: _/var/log/nginx/access.log_

**logFormat**

По замовчуванні використовується дефолтний формат ngix

`%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"`

**processType**

Доступно 2 режими 

* single - режим читання і запису в базу построково
* batch - пакетний режим читання і запису в базу

По замовчуванню: single

Доступні константи классу aigletter\logging\components\Logging відповідно:

* Logging::PROCESS_MODE_SINGLE
* Logging::PROCESS_MODE_BATCH 

**batchSize**

Розмір пакета даних при використанні режиму batch

По замовчуванню: 1000

Перевизначити значення циї параметрів можна в конфігурації додатку, наприклад:

```php
[
    'modules' => [
        'logging' => [
            'class' => \aigletter\logging\Module::class,
            // ... настройки модуля ...
            'params' => [
                'defaultLogFile' => '/home/user/app/logs/access.log',
                'logFormat' => '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"',
                'processMode' => \aigletter\logging\components\Logging::PROCESS_MODE_BATCH,
                'batchSize' => 200
            ],
        ],
    ],
]
```

## Usage

Доступно 3 команди:

1. Команда моніторингу і збереження даних в базу
    
Команда може приймати параметр --logFile.
Якщо параметр не переданий, буде моніторитись файл по замовчуванню

Приклад запуску:
```
php yii logging/monitor
```
Або з параметром:
```
php yii logging/monitor /home/user/app/logs/access.log
```

1. Команда пошуку і виводу логів по датах.

Ця команда вимагає 2 аргумента startDate та finishDate. Формат дати будь який допустимий в php

```
php yii logging/monitor/find '2023-01-01 10:22:45' 2023-02-28
```

3. Команда підрахунку записів за відповідний проміжок часу

Ця команда вимагає 2 аргумента startDate та finishDate. Формат дати будь який допустимий в php

```
php yii logging/monitor/find 01.01.2023 28.02.2023
```