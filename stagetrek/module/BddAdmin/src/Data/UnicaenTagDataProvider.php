<?php

namespace BddAdmin\Data;


use Application\Provider\Misc\Color;
use Application\Provider\Misc\Icone;
use Application\Provider\Tag\CategorieTagProvider;
use Application\Provider\Tag\TagProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenTagDataProvider implements DataProviderInterface
{


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_tag_categorie' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
            break;
            case 'unicaen_tag' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => ['categorie_id', 'code'],
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false,
                        'columns' => [
                            'categorie_id'      => ['transformer' => 'select id from unicaen_tag_categorie where code = %s'],
                        ],],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

//    const ICONE_INFO_CIRCLE = "fas fa-info-circle";
//    const ICONE_ANNEE = "fas fa-calendar";
//    const ICONE_ETUDIANT = "fas fa-user";
//    const ICONE_GROUPE = "fas fa-users";
//    const ICONE_SESSION_STAGE = "fas fa-briefcase-medical";
//    const ICONE_STAGE = "fas fa-notes-medical";
//    const ICONE_TERRAIN = "fas fa-house-medical";
//    const ICONE_CHECK = "fas fa-check";
//    const ICONE_CONTACT = "fas fa-user-doctor";
//    const ICONE_WARNING= "fas fa-exclamation-triangle";
//    const ICONE_ERROR= "fas fa-exclamation-triangle";
//    const ICONE_LOCK= "fas fa-lock";



    public function unicaen_tag_categorie(): array
    {
        $ordre = 0;
        return[
            [
                "code" => CategorieTagProvider::ANNEE,
                "libelle" => "Année",
                "description" => "Tags concernants les années universitaires",
                "icone" => Icone::ANNEE,
                "couleur" => COLOR::DARK_ORANGE,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::ETUDIANT,
                "libelle" => "Étudiant",
                "description" => "Tags concernants les étudiants",
                "icone" => Icone::ETUDIANT,
                "couleur" => Color::PRIMARY,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::GROUPE,
                "libelle" => "Groupe",
                "description" => "Tags concernants les groupes d'étudiants",
                "icone" => Icone::GROUPE,
                "couleur" => Color::PRIMARY,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::SESSION_STAGE,
                "libelle" => "Session",
                "description" => "Tags concernants les sessions de stages",
                "icone" => Icone::SESSION_STAGE,
                "couleur" => COLOR::DARK_GREEN,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::STAGE,
                "libelle" => "Stage",
                "description" => "Tags concernants les stages",
                "icone" => Icone::STAGE,
                "couleur" => COLOR::DARK_GREEN,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::AFFECTATION,
                "libelle" => "Affectation",
                "description" => "Tags concernants les affectations de stages",
                "icone" => Icone::AFFECTATION,
                "couleur" => COLOR::DARK_GREEN,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::TERRAIN,
                "libelle" => "Terrain",
                "description" => "Tags concernants les terrains de stages",
                "icone" => Icone::TERRAIN,
                "couleur" => COLOR::DARK_BLUE,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::CATEGORIE_STAGE,
                "libelle" => "Catégorie stage",
                "description" => "Tags concernants les catégories de stages",
                "icone" => Icone::CATEGORIE_STAGE,
                "couleur" => COLOR::DARK_BLUE,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::CONTACT_STAGE,
                "libelle" => "Contact",
                "description" => "Tags concernants les contacts des stages",
                "icone" => Icone::CONTACT,
                "couleur" => COLOR::MUTED,
                "ordre" => ++$ordre,
            ],
            [
                "code" => CategorieTagProvider::ETAT,
                "libelle" => "État",
                "description" => "Tags concernants des états",
                "icone" => Icone::ETAT,
                "couleur" => COLOR::INFO,
                "ordre" => ++$ordre,
            ],
        ];
    }

    public function unicaen_tag(): array
    {
        $ordre = 0;
        return[
            [
                "code" => TagProvider::ETAT_SUCCESS,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Success",
                "description" => "Tag de succès",
                "icone" => Icone::CHECK,
                "couleur" => COLOR::SUCCESS,
                "ordre" => ++$ordre,
            ],
            [
                "code" => TagProvider::ETAT_WARNING,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Warning",
                "description" => "Tag de warning",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "ordre" => ++$ordre,
            ],
            [
                "code" => TagProvider::ETAT_ERROR,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Error",
                "description" => "Tag d'erreur",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::ERROR,
                "ordre" => ++$ordre,
            ],
            [
                "code" => TagProvider::ETAT_LOCK,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Vérouillé",
                "description" => "Tag de vérouillé",
                "icone" => Icone::FA_LOCK,
                "couleur" => COLOR::DANGER,
                "ordre" => ++$ordre,
            ],
        ];
    }
}