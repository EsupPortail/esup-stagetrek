<?php


namespace Application\Form\Groupe\Hydrator;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\NiveauEtude;
use Application\Form\Groupe\Fieldset\GroupeFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class GroupeHydrator
 * @package Application\Form\Groupe\Hydrator
 */
class GroupeHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
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
        /** @var Groupe $groupe */
        $groupe = $object;
        $data = [
            GroupeFieldset::ID => $groupe->getId(),
            GroupeFieldset::LIBELLE => $groupe->getLibelle(),
        ];
        if ($groupe->getAnneeUniversitaire() != null) {
            $data[GroupeFieldset::ANNEE_UNIVERSITAIRE] = $groupe->getAnneeUniversitaire()->getId();
        }
        if ($groupe->getNiveauEtude() != null) {
            $data[GroupeFieldset::NIVEAU_ETUDE] = $groupe->getNiveauEtude()->getId();
        }
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     * @param array $data
     * @param $object
     * @return Groupe
     */
    public function hydrate(array $data, $object): Groupe
    {
        /** @var Groupe $groupe */
        $groupe = $object;
        if(isset($data[GroupeFieldset::LIBELLE])){
            $groupe->setLibelle($data[GroupeFieldset::LIBELLE]);
        }
        if (isset($data[GroupeFieldset::NIVEAU_ETUDE])){
            $niveauEtude = $this->getObjectManager()->getRepository(NiveauEtude::class)->find($data[GroupeFieldset::NIVEAU_ETUDE]);
            $groupe->setNiveauEtude($niveauEtude);
        }
        if (isset($data[GroupeFieldset::ANNEE_UNIVERSITAIRE])){
            $annee = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->find($data[GroupeFieldset::ANNEE_UNIVERSITAIRE]);
            $groupe->setAnneeUniversitaire($annee);
        }
        return $groupe;
    }
}