<?php

namespace Application\Form\Convention\Hydrator;

use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Convention\Fieldset\ModeleConventionStageFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenRenderer\Entity\Db\Template;

class ModeleConventionStageHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object): array
    {
        /** @var ModeleConventionStage $modele */
        $modele = $object;

        $data[ModeleConventionStageFieldset::ID] = $modele->getId();
        $data[ModeleConventionStageFieldset::LIBELLE] = $modele->getLibelle();
        $data[ModeleConventionStageFieldset::CODE] = $modele->getCode();
        $data[ModeleConventionStageFieldset::DESCRIPTION] = $modele->getDescription();
        $data[ModeleConventionStageFieldset::CORPS] = $modele->getCorps();
        $data[ModeleConventionStageFieldset::CSS] = $modele->getCss();

        foreach ($modele->getTerrainsStages() as $terrainStage) {
            $data[ModeleConventionStageFieldset::TERRAINS][] = $terrainStage->getId();
        }

        return $data;

    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param object $object
     * @return ModeleConventionStage
     */
    public function hydrate(array $data, object $object) : ModeleConventionStage
    {
        /** @var ModeleConventionStage $modele */
        $modele = $object;

        if(!$modele->hasCorpsTemplate()){
            $corpsTemplate = new Template();
            $corpsTemplate->setNamespace(ModeleConventionStage::RENDER_CONVENTION_NAMESPACE);
            $corpsTemplate->setType(ModeleConventionStage::RENDER_CONVENTION_CORPS_TYPE);
            $modele->setCorpsTemplate($corpsTemplate);
        }

        if(isset($data[ModeleConventionStageFieldset::CODE])){
            $modele->setCode($data[ModeleConventionStageFieldset::CODE]);
        }
        if(isset($data[ModeleConventionStageFieldset::LIBELLE])){
            $modele->setLibelle($data[ModeleConventionStageFieldset::LIBELLE]);
        }
        if(isset($data[ModeleConventionStageFieldset::DESCRIPTION])){
            $modele->setDescription($data[ModeleConventionStageFieldset::DESCRIPTION]);
        }
        if(isset($data[ModeleConventionStageFieldset::CORPS])){
            $modele->setCorps($data[ModeleConventionStageFieldset::CORPS]);
        }
        if(isset($data[ModeleConventionStageFieldset::CSS])){
            $modele->setCss($data[ModeleConventionStageFieldset::CSS]);
        }

        $terrainsData = ($data[ModeleConventionStageFieldset::TERRAINS] ?? []);
        $modele->getTerrainsStages()->clear();
        foreach ($terrainsData as $terrainId) {
            $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->findOneBy(['id' => $terrainId]);
            $modele->addTerrainStage($terrain);
        }
        return $modele;
    }
}