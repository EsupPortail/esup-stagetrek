<?php


namespace Application\Form\Etudiant\Hydrator;

use Application\Entity\Db\Etudiant;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
use DateTime;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class EtudiantHydrator
 * @package Application\Form\Etudiant\Hydrator
 */
class EtudiantHydrator extends AbstractHydrator implements HydratorInterface
{
    /**
     * Extract values from an object
     *
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Etudiant $etudiant */
        $etudiant = $object;

        /** @var User $user */
        $user = $etudiant->getUser();
        $idUser = ($user) ? $user->getId() : null;
        $dateNaissance = $etudiant->getDateNaissance();
        return [
            EtudiantFieldset::ID => $etudiant->getId(),
            EtudiantFieldset::USER => $idUser,
            EtudiantFieldset::NUM_ETU => $etudiant->getNumEtu(),
            EtudiantFieldset::NOM => $etudiant->getNom(),
            EtudiantFieldset::PRENOM => $etudiant->getPrenom(),
            EtudiantFieldset::MAIL => $etudiant->getEmail(),
            EtudiantFieldset::DATE_NAISSANCE => $dateNaissance,
            AdresseFieldset::FIELDSET_NAME => $etudiant->getAdresse(),
        ];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param mixed $object
     * @return Etudiant
     */
    public function hydrate(array $data, mixed $object): Etudiant
    {

        /** @var Etudiant $etudiant */
        $etudiant = $object;
        if(isset($data[EtudiantFieldset::NUM_ETU])){
            $etudiant->setNumEtu(trim($data[EtudiantFieldset::NUM_ETU]));
        }
        if(isset($data[EtudiantFieldset::NOM])){ $etudiant->setNom(trim($data[EtudiantFieldset::NOM]));}
        if(isset($data[EtudiantFieldset::PRENOM])){ $etudiant->setPrenom(trim($data[EtudiantFieldset::PRENOM]));}
        if(isset($data[EtudiantFieldset::MAIL])){ $etudiant->setEmail(trim($data[EtudiantFieldset::MAIL]));}
        if (isset($data[EtudiantFieldset::DATE_NAISSANCE])
            && $data[EtudiantFieldset::DATE_NAISSANCE] != '') {
            $date = DateTime::createFromFormat('Y-m-d', $data[EtudiantFieldset::DATE_NAISSANCE]);
            if ($date) {
                $date->setTime(0, 0);
                if (!$etudiant->getDateNaissance()
                    || $etudiant->getDateNaissance()->getTimestamp() != $date->getTimestamp()) {
                    $etudiant->setDateNaissance($date);
                }
            }
        }
        if(!isset($data[EtudiantFieldset::DATE_NAISSANCE]) || !isset($date) || !$date){
            $etudiant->setDateNaissance(null);
        }
        if (isset($data[EtudiantFieldset::ADRESSE])) {
            $etudiant->setAdresse($data[EtudiantFieldset::ADRESSE]);
        }
        return $etudiant;
    }
}