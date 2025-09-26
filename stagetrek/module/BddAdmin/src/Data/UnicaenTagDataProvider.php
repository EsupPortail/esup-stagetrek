<?php

namespace BddAdmin\Data;


use Application\Entity\Db\AdresseType;
use Application\Provider\Tag\CategorieTagProvider;
use Application\Provider\Tag\TagProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenTagDataProvider implements DataProviderInterface {


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

    const ICONE_INFO_CIRCLE = "fas fa-info-circle";
    const ICONE_CHECK = "fas fa-check";
    const ICONE_WARNING= "fas fa-exclamation-triangle";
    const ICONE_ERROR= "fas fa-exclamation-triangle";
    const ICONE_LOCK= "fas fa-lock";

    const COLOR_PRIMARY = "#3381c5";
    const COLOR_SECONDARY = "#6c757d";
    const COLOR_SUCCESS = "#5fa042";
    const COLOR_DANGER = "#c80000";
    const COLOR_ERROR = "#c80000";
    const COLOR_INFO = "#000064";
    const COLOR_WARNING = "#ffa500";


    public function unicaen_tag_categorie(): array
    {
        $ordre = 0;
        return[
            [
                "code" => CategorieTagProvider::ETAT,
                "libelle" => "État",
                "description" => "Tags concernants des états",
                "icone" => self::ICONE_INFO_CIRCLE,
                "couleur" => self::COLOR_INFO,
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
                "icone" => self::ICONE_CHECK,
                "couleur" => self::COLOR_SUCCESS,
                "ordre" => ++$ordre,
            ],
            [
                "code" => TagProvider::ETAT_WARNING,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Warning",
                "description" => "Tag de warning",
                "icone" => self::ICONE_WARNING,
                "couleur" => self::COLOR_WARNING,
                "ordre" => ++$ordre,
            ],
            [
                "code" => TagProvider::ETAT_ERROR,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Error",
                "description" => "Tag d'erreur",
                "icone" => self::ICONE_ERROR,
                "couleur" => self::COLOR_ERROR,
                "ordre" => ++$ordre,
            ],
            [
                "code" => TagProvider::ETAT_LOCK,
                "categorie_id" => CategorieTagProvider::ETAT,
                "libelle" => "Vérouillé",
                "description" => "Tag de vérouillé",
                "icone" => self::ICONE_LOCK,
                "couleur" => self::COLOR_DANGER,
                "ordre" => ++$ordre,
            ],
        ];
    }
}