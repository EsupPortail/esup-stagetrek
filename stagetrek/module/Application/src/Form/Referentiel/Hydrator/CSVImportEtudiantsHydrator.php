<?php


namespace Application\Form\Referentiel\Hydrator;

use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Source;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
use Application\Form\Referentiel\CSVImportEtudiantsForm;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsHydrator;
use ArrayObject;
use DateTime;
use Exception;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class EtudiantHydrator
 * @package Application\Form\Etudiant\Hydrator
 */
class CSVImportEtudiantsHydrator extends AbstractImportEtudiantsHydrator
{
    /**
     * Extract values from an object
     *
     * @param ArrayObject $object
     * @return array
     */
    public function extract(object $object) : array
    {
        $res = parent::extract($object);
        $data = $object->getArrayCopy();
        $res[CSVImportEtudiantsForm::INPUT_SOURCE] = ($data[CSVImportEtudiantsForm::INPUT_SOURCE]) ?? Source::CSV;
        $res[CSVImportEtudiantsForm::INPUT_IMPORT_FILE] = ($data[CSVImportEtudiantsForm::INPUT_IMPORT_FILE]) ?? null;

        return $res;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param mixed $object
     * @return ArrayObject
     * @throws \Exception
     */
    public function hydrate(array $data, mixed $object): ArrayObject
    {
        parent::hydrate($data, $object);
        $fileData = ($data[CSVImportEtudiantsForm::INPUT_IMPORT_FILE]) ?? [];
        if(!isset($fileData) || !is_array($fileData)){
            throw new Exception("Le type de données est invalide");
        }

        return new ArrayObject(['fileData' => $fileData, CSVImportEtudiantsForm::INPUT_SOURCE => Source::CSV]);

    }
}