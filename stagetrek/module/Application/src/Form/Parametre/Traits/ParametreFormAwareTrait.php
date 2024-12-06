<?php


namespace Application\Form\Parametre\Traits;

use Application\Form\Parametre\ParametreForm;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;

/**
 * Traits ParametreFormAwareTrait
 * @package Application\Form\Traits
 */
trait  ParametreFormAwareTrait
{
    /**
     * @var ParametreForm|null $parametreForm ;
     */
    protected ?ParametreForm $parametreForm = null;

    /**
     * @return ParametreForm
     */
    public function getEditParametreForm(): ParametreForm
    {
        $form = $this->parametreForm;
        $form->get($form::SUBMIT)->setLabel(sprintf("%s %s", Icone::SAVE, Label::MODIFIER));
        $form->get($form::INPUT_SUBMIT)->setAttribute("class", "btn btn-primary");
        return $form;
    }

    /**
     * @param ParametreForm $parametreForm
     */
    public function setParametreForm(ParametreForm $parametreForm): void
    {
        $this->parametreForm = $parametreForm;
    }


}