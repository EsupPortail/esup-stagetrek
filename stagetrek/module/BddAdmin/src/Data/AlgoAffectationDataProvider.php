<?php

namespace BddAdmin\Data;


use Application\Entity\Db\Contact;
use Application\Service\Affectation\Algorithmes\AlgoAleatoire;
use Application\Service\Affectation\Algorithmes\AlgoScoreV1;
use Application\Service\Affectation\Algorithmes\AlgoScoreV2;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class AlgoAffectationDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'procedure_affectation' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function procedure_affectation(): array
    {
        $ordre = 1;
        return[
            [
                "code" => AlgoScoreV1::getCodeAlgo(),
                "libelle" => "Affectations par scores - v1",
                "description" => "<p><strong>Algorithme d'affectation par ordre de score - Version 1</strong></p>
                <ul>
                <li>Affecte en respectant au plus proche les préférences des étudiants, les places disponibles et l'ordre pré-défini</li>
                <li>Coût des terrains en fonction des places disponibles, du nombre de demandes, du rang de la préférence satisfaite</li>
                <li>Bonus/Malus en fonction de l'ordre d'affectation, de la demande du terrain et des places disponibles et du rang de l'étudiant</li>
                </ul>",
                "ordre" => $ordre++
            ],
            [
                "code" => AlgoScoreV2::getCodeAlgo(),
                "libelle" => "Affectations par scores - v2",
                "description" => "<p><strong>Algorithme d'affectation par ordre de score - Version 1</strong> 
                <br /><em>(V1 simplifiée)</em></p>
                <ul>
                <li>Affecte en respectant au plus proche les préférences des étudiants, les places disponibles et l'ordre pré-définie</li>
                <li>Coût des terrains en fonction des places disponibles, du nombre de demandes, du rang de la préférences satisfaite</li>
                <li>Bonus / Malus uniquement en fonction de l'ordre d'affectation</li>
                </ul>",
                "ordre" => $ordre++
            ],
            [
                "code" => AlgoAleatoire::getCodeAlgo(),
                "libelle" =>"Affectations aléatoires",
                "description" => "<p><strong>Algorithme d'affectation aléatoire</strong>
            <br /><em>(Idéal pour des tests rapide)</em></p>
            <ul>
            <li>Affectation, coût et bonus/malus aléatoire. Ignore les contraintes de places</li>
            </ul>",
                "ordre" => $ordre++
            ],
        ];
    }
}