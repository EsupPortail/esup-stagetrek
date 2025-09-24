<?php
use UnicaenMail\Entity\Db\Mail;

$transportOptions = [];
if (isset($_ENV['MAIL_SMTP_HOST']) && $_ENV['MAIL_SMTP_HOST']!="") {
    $transportOptions['host'] = $_ENV['MAIL_SMTP_HOST'];
}

if (isset($_ENV['MAIL_SMTP_PORT']) && $_ENV['MAIL_SMTP_PORT']!="") {
    $transportOptions['port'] = intval($_ENV['MAIL_SMTP_PORT']);
}


if (isset($_ENV['MAIL_SMTP_USE_AUTH']) && $_ENV['MAIL_SMTP_USE_AUTH']!="") {
    $transportOptions['connection_config']['use_auth'] =  (isset($_ENV['MAIL_SMTP_USE_AUTH']) && $_ENV['MAIL_SMTP_USE_AUTH']=="true");
}
if (isset($_ENV['MAIL_SMTP_USERNAME']) && $_ENV['MAIL_SMTP_USERNAME']!="") {
    $transportOptions['connection_config']['username'] = $_ENV['MAIL_SMTP_USERNAME'];
}
if (isset($_ENV['MAIL_SMTP_PASSWORD']) && $_ENV['MAIL_SMTP_PASSWORD']!="") {
    $transportOptions['connection_config']['password'] = $_ENV['MAIL_SMTP_PASSWORD'];
}
$transportOptions['tls'] =  (isset($_ENV['MAIL_SMTP_TLS']) && ($_ENV['MAIL_SMTP_TLS']=="true")) ? true : false;

return [
    'unicaen-mail' => [
        /**
         * Classe de entité
         **/
        'mail_entity_class' => Mail::class,
        /**
         * Options concernant l'envoi de mail par l'application
         */
        'transport_options' =>  $transportOptions,
        /**
         * Configuration de l'expéditeur
         */

        'module' => [
            'default' => [
                //!!! si do_not_send est a false, il n'y a pas de redirection dans tout les cas
                'do_not_send' => (isset($_ENV['MAIL_DO_NOT_SEND']) && $_ENV['MAIL_DO_NOT_SEND']=="true") ? true : false,
                'redirect' => (isset($_ENV['MAIL_REDIRECT']) && $_ENV['MAIL_REDIRECT']=="true") ? true : false,
                'redirect_to' => [($_ENV['MAIL_REDIRECT_TO']) ?? null],
                'subject_prefix' => ($_ENV['MAIL_SUBJECT_PREFIX']) ?? "StageTrek", // Donne [StageTrek] Sujet du mail
                'from_name' => ($_ENV['MAIL_FROM_NAME']) ?? "StageTrek",
                'from_email' => ($_ENV['MAIL_FROM_EMAIL']) ?? "ne-pas-repondre",
            ],
        ]
    ],
];