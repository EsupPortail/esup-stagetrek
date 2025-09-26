<?php


namespace Application\Form\Notification\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;

/**
 * Class FaqCategorieQuestionFieldset
 * @package Application\Form\Fieldset
 */
class FaqCategorieQuestionFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;
    use CodeInputAwareTrait;

    public function init(): static
    {
        $this->initIdInput();
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initOrdreInput();
        return $this;
    }
}