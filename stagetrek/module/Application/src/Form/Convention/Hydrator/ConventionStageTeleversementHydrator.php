<?php

namespace Application\Form\Convention\Hydrator;

use Application\Entity\Db\ConventionStage;
use Application\Form\Convention\Fieldset\ConventionStageTeleversementFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

class ConventionStageTeleversementHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object): array
    {
        return [];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     */
    public function hydrate(array $data, object $object): ConventionStage
    {
        /** @var ConventionStage $convention */
        $convention = $object;
        if (isset($data[ConventionStageTeleversementFieldset::INPUT_FILE]['tmp_name'])) {
            $tmpName = $data[ConventionStageTeleversementFieldset::INPUT_FILE]['tmp_name'];
            if (isset($tmpName) && $tmpName != "") {
                $convention->setFileName($convention->getDefaultFileName($convention->getStage()));
            }
        }
        return $convention;

    }
}