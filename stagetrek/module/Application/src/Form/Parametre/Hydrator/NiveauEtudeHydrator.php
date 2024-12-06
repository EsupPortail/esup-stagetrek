<?php


namespace Application\Form\Parametre\Hydrator;

use Application\Entity\Db\NiveauEtude;
use Application\Form\Parametre\Fieldset\NiveauEtudeFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class NiveauEtudeHydrator
 * @package Application\Form\Hydrator
 */
class NiveauEtudeHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var NiveauEtude $niveauEtude */
        $niveauEtude = $object;
        $data = [];
        $data[NiveauEtudeFieldset::ID] = ($niveauEtude->getId()) ?? null;
        $data[NiveauEtudeFieldset::LIBELLE] = ($niveauEtude->getLibelle()) ?? "";
        $data[NiveauEtudeFieldset::NB_STAGES] = ($niveauEtude->getNbStages()) ?? 0;
        $data[NiveauEtudeFieldset::ORDRE] = ($niveauEtude->getOrdre()) ?? 0;
        $data[NiveauEtudeFieldset::NIVEAU_ETUDE_PARENT] = ($niveauEtude->getNiveauEtudeParent()) ? $niveauEtude->getNiveauEtudeParent()->getId() : 0;
        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return NiveauEtude
     */
    public function hydrate(array $data, object $object): NiveauEtude
    {
        /** @var NiveauEtude $niveauEtude */
        $niveauEtude = $object;

        if(isset($data[NiveauEtudeFieldset::LIBELLE])) $niveauEtude->setLibelle(trim($data[NiveauEtudeFieldset::LIBELLE]));
        if(isset($data[NiveauEtudeFieldset::NB_STAGES])) $niveauEtude->setNbStages(intval($data[NiveauEtudeFieldset::NB_STAGES]));
        if(isset($data[NiveauEtudeFieldset::ORDRE])) $niveauEtude->setOrdre(intval($data[NiveauEtudeFieldset::ORDRE]));
        if (isset($data[NiveauEtudeFieldset::NIVEAU_ETUDE_PARENT])
            && intval($data[NiveauEtudeFieldset::NIVEAU_ETUDE_PARENT]) > 0){
            $id =  intval($data[NiveauEtudeFieldset::NIVEAU_ETUDE_PARENT]);
            /** @var NiveauEtude $parent */
            $parent = $this->getObjectManager()->getRepository(NiveauEtude::class)->find($id);
            $niveauEtude->setNiveauEtudeParent($parent);
        }
        else{
            $niveauEtude->setNiveauEtudeParent(null);
        }
        return $niveauEtude;
    }
}