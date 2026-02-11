<?php

namespace BddAdmin;

use BddAdmin\Migration\Factory\Migration_20251031_ContactStageFactory;
use BddAdmin\Migration\Migration_20250717_Faq;
use BddAdmin\Migration\Migration_20250917_Annee;
use BddAdmin\Migration\Migration_20250917_Etudiant;
use BddAdmin\Migration\Migration_20250917_Groupe;
use BddAdmin\Migration\Migration_20250924_Annee;
use BddAdmin\Migration\Migration_20250924_Etudiant;
use BddAdmin\Migration\Migration_20251030_Session;
use BddAdmin\Migration\Migration_20251031_ContactStage;

return [
    'unicaen-bddadmin' => [
        'migration' => [
            Migration_20250717_Faq::class,
            Migration_20250917_Annee::class,
            Migration_20250917_Etudiant::class,
            Migration_20250917_Groupe::class,
            Migration_20250924_Annee::class,
            Migration_20250924_Etudiant::class,
            Migration_20251030_Session::class,
            Migration_20251031_ContactStage::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Migration_20251031_ContactStage::class => Migration_20251031_ContactStageFactory::class,
        ],
        'invokables' => [
//            Scripts de migrations
            Migration_20250717_Faq::class => Migration_20250717_Faq::class,
            Migration_20250917_Annee::class => Migration_20250917_Annee::class,
            Migration_20250917_Etudiant::class => Migration_20250917_Etudiant::class,
            Migration_20250917_Groupe::class => Migration_20250917_Groupe::class,
            Migration_20250924_Annee::class => Migration_20250924_Annee::class,
            Migration_20250924_Etudiant::class => Migration_20250924_Etudiant::class,
            Migration_20251030_Session::class => Migration_20251030_Session::class,
        ]
    ],
];