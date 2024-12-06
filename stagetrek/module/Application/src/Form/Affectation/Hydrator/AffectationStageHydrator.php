<?php


namespace Application\Form\Affectation\Hydrator;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class AffectationStageHydrator
 * @package Application\Form\AffectationStage\Hydrator;
 */
class AffectationStageHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var AffectationStage $affectation */
        $affectation = $object;
        $data = [];
        $data[AffectationStageFieldset::ID] = $affectation->getId();
        $data[AffectationStageFieldset::STAGE] = $affectation->getStage()->getId();
        $data[AffectationStageFieldset::TERRAIN_STAGE] = ($affectation->getTerrainStage()) ? $affectation->getTerrainStage()->getId() : 0;
        $data[AffectationStageFieldset::TERRAIN_STAGE_SECONDAIRE] = ($affectation->getTerrainStageSecondaire()) ? $affectation->getTerrainStageSecondaire()->getId() : 0;
        $data[AffectationStageFieldset::COUT_TERRAIN] = $affectation->getCoutTerrain();
        $data[AffectationStageFieldset::BONUS_MALUS] = $affectation->getBonusMalus();
        $data[AffectationStageFieldset::INFOS] = $affectation->getInformationsComplementaires();
        $data[AffectationStageFieldset::STAGE_NON_EFFECTUE] = $affectation->getStage()->isNonEffectue();

        $data[AffectationStageFieldset::PRE_VALIDER] = ($affectation->isPreValidee());
        $data[AffectationStageFieldset::VALIDER] = false; //On force ainsi a revalider l'affectation
        $data[AffectationStageFieldset::SEND_MAIL] = false; //par défaut
        return $data;
    }

    /**
     * @param array $data
     * @param $object
     * @return \Application\Entity\Db\AffectationStage
     * @throws \Exception
     */
    public function hydrate(array $data, $object): AffectationStage
    {
        /** @var AffectationStage $affectation */
        $affectation = $object;
        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($data[AffectationStageFieldset::STAGE]);
        /** @var TerrainStage $terrain */
        $terrain = null;
        if (isset($data[AffectationStageFieldset::TERRAIN_STAGE])) {
            //Pour rechercher avec un vrai id;
            $terrainId = intval($data[AffectationStageFieldset::TERRAIN_STAGE]);
            $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainId);
        }
        /** @var TerrainStage|null $terrainSecondaire */
        $terrainSecondaire = null;
        if (isset($data[AffectationStageFieldset::TERRAIN_STAGE_SECONDAIRE])) {
            //Pour rechercher avec un vrai id;
            $terrainSecondaireId = intval($data[AffectationStageFieldset::TERRAIN_STAGE_SECONDAIRE]);
            $terrainSecondaire = $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainSecondaireId);
        }

        $coutTerrain = (isset($data[AffectationStageFieldset::COUT_TERRAIN])) ? $data[AffectationStageFieldset::COUT_TERRAIN] : 0;
        $bonusMalus = (isset($data[AffectationStageFieldset::BONUS_MALUS])) ? $data[AffectationStageFieldset::BONUS_MALUS] : 0;
        $infos = (isset($data[AffectationStageFieldset::INFOS])) ? $data[AffectationStageFieldset::INFOS] : null;

        $nonEffectue = (isset($data[AffectationStageFieldset::STAGE_NON_EFFECTUE]) )? boolval($data[AffectationStageFieldset::STAGE_NON_EFFECTUE]) : false;
        $preValidee = (isset($data[AffectationStageFieldset::PRE_VALIDER]) )? boolval($data[AffectationStageFieldset::PRE_VALIDER]) : false;
        $validee = (isset($data[AffectationStageFieldset::VALIDER])) ? boolval($data[AffectationStageFieldset::VALIDER]) : false;

        $stage->setStageNonEffectue($nonEffectue);
        $affectation->setStage($stage);
        $affectation->setTerrainStage($terrain);
        $affectation->setTerrainStageSecondaire($terrainSecondaire);
        $affectation->setCoutTerrain($coutTerrain);
        $affectation->setBonusMalus($bonusMalus);
        $affectation->setInformationsComplementaires($infos);
        $affectation->setPreValidee($preValidee);
        $affectation->setValidee($validee);

        return $affectation;
    }
}