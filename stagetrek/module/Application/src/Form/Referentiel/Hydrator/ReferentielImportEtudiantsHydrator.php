<?php


namespace Application\Form\Referentiel\Hydrator;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsHydrator;
use Application\Form\Referentiel\ReferentielImportEtudiantsForm;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielPromoServiceAwareTrait;
use ArrayObject;
use Exception;

/**
 * Class EtudiantHydrator
 * @package Application\Form\Etudiant\Hydrator
 */
class ReferentielImportEtudiantsHydrator extends AbstractImportEtudiantsHydrator
{
    Use ReferentielPromoServiceAwareTrait;
    Use AnneeUniversitaireServiceAwareTrait;

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
        $refId = (isset($data[ReferentielImportEtudiantsForm::INPUT_REFERENTIEL])) ? intval($data[ReferentielImportEtudiantsForm::INPUT_REFERENTIEL]) : 0;
        /** @var ReferentielPromo $referentiel */
        $referentiel = $this->getReferentielPromoService()->find($refId);

        $res[ReferentielImportEtudiantsForm::INPUT_REFERENTIEL] = ($refId) ?? null;
        $res[ReferentielImportEtudiantsForm::INPUT_SOURCE] = ($referentiel) ? $referentiel->getSource()->getCode() : null;
        $res[ReferentielImportEtudiantsForm::INPUT_ANNEE] = ($data[ReferentielImportEtudiantsForm::INPUT_ANNEE]) ?? null;
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
        $refId = ($data[ReferentielImportEtudiantsForm::INPUT_REFERENTIEL]) ? intval($data[ReferentielImportEtudiantsForm::INPUT_REFERENTIEL]) : 0;
        $anneeId = ($data[ReferentielImportEtudiantsForm::INPUT_ANNEE]) ? intval($data[ReferentielImportEtudiantsForm::INPUT_ANNEE]) : 0;

        /** @var ReferentielPromo $referentiel */
        $referentiel = $this->getReferentielPromoService()->find($refId);
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getAnneeUniversitaireService()->find($anneeId);
        if(!isset($annee)){
            throw new Exception("L'année demandé n'ais pas valide");
        }
        return new ArrayObject(['referentiel' => $referentiel, 'annee' => $annee, ReferentielImportEtudiantsForm::INPUT_SOURCE => $referentiel->getSource()->getCode()]);

    }
}