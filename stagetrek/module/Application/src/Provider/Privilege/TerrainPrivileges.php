<?php

namespace Application\Provider\Privilege;

use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * Class StagePrivileges
 * @package Application\Provider\Privilege
 */
class TerrainPrivileges extends Privileges
{
    const TERRAIN_STAGE_AFFICHER = 'terrain-terrain_stage_afficher';
    const TERRAIN_STAGE_AJOUTER = 'terrain-terrain_stage_ajouter';
    const TERRAIN_STAGE_MODIFIER = 'terrain-terrain_stage_modifier';
    const TERRAIN_STAGE_SUPPRIMER = 'terrain-terrain_stage_supprimer';
    const CATEGORIE_STAGE_AFFICHER = 'terrain-categorie_stage_afficher';
    const CATEGORIE_STAGE_AJOUTER = 'terrain-categorie_stage_ajouter';
    const CATEGORIE_STAGE_MODIFIER = 'terrain-categorie_stage_modifier';
    const CATEGORIE_STAGE_SUPPRIMER = 'terrain-categorie_stage_supprimer';
    const TERRAINS_IMPORTER = 'terrain-terrains_importer';
    const TERRAINS_EXPORTER = 'terrain-terrains_exporter';
}