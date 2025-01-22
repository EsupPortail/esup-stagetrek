<?php

namespace Application\Service\ConventionStage;

use Application\Entity\Db\ModeleConventionStage;
use Application\Service\Misc\CommonEntityService;
use UnicaenRenderer\Service\Macro\MacroService;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;

/**
 * Class ModeleConventionStageService
 * @package Application\Service\EntityService
 */
class ModeleConventionStageService extends CommonEntityService
{

    /** @return string */
    public function getEntityClass(): string
    {
        return ModeleConventionStage::class;
    }
}


