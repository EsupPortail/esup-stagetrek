<?php


namespace Application\Form\TerrainStage\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\AcronymeInputAwareTrait;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;

/**
 * Class CategorieStageFieldset
 * @package Application\Form\TerrainStage\Fieldset
 */
class CategorieStageFieldset extends AbstractEntityFieldset
{

    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;
    use AcronymeInputAwareTrait;

    public function init() : static
    {
        $this->initIdInput();
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initOrdreInput();
        $this->initAcronymeInput();
        $this->initInputCategorie();
        return $this;
    }

    const CATEGORIE_PRINCIPALE = "categoriePrincipale";
    protected function initInputCategorie(): static
    {
        $this->add([
            'name' => self::CATEGORIE_PRINCIPALE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Catégorie de stage principale",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::CATEGORIE_PRINCIPALE,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->setInputfilterSpecification(self::CATEGORIE_PRINCIPALE, [
            'name' => self::CATEGORIE_PRINCIPALE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        return $this;
    }
}