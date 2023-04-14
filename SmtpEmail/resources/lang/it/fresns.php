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

    'name' => 'Plugin Fresns Email',
    'description' => 'Il plugin ufficiale di Fresns mail per l\'invio SMTP.',

    'menuConfig' => 'Configurazione della posta',
    'menuTest' => 'Test di invio',
    'menuVariable' => 'Nomi di variabili supportati',

    'smtpHost' => 'Host SMTP',
    'smtpHostIntro' => 'La consegna della posta configurata in modo non corretto causerà il time out del server',
    'smtpPort' => 'Porta SMTP',
    'smtpPortIntro' => 'Il vostro gruppo di sicurezza del server "Public Outbound" deve essere aperto per questa porta TCP',
    'smtpUser' => 'Nome utente SMTP',
    'smtpUserIntro' => 'Inserisci il tuo nome utente completo',
    'smtpPassword' => 'Password SMTP',
    'smtpPasswordIntro' => 'Inserisci la tua password o il codice di autorizzazione',
    'smtpVerifyType' => 'Tipo di verifica SMTP',
    'smtpVerifyTypeIntro' => 'Selezionare il metodo di autenticazione del server di posta',
    'smtpFromMail' => 'Email del mittente',
    'smtpFromMailIntro' => 'Indirizzo e-mail per l\'invio dell\'identità',
    'smtpFromName' => 'Nome del mittente',
    'smtpFromNameIntro' => 'Nome del marchio o della società',
    'settingButton' => 'Salva',

    'testMailDesc' => 'Dopo aver salvato la configurazione della mailbox, inserite qui la mailbox in entrata e provate l\'invio.',
    'testMailSend' => 'Conferma l\'invio',

    'variableCode' => 'Verificare il codice',
    'variableTime' => 'Invia tempo',
];
