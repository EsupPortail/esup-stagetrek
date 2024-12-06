<?php

namespace Application\Form\Misc\Traits;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Text;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;

/**
 * @method void setInputfilterSpecification(string $inputId, array $specification)
 * @method void add($elementOrFieldset, array $flags = [])
 */

trait MailInputAwareTrait
{

    protected function initMailInput(): static
    {
        $this->add([
            "name" => DefaultInputKeyInterface::MAIL,
            'type' => Text::class,
            'options' => [
                'label' => $this->mailLabel,
            ],
            'attributes' => [
                "id" => DefaultInputKeyInterface::MAIL,
                "placeholder" => $this->mailPlaceHolder,
            ],
        ]);

        $this->setInputfilterSpecification(DefaultInputKeyInterface::MAIL, [
            'name' => DefaultInputKeyInterface::MAIL,
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
                        'max' => 64,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "L'adresse mail n'est pas valide.",
                        ],
                        'callback' => function ($value) {
                            $mail = trim($value);
                            if ($mail == "") {
                                return true;
                            }
                            return filter_var($mail, FILTER_VALIDATE_EMAIL);
                        },
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
        return $this;
    }

    protected string $mailLabel="Mail";
    protected string  $mailPlaceHolder="Saisir une adresse mail";

    /**
     * @return string
     */
    public function getMailLabel(): string
    {
        return $this->mailLabel;
    }

    /**
     * @param string $mailLabel
     * @return \Application\Form\Misc\Traits\MailInputAwareTrait
     */
    public function setMailLabel(string $mailLabel): static
    {
        $this->mailLabel = $mailLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getMailPlaceHolder(): string
    {
        return $this->mailPlaceHolder;
    }

    /**
     * @param string $mailPlaceHolder
     * @return \Application\Form\Misc\Traits\MailInputAwareTrait
     */
    public function setMailPlaceHolder(string $mailPlaceHolder): static
    {
        $this->mailPlaceHolder = $mailPlaceHolder;
        return $this;
    }
}