<?php

namespace Application\Service\Affectation\Algorithmes;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\TerrainStage;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\TerrainStage\Traits\TerrainStageServiceAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
class AlgoAleatoire extends AbstractAlgorithmeAffectation implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use TerrainStageServiceAwareTrait;
    use AffectationStageServiceAwareTrait;
    use ParametreServiceAwareTrait;

    CONST CODE_ALGO = 'algo_aleatoire';
    public static function getCodeAlgo() : string
    {
        return self::CODE_ALGO;
    }

    public function run(SessionStage $sessionStage) : static
    {
        $coutTerrainMin = -10; //TODO : un paramètre ?
        $coutTerrainMax = $this->getParametreService()->getParametreValue(Parametre::AFFECTATION_COUT_TERRAIN_MAX);
        $coutTotalMax = $this->getParametreService()->getParametreValue(Parametre::AFFECTATION_COUT_TOTAL_MAX);
        $bonusMin = -10;
        $bonusMax = ($coutTotalMax-$coutTerrainMax);

        $terrainsPrincipaux = $this->getObjectManager()->getRepository(TerrainStage::class)->findBy(['isTerrainPrincipal' => true],[]);
        $terrainsSecondaires = $this->getObjectManager()->getRepository(TerrainStage::class)->findBy(['isTerrainPrincipal' => false],[]);

        $affectations = $sessionStage->getAffectations();
        $nbTerrains = sizeof($terrainsPrincipaux);
        $nbTerrains2 = sizeof($terrainsSecondaires);
        /** @var AffectationStage $affectation */
        foreach ($affectations as $affectation) {
            if($affectation->isValidee()){continue;}
            if($affectation->isPreValidee()){continue;}
            $terrainNum = intval(rand(0, $nbTerrains-1));
            $terrain = $terrainsPrincipaux[$terrainNum];
            $hasSecondaire = (intval(rand(0, 10)) <= 1);
            $terrainSecondaire = null;
            if($hasSecondaire){
                $terrainNum = intval(rand(0, $nbTerrains2-1));
                $terrainSecondaire = $terrainsSecondaires[$terrainNum];
            }
            $affectation->setTerrainStage($terrain);
            $affectation->setTerrainStageSecondaire($terrainSecondaire);
            $coutTerrain = rand($coutTerrainMin, $coutTerrainMax);
            $bonus = rand($bonusMin, $bonusMax);
            $coutTotal = min($coutTotalMax, $coutTerrain+$bonus);
            $affectation->setCoutTerrain($coutTerrain);
            $affectation->setBonusMalus($bonus);
            $affectation->setCout($coutTotal);

            $this->getObjectManager()->flush($affectation);
        }


        return $this;
    }



}