<?php



namespace Application\Form\Parametre\Hydrator;

use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Entity\Db\TerrainStage;
use Application\Form\Parametre\Fieldset\ParametreCoutTerrainFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class ParametreTerrainCoutAffectationFixeHydrator
 * @package Application\Form\Hydrator
 */
class ParametreCoutTerrainHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var ParametreTerrainCoutAffectationFixe $parametre */
        $parametre = $object;
        $data = [];
        $data[ParametreCoutTerrainFieldset::ID] = ($parametre->getId()) ?? null;
        $data[ParametreCoutTerrainFieldset::TERRAIN_STAGE] = ($parametre->getTerrainStage()) ? $parametre->getTerrainStage()->getId() : 0;
        $data[ParametreCoutTerrainFieldset::COUT] = ($parametre->getCout()) ?? 0;
        $data[ParametreCoutTerrainFieldset::COUT_MEDIAN] = $parametre->getUseCoutMedian();
        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return ParametreTerrainCoutAffectationFixe
     */
    public function hydrate(array $data, object $object) : ParametreTerrainCoutAffectationFixe
    {
        /** @var ParametreTerrainCoutAffectationFixe $parametre */
        $parametre = $object;
        /** @var TerrainStage $terrain */
        $terrain = null;
        if ($data[ParametreCoutTerrainFieldset::TERRAIN_STAGE] != '')
            $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->find($data[ParametreCoutTerrainFieldset::TERRAIN_STAGE]);
        $cout = (isset($data[ParametreCoutTerrainFieldset::COUT])) ? $data[ParametreCoutTerrainFieldset::COUT] : 0;
        $coutMedian = isset($data[ParametreCoutTerrainFieldset::COUT_MEDIAN]) && intval($data[ParametreCoutTerrainFieldset::COUT_MEDIAN])==1;
        $parametre->setTerrainStage($terrain);
        $parametre->setCout($cout);
        $parametre->setUseCoutMedian($coutMedian);
        return $parametre;
    }
}