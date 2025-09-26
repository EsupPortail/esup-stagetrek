<?php
namespace Application\Form\Convention;

use Application\Entity\Db\Stage;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Convention\Fieldset\ConventionStageTeleversementFieldset;

class ConventionStageTeleversementForm extends AbstractEntityForm
{

    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ConventionStageTeleversementFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }
}
