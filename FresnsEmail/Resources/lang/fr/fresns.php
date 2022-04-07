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

    'name' => 'Fresns Email Plugin',
    'description' => 'Le plugin officiel de Fresns mail pour l\'envoi par SMTP.',

    'menuConfig' => 'Configuration du courrier',
    'menuTest' => 'Test d\'envoi',
    'menuVariable' => 'Noms de variables pris en charge',

    'smtpHost' => 'Hôte SMTP',
    'smtpHostIntro' => 'Une configuration incorrecte de la distribution du courrier entraînera une perte de temps pour le serveur.',
    'smtpPort' => 'Port SMTP',
    'smtpPortIntro' => 'Le groupe de sécurité de votre serveur "Public Outbound" doit être ouvert pour ce port TCP.',
    'smtpUser' => 'Nom d\'utilisateur SMTP',
    'smtpUserIntro' => 'Remplissez votre nom d\'utilisateur complet',
    'smtpPassword' => 'Mot de passe SMTP',
    'smtpPasswordIntro' => 'Remplissez votre mot de passe ou votre code d\'autorisation',
    'smtpVerifyType' => 'Type de vérification SMTP',
    'smtpVerifyTypeIntro' => 'Sélectionnez la méthode d\'authentification du serveur de messagerie',
    'smtpFromMail' => 'Email de l\'expéditeur',
    'smtpFromMailIntro' => 'Adresse électronique pour l\'envoi de l\'identité',
    'smtpFromName' => 'Nom de l\'expéditeur',
    'smtpFromNameIntro' => 'Nom de la marque ou de la société',
    'settingButton' => 'Enregistrer',

    'testMailDesc' => 'Après avoir enregistré la configuration de la boîte aux lettres, saisissez la boîte aux lettres entrante ici et testez l\'envoi.',
    'testMailSend' => 'Confirmer l\'envoi',

    'variableCode' => 'Code de vérification',
    'variableTime' => 'Heure d\'envoi',
];
