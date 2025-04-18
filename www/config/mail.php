<?php
// Configuration pour l'envoi d'emails via Mailtrap (service de test)
return [
    'host' => 'sandbox.smtp.mailtrap.io',    // Serveur SMTP
    'username' => '97230c17855313',         // Identifiant Mailtrap
    'password' => 'df0281e92529ab',         // Mot de passe Mailtrap
    'port' => 2525,                         // Port SMTP
    'encryption' => null,                   // Type de chiffrement (null pour aucun)
    'from' => 'no-reply@pausewifi.com',     // Email expéditeur
    'from_name' => 'Pause WiFi'             // Nom expéditeur
];