<?php
//Configuration des types d'événements et des services associés

return [
    'unicaen-evenement' => [
        'max_time_execution' => (isset($_ENV['EVENEMENTS_MAX_TIME_EXECUTION'])) ? intval($_ENV['EVENEMENTS_MAX_TIME_EXECUTION']) : 300,
    ],
];