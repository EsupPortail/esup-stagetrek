<?php


namespace Application\Form\Referentiel\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\CodeInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;

/**
 * Class ReferentielPromoFieldset
 * @package Application\Form\Fieldset
 */
class SourceFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use CodeInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;

    public function init() : static
    {
        $this->initIdInput();
        $this->initCodeInput(true);
        $this->initLibelleInput();
        $this->initOrdreInput();
        return $this;
    }
}