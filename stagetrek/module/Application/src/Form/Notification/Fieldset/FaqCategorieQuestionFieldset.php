<?php


namespace Application\Form\Notification\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
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

    public function init(): void
    {
        $this->initIdInput();
        $this->initLibelleInput();
        $this->initOrdreInput();
    }
}