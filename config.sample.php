<?php

return [
    'defaults' => [
        'activity_id' => 9,
        'issue_ids' => [
            // Issue Id => Issue Label
            1000 => 'Issue#Id – Issue Label Exemple',
        ],
    ],
    'redmine' => [
        'api_url' => 'https://redmine.example.com',
        'username' => '',
        'password' => '',
        'user_id' => null,
        'activities' => [
            11 => 'Analyse',
            19 => 'Déploiement',
            9 => 'Développement',
            31 => 'Documentation',
            76 => 'Gestion de projet',
            25 => 'Réunion',
            30 => 'Revue de code',
            29 => 'Support',
        ],
    ],
];
