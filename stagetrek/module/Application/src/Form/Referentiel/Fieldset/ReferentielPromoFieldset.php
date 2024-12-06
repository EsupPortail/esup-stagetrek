<?php


namespace Application\Form\Referentiel\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Application\Form\Referentiel\Element\SourceSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Text;
use Laminas\Validator\StringLength;

/**
 * Class ReferentielPromoFieldset
 * @package Application\Form\Fieldset
 */
class ReferentielPromoFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;

    public function init(): void
    {
        $this->initIdInput();
        $this->initCodeInput();
        $this->initLibelleInput();
        $this->initOrdreInput();
        $this->initSourceInput();
        $this->initCodePromoInput();
    }


    const SOURCE = "source";
    protected function initSourceInput(): void
    {
        $this->add([
            'type' => SourceSelectPicker::class,
            'name' => self::SOURCE,
            'options' => [
                'label' => 'Source',
                "empty_option" => 'Sélectionnez une source de référence',
            ],
            'attributes' => [
                'id' => self::SOURCE,
                'autofocus' => true
            ],
        ]);

        $this->setInputfilterSpecification(self::SOURCE, [
                "name" => self::SOURCE,
                'required' => true,
        ]);
    }


    const CODE_PROMO = "codePromo";
    protected function initCodePromoInput(): void
    {
        $this->add([
            'name' => self::CODE_PROMO,
            'type' => Text::class,
            'options' => [
                'label' => 'Code promo',
            ],
            'attributes' => [
                "id" => self::CODE_PROMO,
                "placeholder" => 'Saisir le code de la promo dans la source de référence',
            ],
        ]);

        $this->setInputfilterSpecification(self::CODE_PROMO, [
                "name" => self::CODE_PROMO,
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ],
                    ],
                ],
        ]);
    }
}