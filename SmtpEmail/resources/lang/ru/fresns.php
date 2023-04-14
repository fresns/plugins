<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Fresns Email Plugin',
    'description' => 'Официальный почтовый плагин Fresns для отправки по SMTP.',

    'menuConfig' => 'Конфигурация почты',
    'menuTest' => 'Тестовая отправка',
    'menuVariable' => 'Поддерживаемые имена переменных',

    'smtpHost' => 'SMTP хост',
    'smtpHostIntro' => 'Неправильно настроенная доставка почты приведет к тайм-ауту сервера',
    'smtpPort' => 'SMTP порт',
    'smtpPortIntro' => 'Группа безопасности вашего сервера "Public Outbound" должна быть открыта для этого TCP-порта.',
    'smtpUser' => 'Имя пользователя SMTP',
    'smtpUserIntro' => 'Введите свое полное имя пользователя',
    'smtpPassword' => 'SMTP Пароль',
    'smtpPasswordIntro' => 'Введите свой пароль или код авторизации',
    'smtpVerifyType' => 'Тип проверки SMTP',
    'smtpVerifyTypeIntro' => 'Выберите метод аутентификации почтового сервера',
    'smtpFromMail' => 'Электронная почта отправителя',
    'smtpFromMailIntro' => 'Адрес электронной почты для отправки идентификационных данных',
    'smtpFromName' => 'Имя отправителя',
    'smtpFromNameIntro' => 'Название бренда или название компании',
    'settingButton' => 'Сохранить',

    'testMailDesc' => 'После сохранения конфигурации почтового ящика введите здесь входящий почтовый ящик и протестируйте отправку.',
    'testMailSend' => 'Подтвердить отправку',

    'variableCode' => 'Код проверки',
    'variableTime' => 'Время отправки',
];
