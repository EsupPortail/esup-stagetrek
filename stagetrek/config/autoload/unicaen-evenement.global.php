<?php
//Configuration des types d'événements et des services associés


return [
    'unicaen-evenement' => [
        /** Délai maximal avant l'annulation pour faute de trop grand retard  */
        'delai-peremption' => ($_ENV['EVENEMENTS_DELAI_PEREMPTION']) ?? "P5D",
        'max_time_execution' => ($_ENV['EVENEMENTS_MAX_TIME_EXECUTION']) ?? "PT5M",
    ],
];
