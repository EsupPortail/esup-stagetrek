<?php


namespace Application\Form\Etudiant\Hydrator;

use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Form\Etudiant\Fieldset\DisponibiliteFieldset;
use DateInterval;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;

/**
 * Class DisponibiliteHydrator
 * @package Application\Form\Disponibilite
 */
class DisponibiliteHydrator extends AbstractHydrator implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Disponibilite $disponibilite */
        $disponibilite = $object;
        $data = [];
        $data[DisponibiliteFieldset::ID] = $disponibilite->getId();
        $data[DisponibiliteFieldset::ETUDIANT] = ($disponibilite->getEtudiant()) ? $disponibilite->getEtudiant()->getId() : null;
        $defaultDateDebut = new DateTime();
        $defaultDateFin = new DateTime();
        $defaultDateFin = $defaultDateFin->add(new DateInterval('P1M'));
        $data[DisponibiliteFieldset::DATE_DEBUT] = ($disponibilite->getDateDebut()) ?
            $disponibilite->getDateDebut()->format('Y-m-d') : $defaultDateDebut;
        $data[DisponibiliteFieldset::DATE_FIN] = ($disponibilite->getDateFin()) ?
            $disponibilite->getDateFin()->format('Y-m-d') : $defaultDateFin;
        $data[DisponibiliteFieldset::INFO] = $disponibilite->getInformationsComplementaires();
        return $data;
    }

    /**
     * @param array $data
     * @param $object
     * @return Disponibilite
     */
    public function hydrate(array $data, $object): Disponibilite
    {
        /** @var Disponibilite $disponibilite */
        $disponibilite = $object;
        /** @var Etudiant $etudiant */
        $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->find($data[DisponibiliteFieldset::ETUDIANT]);
        $disponibilite->setEtudiant($etudiant);
        if ($data[DisponibiliteFieldset::DATE_DEBUT]) {
            $date = new DateTime();
            $chaine = explode("-", $data[DisponibiliteFieldset::DATE_DEBUT]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if (!$disponibilite->getDateDebut()
                || $disponibilite->getDateDebut()->getTimestamp() != $date->getTimestamp()) {
                $disponibilite->setDateDebut($date);
            }
        }
        if ($data[DisponibiliteFieldset::DATE_FIN]) {
            $date = new DateTime();
            //Todo : mettre les champs date en dateTime ne bdd pôur ne pus avoir de pb
//            $chaine = explode("-", $data[DisponibiliteFieldset::DATE_FIN]);
//            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
//            $date->setTime(23, 59);
            $chaine = explode("-", $data[DisponibiliteFieldset::DATE_FIN]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if (!$disponibilite->getDateFin()
                || $disponibilite->getDateFin()->getTimestamp() != $date->getTimestamp()) {
                $disponibilite->setDateFin($date);
            }
        }
        if ($data[DisponibiliteFieldset::INFO]) {
            $disponibilite->setInformationsComplementaires(trim($data[DisponibiliteFieldset::INFO]));
        } else {
            $disponibilite->setInformationsComplementaires();
        }
        return $disponibilite;
    }
}