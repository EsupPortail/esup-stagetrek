<?php
//Configuration des types d'événements et des services associés
use Application\Provider\Misc\EnvironnementProvider;

$simulation = false;
if(isset($_ENV['EVENEMENTS_SIMULATE_EXECUTION']) && $_ENV['EVENEMENTS_SIMULATE_EXECUTION']=="true"){
    $simulation = true;
} //Pour les cas ou la variables d'environnement n'est pas encore définie, on passera pas le type d'environnement
elseif(!isset($_ENV['EVENEMENTS_SIMULATE_EXECUTION'])){
    $env = ($_ENV['APP_ENV']) ?? EnvironnementProvider::PRODUCTION;
    $simulation = $env != EnvironnementProvider::PRODUCTION;
}

return [
    'unicaen-evenement' => [
        /** Délai maximal avant l'annulation pour faute de trop grand retard  */
        'delai-peremption' => ($_ENV['EVENEMENTS_DELAI_PEREMPTION']) ?? "P5D",
        'max_time_execution' => ($_ENV['EVENEMENTS_MAX_TIME_EXECUTION']) ?? "PT5M",
        'simulate_execution' => $simulation,
    ],
];
