<?php

namespace Application\Form\TerrainStage\Hydrator;

use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\NiveauEtude;
use Application\Entity\Db\TerrainStage;
use Application\Form\TerrainStage\Fieldset\TerrainStageFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class TerrainStageHydrator
 * @package Application\Form\TerrainStage\Hydrator
 */
class TerrainStageHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * Extract values from an object
     *
     * @param object $object
     * @return array
     */
    public function extract(object $object): array
    {
        /** @var TerrainStage $terrain */
        $terrain = $object;
        $data = [TerrainStageFieldset::ID => $terrain->getId(), TerrainStageFieldset::CODE => $terrain->getCode(), TerrainStageFieldset::LIBELLE => $terrain->getLibelle(), TerrainStageFieldset::SERVICE => $terrain->getService(), TerrainStageFieldset::CATEGORIE_STAGE => ($terrain->getCategorieStage()) ? $terrain->getCategorieStage()->getId() : null, TerrainStageFieldset::MODELE_CONVENTION => ($terrain->getModeleConventionStage()) ? $terrain->getModeleConventionStage()->getId() : null, TerrainStageFieldset::ADRESSE => $terrain->getAdresse(), TerrainStageFieldset::INFOS => $terrain->getInfos(), TerrainStageFieldset::LIEN => $terrain->getLien(), TerrainStageFieldset::HORS_SUBDIVISION => $terrain->isHorsSubdivision(), TerrainStageFieldset::ACTIF => $terrain->isActif(), TerrainStageFieldset::PREFERENCES_AUTORISEES => $terrain->getPreferencesAutorisees(), TerrainStageFieldset::MIN_PLACE => $terrain->getMinPlace(), TerrainStageFieldset::IDEAL_PLACE => $terrain->getIdealPlace(), TerrainStageFieldset::MAX_PLACE => $terrain->getMaxPlace(),];
        /** @var TerrainStage $t2
         */
        foreach ($terrain->getTerrainsPrincipaux() as $t2) {
            $data[TerrainStageFieldset::TERRAINS_PRINCIPAUX_ASSOCIES][] = $t2->getId();
        }
        foreach ($terrain->getTerrainsSecondaires() as $t2) {
            $data[TerrainStageFieldset::TERRAINS_SECONDAIRES_ASSOCIES][] = $t2->getId();
        }
        /** @var NiveauEtude $n */
        foreach ($terrain->getNiveauxEtudesContraints() as $n) {
            $data[TerrainStageFieldset::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE][] = $n->getId();
        }
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param object $object
     * @return TerrainStage
     */
    public function hydrate(array $data, object $object): TerrainStage
    {
        /** @var TerrainStage $terrain */
        $terrain = $object;
        if ($data[TerrainStageFieldset::CATEGORIE_STAGE]) {
            $id = $data[TerrainStageFieldset::CATEGORIE_STAGE];
            if ($terrain->getCategorieStage() === null || $terrain->getCategorieStage()->getId() != $id) {
                /** @var CategorieStage $categorieStage */
                $categorieStage = $this->getObjectManager()->getRepository(CategorieStage::class)->find($id);
                $terrain->setCategorieStage($categorieStage);
                $terrain->setIsTerrainPrincipal($categorieStage->isCategoriePrincipale());
            }
        }
        if (isset($data[TerrainStageFieldset::CODE])) {
            $terrain->setCode(trim($data[TerrainStageFieldset::CODE]));
        }
        if (isset($data[TerrainStageFieldset::LIBELLE])) {
            $terrain->setLibelle(trim($data[TerrainStageFieldset::LIBELLE]));
        }
        (isset($data[TerrainStageFieldset::SERVICE])) ? $terrain->setService(trim($data[TerrainStageFieldset::SERVICE])) : $terrain->setService(null);
        if (isset($data[TerrainStageFieldset::ADRESSE])) {
            $terrain->setAdresse($data[TerrainStageFieldset::ADRESSE]);
        }
        (isset($data[TerrainStageFieldset::INFOS])) ? $terrain->setInfos(trim($data[TerrainStageFieldset::INFOS])) : $terrain->setInfos();
        (isset($data[TerrainStageFieldset::LIEN])) ? $terrain->setLien(trim($data[TerrainStageFieldset::LIEN])) : $terrain->setLien();
        (isset($data[TerrainStageFieldset::HORS_SUBDIVISION])) ? $terrain->setHorsSubdivision(boolval($data[TerrainStageFieldset::HORS_SUBDIVISION])) : $terrain->setHorsSubdivision(false);
        (isset($data[TerrainStageFieldset::ACTIF])) ? $terrain->setActif(boolval($data[TerrainStageFieldset::ACTIF])) : $terrain->setActif(false);
        (isset($data[TerrainStageFieldset::PREFERENCES_AUTORISEES])) ? $terrain->setPreferencesAutorisees(boolval($data[TerrainStageFieldset::PREFERENCES_AUTORISEES])) : $terrain->setPreferencesAutorisees(true);
        (isset($data[TerrainStageFieldset::MIN_PLACE])) ? $terrain->setMinPlace(intval($data[TerrainStageFieldset::MIN_PLACE])) : $terrain->setMinPlace(0);
        (isset($data[TerrainStageFieldset::IDEAL_PLACE])) ? $terrain->setIdealPlace(intval($data[TerrainStageFieldset::IDEAL_PLACE])) : $terrain->setIdealPlace(0);
        (isset($data[TerrainStageFieldset::MAX_PLACE])) ? $terrain->setMaxPlace(intval($data[TerrainStageFieldset::MAX_PLACE])) : $terrain->setMaxPlace(0);
        if ($terrain->isTerrainPrincipal()) {
            $terrain->getTerrainsPrincipaux()->clear();
            $terrain->getTerrainsSecondaires()->clear();
            $terrainsSecondairesData = ($data[TerrainStageFieldset::TERRAINS_SECONDAIRES_ASSOCIES] ?? []);
            foreach ($terrainsSecondairesData as $terrainSecondaireId) {
                $terrainSecondaire = $this->getObjectManager()->getRepository(TerrainStage::class)->findOneBy(['id' => $terrainSecondaireId, 'isTerrainPrincipal' => false]);
                $terrain->addTerrainSecondaire($terrainSecondaire);
            }
        } elseif ($terrain->isTerrainSecondaire()) {
            $terrain->getTerrainsPrincipaux()->clear();
            $terrain->getTerrainsSecondaires()->clear();
            $terrainsPrincipauxData = ($data[TerrainStageFieldset::TERRAINS_PRINCIPAUX_ASSOCIES] ?? []);
            foreach ($terrainsPrincipauxData as $terrainPrincipalId) {
                $terrainPrincipal = $this->getObjectManager()->getRepository(TerrainStage::class)->findOneBy(['id' => $terrainPrincipalId, 'isTerrainPrincipal' => true]);
                $terrain->addTerrainPrincipal($terrainPrincipal);
            }
        }
        if ($data[TerrainStageFieldset::MODELE_CONVENTION]) {
            $id = $data[TerrainStageFieldset::MODELE_CONVENTION];
            if ($terrain->getModeleConventionStage() === null || $terrain->getModeleConventionStage()->getId() != $id) {
                /** @var ModeleConventionStage $modele */
                $modele = $this->getObjectManager()->getRepository(ModeleConventionStage::class)->find($id);
                $terrain->setModeleConventionStage($modele);
            }
        } else {
            $terrain->setModeleConventionStage();
        }
        if (isset($data[TerrainStageFieldset::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE]) && (!empty($data[TerrainStageFieldset::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE]))) {
            if($terrain->getId() !== null){
                // on retire les contraintes qui ne sont plus d'actualités
                /** @var NiveauEtude $niveau */
                foreach ($terrain->getNiveauxEtudesContraints() as $niveau) {
                    if (!in_array($niveau->getId(), $data[TerrainStageFieldset::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE])) {
                        $terrain->removeNiveauEtudeContraint($niveau);
                    }
                }
            }
            foreach ($data[TerrainStageFieldset::RESTRICTIONS_TERRAIN_NIVEAU_ETUDE] as $niveauEtudeId) {
                /** @var NiveauEtude $niveau */
                $niveau = $this->getObjectManager()->getRepository(NiveauEtude::class)->find($niveauEtudeId);
                $terrain->addNiveauEtudeContraint($niveau);
            }
        } else {//Aucune contraintes, on retire les contraintes existantes
            $terrain->getNiveauxEtudesContraints()->clear();
        }
        return $terrain;
    }
}