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

    'name' => 'Fresns E-Mail-Plugin',
    'description' => 'Das offizielle Fresns-Mail-Plugin für den SMTP-Versand.',

    'menuConfig' => 'Mail-Konfiguration',
    'menuTest' => 'Test Senden',

    'smtpHost' => 'SMTP-Host',
    'smtpHostIntro' => 'Eine falsch konfigurierte E-Mail-Zustellung führt zu einer Zeitüberschreitung des Servers',
    'smtpPort' => 'SMTP-Anschluss',
    'smtpPortIntro' => 'Ihre Server-Sicherheitsgruppe "Public Outbound" muss für diesen TCP-Port geöffnet sein',
    'smtpUser' => 'SMTP-Benutzername',
    'smtpUserIntro' => 'Geben Sie Ihren vollständigen Benutzernamen ein',
    'smtpPassword' => 'SMTP-Kennwort',
    'smtpPasswordIntro' => 'Geben Sie Ihr Passwort oder Ihren Autorisierungscode ein',
    'smtpVerifyType' => 'SMTP-Überprüfungs-Typ',
    'smtpVerifyTypeIntro' => 'Wählen Sie die Authentifizierungsmethode des Mailservers',
    'smtpFromMail' => 'Absender E-Mail',
    'smtpFromMailIntro' => 'E-Mail-Adresse für die Übermittlung der Identität',
    'smtpFromName' => 'Name des Absenders',
    'smtpFromNameIntro' => 'Markenname oder Firmenname',
    'settingButton' => 'Speichern',

    'testMailDesc' => 'Nach dem Speichern der Mailbox-Konfiguration geben Sie hier die Eingangs-Mailbox ein und testen den Versand.',
    'testMailSend' => 'Bestätigen Sie Senden',
];
