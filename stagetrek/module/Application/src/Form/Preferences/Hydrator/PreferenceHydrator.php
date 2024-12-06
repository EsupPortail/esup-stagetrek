<?php


namespace Application\Form\Preferences\Hydrator;


use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Preferences\Fieldset\PreferenceFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class PreferenceHydrator
 * @package Application\Form\Hydrator
 */
class PreferenceHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * Extract values from an object
     *
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Preference $preference */
        $preference = $object;
        $data = [];
        $data[PreferenceFieldset::ID] = $preference->getId();
        $data[PreferenceFieldset::STAGE] = ($preference->getStage()) ? $preference->getStage()->getId() : 0;
        $data[PreferenceFieldset::TERRAIN_STAGE] = ($preference->getTerrainStage()) ? $preference->getTerrainStage()->getId() : 0;
        $data[PreferenceFieldset::TERRAIN_STAGE_SECONDAIRE] = ($preference->getTerrainStageSecondaire()) ? $preference->getTerrainStageSecondaire()->getId() : 0;
        $data[PreferenceFieldset::RANG] = $preference->getRang();
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param $object
     * @return Preference
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function hydrate(array $data, $object): Preference
    {

        /** @var Preference $preference */
        $preference = $object;        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($data[PreferenceFieldset::STAGE]);
        $terrainId = $data[PreferenceFieldset::TERRAIN_STAGE];
        /** @var TerrainStage $terrain */
        $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainId);
        /** @var TerrainStage $terrainSecondaire */
        $terrainSecondaire = null;
        if ($data[PreferenceFieldset::TERRAIN_STAGE_SECONDAIRE] != '')
            $terrainSecondaire = $this->getObjectManager()->getRepository(TerrainStage::class)->find($data[PreferenceFieldset::TERRAIN_STAGE_SECONDAIRE]);
        $rang = $data[PreferenceFieldset::RANG];
        $preference->setStage($stage);
        $preference->setTerrainStage($terrain);
        $preference->setTerrainStageSecondaire($terrainSecondaire);
        $preference->setRang($rang);
        return $preference;
    }
}