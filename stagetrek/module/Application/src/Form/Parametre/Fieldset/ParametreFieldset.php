<?php


namespace Application\Form\Parametre\Fieldset;

use Application\Entity\Db\Parametre;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\DescriptionInputAwareTrait;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Laminas\Form\Element\Text;
use Laminas\Validator\Callback;

/**
 * Class ParametreFieldset
 * @package Application\Form\Fieldset
 */
class ParametreFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use OrdreInputAwareTrait;
    use LibelleInputAwareTrait;
    use DescriptionInputAwareTrait;

    public function init() : static
    {
        $this->setDescriptionMaxSize(255);

        $this->initIdInput();
        $this->initLibelleInput();
        $this->initOrdreInput();
        $this->initDescriptionInput();
        $this->initParamValue();
        return $this;
    }

    const PARAM_VALUE = "value";
    protected function initParamValue() : static
    {
        $this->add([
            'name' => self::PARAM_VALUE,
            'type' => Text::class,
            'options' => [
                'label' => "Valeur",
            ],
            'attributes' => [
                'id' => self::PARAM_VALUE,
            ],
        ]);

        $this->setInputfilterSpecification(self::PARAM_VALUE, [
            "name" => self::PARAM_VALUE,
            "required" => false,
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "La valeur n'est pas du type attendue",
                    ],
                    'callback' => function ($value, $context = []) {
                        $id = (key_exists(self::ID, $context)) ? $context[self::ID] : null;
                        /** @var Parametre $param */
                        $param = $this->getObjectManager()->getRepository(Parametre::class)->find($id);
                        if(!$param){
                            return true; //False car le paramètre n'existe pas (on ne devrais même pas pouvoir atteindre cette page
                        }
                        if(!$param->getParametreType() || !$param->getParametreType()->getCastFonction()){
                            return true;
                        }
                        $castedValue = $param->getParametreType()->getCastFonction()($value);
                        return match ($param->getParametreType()->getLibelle()) {
                            'Boolean' => $value == "1" || $value == "0",
                            default => (strlen($value) == strlen("" . $castedValue)),
                        };
                    },
                    'break_chain_on_failure' => false,
                ]],
            ],
        ]);
        return $this;
    }
}