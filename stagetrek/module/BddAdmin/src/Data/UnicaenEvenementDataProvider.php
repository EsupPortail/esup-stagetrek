<?php

namespace BddAdmin\Data;


use BddAdmin\Data\Interfaces\DataProviderInterface;
use Evenement\Provider\TypeEvenementProvider;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;
use UnicaenEvenement\Entity\Db\Etat;

class UnicaenEvenementDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_evenement_etat' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
                ];
                break;
            case 'unicaen_evenement_type' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update'  => true, 'soft-delete' => false, 'delete' => false,
                        'columns' => ['categorie_id' => ['transformer' => 'select id from unicaen_etat_categorie where code = %s'],
                    ]]
                ];
                break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_evenement_etat(): array
    {
        return [
            [
                "code" => Etat::EN_ATTENTE,
                "libelle" => "En attente",
                "description" => "Événement en attente de traitement",
            ],
            [
                "code" => Etat::EN_COURS,
                "libelle" => "En cours",
                "description" => "L'événement est en cours de traitement et sera mis à jour après celui-ci",
            ],
            [
                "code" => Etat::ECHEC,
                "libelle" => "Echoué",
                "description" => "Événement dont le traitement a échoué",
            ],
            [
                "code" => Etat::SUCCES,
                "libelle" => "Réussi",
                "description" => "Événement dont le traitement a réussi",
            ],
            [
                "code" => Etat::ANNULE,
                "libelle" => "Annulé",
                "description" => "Événement dont le traitement a été annulé",
            ],
        ];
    }

    public function unicaen_evenement_type() : array
    {
        return [
            [
                "code" => TypeEvenementProvider::MAIL_AUTO_STAGE_DEBUT_CHOIX,
                "libelle" => "Mail automatique - Début de la phase de définition des préférences",
                "description" => "Envoie d'un mail automatique pour le début de la phase de définition des préférences",
                "parametres" => "session-id;stage-id;etudiant-id;etudiant",
                "recursion" => "",
            ],
            [
                "code" => TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX,
                "libelle" => "Mail automatique - Rappel de la phase de définition des préférences",
                "description" => "Envoie d'un mail automatique de rappel pour la phase de définition des préférences",
                "parametres" => "session-id;stage-id;etudiant-id;etudiant",
                "recursion" => "",
            ],
            [
                "code" => TypeEvenementProvider::MAIL_AUTO_AFFECTATION_VALIDEE,
                "libelle" => "Mail automatique - Affectation validée",
                "description" => "Envoie d'un mail automatique lors de la validation de l''affectation de stage",
                "parametres" => "session-id;stage-id;etudiant-id;stage;etudiant",
                "recursion" => "",
            ],
            [
                "code" => TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
                "libelle" => "Mail automatique - Début de la phase de validation",
                "description" => "Envoie d'un mail automatique de demande de validation d''un stage",
                "parametres" => "session-id;stage-id;etudiant-id;contact-stage-id;etudiant;contact",
                "recursion" => "",
            ],
            [
                "code" => TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION,
                "libelle" => "Mail automatique - Rappel de la phase de validation",
                "description" => "Envoie d'un mail automatique de rappel pour la validation d'un stage.",
                "parametres" => "session-id;stage-id;etudiant-id;contact-stage-id;etudiant;contact",
                "recursion" => "",
            ],
            [
                "code" => TypeEvenementProvider::MAIL_AUTO_STAGE_VALIDATION_EFFECTUE,
                "libelle" => "Mail automatique - Validation stage effectue",
                "description" => "Envoie de mail signalant que la validation d''un stage a été effectuée. S''il s'agit de la première fois, un mail d'information est également envoyée à l'étudiant",
                "parametres" => "session-id;stage-id;etudiant-id;etudiant",
                "recursion" => "",
            ],
        ];

    }
}