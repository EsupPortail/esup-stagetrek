<?php


namespace Application\Form\Contrainte\Hydrator;

use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\ContrainteCursusPortee;
use Application\Entity\Db\TerrainStage;
use Application\Form\Contrainte\Fieldset\ContrainteCursusFieldset;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class ContrainteCursusHydrator
 * @package Application\Form\ContraintesCursus\Hydrator
 */
class ContrainteCursusHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        $contrainte = $object;
        $data = [];
        $data[ContrainteCursusFieldset::ID] = ($contrainte->getId()) ? $contrainte->getId() : 0;
        $data[ContrainteCursusFieldset::LIBELLE] = $contrainte->getLibelle();
        $data[ContrainteCursusFieldset::ACRONYME] = $contrainte->getAcronyme();
        $data[ContrainteCursusFieldset::DESCRIPTION] = ($contrainte->getDescription()) ? $contrainte->getDescription() : "";
        if ($contrainte->getContrainteCursusPortee() !== null) {
            $data[ContrainteCursusFieldset::PORTEE] = $contrainte->getContrainteCursusPortee()->getId();
        }
        if ($contrainte->hasPorteeCategorie() && $contrainte->getCategorieStage() !== null) {
            $data[ContrainteCursusFieldset::CATEGORIE_STAGE] = $contrainte->getCategorieStage()->getId();
        }
        if ($contrainte->hasPorteeTerrain() && $contrainte->getTerrainStage() !== null) {
            $data[ContrainteCursusFieldset::TERRAIN_STAGE] = $contrainte->getTerrainStage()->getId();
        }
        if ($contrainte->getNombreDeStageMin() !== null) {
            $data[ContrainteCursusFieldset::NB_STAGE_MIN] = $contrainte->getNombreDeStageMin();
        } else {
            $data[ContrainteCursusFieldset::NB_STAGE_MIN] = 0;
        }
        if ($contrainte->getNombreDeStageMax() !== null) {
            $data[ContrainteCursusFieldset::NB_STAGE_MAX] = $contrainte->getNombreDeStageMax();
        } else {
            $data[ContrainteCursusFieldset::NB_STAGE_MAX] = 0;
        }

        $data[ContrainteCursusFieldset::DATE_DEBUT] = $contrainte->getDateDebut();
        $data[ContrainteCursusFieldset::DATE_FIN] = $contrainte->getDateFin();
        $data[ContrainteCursusFieldset::ORDRE] = $contrainte->getOrdre();

        return $data;
    }

    /**
     * @param array $data
     * @param mixed $object
     * @return mixed
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function hydrate(array $data, mixed $object): mixed
    {   $contrainte = $object;
        $libelle = $data[ContrainteCursusFieldset::LIBELLE];
        $acronyme = $data[ContrainteCursusFieldset::ACRONYME];
        $description = $data[ContrainteCursusFieldset::DESCRIPTION];
        /** @var ContrainteCursusPortee $portee */
        $portee = null;
        /** @var CategorieStage $categorie */
        $categorie = null;
        /** @var TerrainStage $terrain */
        $terrain = null;
        if ($data[ContrainteCursusFieldset::PORTEE] != '')
            $portee = $this->getObjectManager()->getRepository(ContrainteCursusPortee::class)->find($data[ContrainteCursusFieldset::PORTEE]);
        if ($data[ContrainteCursusFieldset::CATEGORIE_STAGE] != '')
            $categorie = $this->getObjectManager()->getRepository(CategorieStage::class)->find($data[ContrainteCursusFieldset::CATEGORIE_STAGE]);
       if ($data[ContrainteCursusFieldset::TERRAIN_STAGE] != '')
            $terrain = $this->getObjectManager()->getRepository(TerrainStage::class)->find($data[ContrainteCursusFieldset::TERRAIN_STAGE]);
        $nbMin = ($data[ContrainteCursusFieldset::NB_STAGE_MIN] != 0) ? $data[ContrainteCursusFieldset::NB_STAGE_MIN] : null;
        $nbMax = ($data[ContrainteCursusFieldset::NB_STAGE_MAX] != 0) ? $data[ContrainteCursusFieldset::NB_STAGE_MAX] : null;

        $dDebut = new DateTime();
        if ($data[ContrainteCursusFieldset::DATE_DEBUT]) {
            $chaine = explode("-", $data[ContrainteCursusFieldset::DATE_DEBUT]);
            $dDebut->setDate($chaine[0], $chaine[1], $chaine[2]);
            $dDebut->setTime(0, 0);
        }
        $dFin = new DateTime();
        if ($data[ContrainteCursusFieldset::DATE_FIN]) {
            $chaine = explode("-", $data[ContrainteCursusFieldset::DATE_FIN]);
            $dFin->setDate($chaine[0], $chaine[1], $chaine[2]);
            $dFin->setTime(0, 0);
        }
        $ordre = ($data[ContrainteCursusFieldset::ORDRE] ?? 0);

        $contrainte->setLibelle($libelle);
        $contrainte->setAcronyme($acronyme);
        $contrainte->setDescription($description);
        $contrainte->setContrainteCursusPortee($portee);
        $contrainte->setCategorieStage($categorie);
        $contrainte->setTerrainStage($terrain);
        $contrainte->setNombreDeStageMin($nbMin);
        $contrainte->setNombreDeStageMax($nbMax);
        if (!$contrainte->getDateDebut()
            || $contrainte->getDateDebut()->getTimestamp() != $dDebut->getTimestamp()) {
            $contrainte->setDateDebut($dDebut);
        }
        if (!$contrainte->getDateFin()
            || $contrainte->getDateFin()->getTimestamp() != $dFin->getTimestamp()) {
            $contrainte->setDateFin($dFin);
        }
        $contrainte->setOrdre($ordre);
        return $contrainte;
    }
}