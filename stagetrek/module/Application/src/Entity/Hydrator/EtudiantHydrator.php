<?php


namespace Application\Entity\Hydrator;

use Application\Entity\Db\Adresse;
use Application\Entity\Db\Etudiant;
use Application\Exceptions\ImportException;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
use Application\Misc\Util;
use Application\Service\Referentiel\Interfaces\ReferentielEtudiantInterface;
use DateTime;
use Exception;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenUtilisateur\Entity\Db\User;

class EtudiantHydrator extends AbstractHydrator implements HydratorInterface
{
    const KEY_ID = 'id';
    const KEY_NUMERO_ETUDIANT = 'num_etu';
    const KEY_USER = 'user';
    const KEY_NOM = 'nom';
    const KEY_PRENOM = 'prenom';
    const KEY_EMAIL = 'email';
    const KEY_DATE_NAISSANCE = 'date_naissance';
    const KEY_ADDRESE = 'adresse';
//    Note les autres données ne sont pas géré ici

    /**
     * Extract values from an object
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        if($object instanceof Etudiant){
            return [
                self::KEY_ID => $object->getId(),
                self::KEY_NUMERO_ETUDIANT => $object->getNumEtu(),
                self::KEY_USER => $object->getUser(),
                self::KEY_NOM => $object->getNom(),
                self::KEY_PRENOM => $object->getPrenom(),
                self::KEY_EMAIL => $object->getNom(),
                self::KEY_DATE_NAISSANCE => $object->getDateNaissance(),
                self::KEY_ADDRESE => $object->getAdresse(),
            ];
        }

        if($object instanceof ReferentielEtudiantInterface){
            return [
                self::KEY_ID => $object->getId(),
                self::KEY_NUMERO_ETUDIANT => $object->getNumEtu(),
                self::KEY_USER => null,
                self::KEY_NOM => $object->getLastName(),
                self::KEY_PRENOM => $object->getFirstName(),
                self::KEY_EMAIL => $object->getMail(),
                self::KEY_DATE_NAISSANCE => $object->getDateNaissance(),
                self::KEY_ADDRESE => null,
            ];
        }

        throw new Exception("Objet non géré");
    }
    /**
     * @param array $data
     * @param mixed $object
     * @return Etudiant
     */
    public function hydrate(array $data, mixed $object): Etudiant
    {
        if(!$object instanceof Etudiant){
            throw new Exception("Impossible d'hydrater l'étudiant car l'objet fournis n'est pas valide");
        }

        $numEtu = ($data[self::KEY_NUMERO_ETUDIANT]) ?? null;
        $nom = ($data[self::KEY_NOM]) ?? null;
        $prenom = ($data[self::KEY_PRENOM]) ?? null;
        $email = ($data[self::KEY_EMAIL]) ?? null;
        $dateNaissance = ($data[self::KEY_DATE_NAISSANCE]) ?? null;
        if(!isset($numEtu)){
            throw new ImportException("Le numéro de l'étudiant".Util::POINT_MEDIANT."e n'est pas défini");
        }
        if(!isset($nom)){
            throw new ImportException("Le nom de l'étudiant".Util::POINT_MEDIANT."e n'est pas défini");
        }
        if(!isset($prenom)){
            throw new ImportException("Le prénom de l'étudiant".Util::POINT_MEDIANT."e n'est pas défini");
        }
        if(!isset($email)){
            throw new ImportException("L'adresse mail de l'étudiant".Util::POINT_MEDIANT."e n'est pas défini");
        }

        $object->setNumEtu($numEtu);
        $object->setNom($nom);
        $object->setPrenom($prenom);
        $object->setEmail($email);
        if(isset($dateNaissance) && $dateNaissance instanceof DateTime) {
            $object->setDateNaissance($dateNaissance);
        }
        if($object->getAdresse() === null){
            $adresse = new Adresse();
            $object->setAdresse($adresse);
        }
        return $object;
    }
}