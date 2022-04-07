<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Plugin de Email Fresns',
    'description' => 'O plugin oficial de correio Fresns para envio de SMTP.',

    'menuConfig' => 'Configuração do correio',
    'menuTest' => 'Envio de testes',
    'menuVariable' => 'Nomes de variáveis suportadas',

    'smtpHost' => 'Hospedeiro SMTP',
    'smtpHostIntro' => 'A entrega de correio configurado incorrectamente fará com que o servidor fique sem tempo',
    'smtpPort' => 'Porto SMTP',
    'smtpPortIntro' => 'O seu grupo de segurança de servidor "Public Outbound" precisa de estar aberto para esta porta TCP',
    'smtpUser' => 'Nome de utilizador SMTP',
    'smtpUserIntro' => 'Preencha o seu nome de utilizador completo',
    'smtpPassword' => 'Senha SMTP',
    'smtpPasswordIntro' => 'Preencha a sua palavra-passe ou código de autorização',
    'smtpVerifyType' => 'Tipo de Verificação SMTP',
    'smtpVerifyTypeIntro' => 'Seleccionar o método de autenticação do servidor de correio',
    'smtpFromMail' => 'E-mail do remetente',
    'smtpFromMailIntro' => 'Endereço de e-mail para envio de identidade',
    'smtpFromName' => 'Nome do remetente',
    'smtpFromNameIntro' => 'Nome da marca ou nome da empresa',
    'settingButton' => 'Guardar',

    'testMailDesc' => 'Depois de guardar a configuração da caixa de correio, introduzir aqui a caixa de correio de entrada e testar o envio.',
    'testMailSend' => 'Confirmar Enviar',

    'variableCode' => 'Verificar o Código',
    'variableTime' => 'Tempo de envio',
];
