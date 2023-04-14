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

    'name' => 'Fresns Plugin de correo electrónico',
    'description' => 'El plugin oficial de Fresns mail para el envío SMTP.',

    'menuConfig' => 'Configuración de correo',
    'menuTest' => 'Prueba de envío',
    'menuVariable' => 'Nombres de variables admitidos',

    'smtpHost' => 'Host SMTP',
    'smtpHostIntro' => 'Una configuración incorrecta de la entrega de correo hará que el servidor se quede sin tiempo de espera',
    'smtpPort' => 'Puerto SMTP',
    'smtpPortIntro' => 'El grupo de seguridad de su servidor "Public Outbound" debe estar abierto para este puerto TCP',
    'smtpUser' => 'Nombre de usuario SMTP',
    'smtpUserIntro' => 'Escriba su nombre de usuario completo',
    'smtpPassword' => 'Contraseña SMTP',
    'smtpPasswordIntro' => 'Introduzca su contraseña o código de autorización',
    'smtpVerifyType' => 'Tipo de verificación SMTP',
    'smtpVerifyTypeIntro' => 'Seleccione el método de autenticación del servidor de correo',
    'smtpFromMail' => 'Correo electrónico del remitente',
    'smtpFromMailIntro' => 'Dirección de correo electrónico para el envío de la identidad',
    'smtpFromName' => 'Nombre del remitente',
    'smtpFromNameIntro' => 'Marca o nombre de la empresa',
    'settingButton' => 'Guardar',

    'testMailDesc' => 'Después de guardar la configuración del buzón, introduzca aquí el buzón de entrada y pruebe el envío.',
    'testMailSend' => 'Confirmar envío',

    'variableCode' => 'Código de verificación',
    'variableTime' => 'Hora de envío',
];
