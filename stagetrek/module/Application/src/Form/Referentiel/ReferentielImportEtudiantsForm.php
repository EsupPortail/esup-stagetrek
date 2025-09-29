<?php


namespace Application\Form\Referentiel;


use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Referentiel\Element\ReferentielPromoSelectPicker;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;

/**
 * Class ReferentielImportEtudiantsForm
 * @package Application\Form\Etudiant
 */
class ReferentielImportEtudiantsForm extends AbstractImportEtudiantsForm
{
    public static function getKey(): string
    {
        return 'referentiel';
    }

    public function getFormLabel(): string
    {
        return "Depuis un référentiel";
    }

    public function init(): static
    {
        parent::init();
        $this->initReferentielInput();
        $this->initAnneeInput();
        return $this;
    }


    const INPUT_REFERENTIEL = 'referentiel';
    private function initReferentielInput() : static
    {

        $this->add([
            'type' => ReferentielPromoSelectPicker::class,
            'name' => self::INPUT_REFERENTIEL,
            'options' => [
                'label' => "Référentiel de promo",
                'empty_option' => "Selectionnée le référentiel",
            ],
            'attributes' => [
                'id' => self::INPUT_REFERENTIEL,
            ],
        ]);

        $this->setInputfilterSpecification(self::INPUT_REFERENTIEL, [
            'name' => self::INPUT_REFERENTIEL,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
        ]);

        return $this;
    }

    const INPUT_ANNEE = 'annee';
    private function initAnneeInput() : static
    {
        $this->add([
            'type' => AnneeUniversitaireSelectPicker::class,
            'name' => self::INPUT_ANNEE,
            'options' => [
                'label' => "Année universitaire",
                'empty_option' => "Selectionnez une année universitaire",
            ],
            'attributes' => [
                'id' => self::INPUT_ANNEE,
            ],
        ]);

        $this->setInputfilterSpecification(self::INPUT_ANNEE, [
            'name' => self::INPUT_ANNEE,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
        ]);

        return $this;
    }
}