<?php


namespace Application\Form\Stages\Hydrator;

use Application\Entity\Db\Stage;
use Application\Form\Stages\Fieldset\ValidationStageFieldset;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class ValidationStageHydrator
 * @package Application\Form\Stages\Hydrator
 */
class ValidationStageHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var Stage $stage */
        $stage = $object;
        $data = [];
        $etatValidation = ValidationStageFieldset::VALIDATION_ETAT_NON_DEFINI ; //Pas d'option choisie par défaut
        $validationStage = $stage->getValidationStage();

        if (!$validationStage) {
            return [];
        } //Cas d'erreur théoriquement impossible
        $data[ValidationStageFieldset::ID] = $validationStage->getId();
        if ($validationStage->isValide()) {
            $etatValidation = ValidationStageFieldset::VALIDATION_ETAT_VALIDER;
        } elseif ($validationStage->isInvalide()) {
            $etatValidation = ValidationStageFieldset::VALIDATION_ETAT_INVALIDER;
        }
        $data[ValidationStageFieldset::INPUT_ETAT_VALIDATION] = $etatValidation;
        $data[ValidationStageFieldset::INPUT_WARNING] = $validationStage->getWarning();
        $data[ValidationStageFieldset::INPUT_VALIDATE_BY] = $validationStage->getValidateBy();
        $data[ValidationStageFieldset::INPUT_COMMENTAIRES] = $validationStage->getCommentaire();
        $data[ValidationStageFieldset::INPUT_COMMENTAIRES_CACHE] = $validationStage->getCommentaireCache();
        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return \Application\Entity\Db\Stage
     */
    public function hydrate(array $data, object $object) : Stage
    {
        /** @var Stage $stage */
        $stage = $object;
        $validationStage = $stage->getValidationStage();
        if (!$validationStage) {
            return $stage;
        } //Logiquement impossible
        $etat = intval(($data[ValidationStageFieldset::INPUT_ETAT_VALIDATION]) ?? 0);
        $validateBy = trim($data[ValidationStageFieldset::INPUT_VALIDATE_BY]??"");
        switch ($etat) {
            case ValidationStageFieldset::VALIDATION_ETAT_INVALIDER :
                if(!$validationStage->isInvalide()) {
                    $validationStage->setIsInvalide(true);
                    $validationStage->setValidateBy($validateBy);
                    $validationStage->setDateValidation(new DateTime());
                }
            break;
            case ValidationStageFieldset::VALIDATION_ETAT_VALIDER :
                if(!$validationStage->isValide()) {
                $validationStage->setIsValide(true);
                    $validationStage->setValidateBy($validateBy);
                    $validationStage->setDateValidation(new DateTime());
                    }
            break;
            case ValidationStageFieldset::VALIDATION_ETAT_NON_DEFINI :
            default:
                $validationStage->setIsValide(false);
                $validationStage->setIsInvalide(false);
                $validationStage->setValidateBy();
                $validationStage->setDateValidation();
            break;
        }
        $warning = intval(($data[ValidationStageFieldset::INPUT_WARNING]) ?? 0);

        $validationStage->setWarning(($warning == 1));
        $commentaires = ($data[ValidationStageFieldset::INPUT_COMMENTAIRES]) ?? "";
        $commentairesCaches = ($data[ValidationStageFieldset::INPUT_COMMENTAIRES_CACHE]) ?? "";
        $validationStage->setCommentaire(trim($commentaires));
        $validationStage->setCommentaireCache(trim($commentairesCaches));
        $stage->setValidationStage($validationStage);

        return $stage;
    }
}