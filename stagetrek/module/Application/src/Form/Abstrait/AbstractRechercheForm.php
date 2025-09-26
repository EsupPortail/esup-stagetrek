<?php


namespace Application\Form\Abstrait;


use Application\Form\Abstrait\Traits\FormElementTrait;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

abstract class AbstractRechercheForm extends Form
    implements
    InputFilterProviderInterface
{
    use FormElementTrait;

    const INVALIDE_ERROR_MESSAGE = "Recherche impossible";
    protected ?string $title = null;

    /**
     * @return string|null
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return AbstractRechercheForm
     */
    public function setTitle(?string $title) : AbstractRechercheForm
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param null|int|string $name Optional name for the element
     * @param array $options Optional options for the element
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->title = ($name) ?: "";
    }

    const INPUT_EFFACER='effacer';
    const INPUT_RECHERCHER='rechercher';
    const CSRF='csrf';

    public function init(): static
    {
        parent::init();
        $this->setAttribute("action", $this->getCurrentUrl());
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'rechercherForm');


        $this->add(new Csrf(self::CSRF));

        $this->add([
            'type' => Button::class,
            'name' => self::INPUT_EFFACER,
            'options' => [
                'label' => '<span class="fas fa-backspace"></span> Effacer',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'id' =>  self::INPUT_EFFACER,
                'type' => 'button',
                'class' => 'btn btn-secondary reset',
                'title' => "Effacer les critères de recherche"
            ],
        ]);



        $this->add([
            'type' => Button::class,
            'name' => self::INPUT_RECHERCHER,
            'options' => [
                'label' => '<span class="fas fa-search"></span> Rechercher',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'id' =>  self::INPUT_RECHERCHER,
                'type' => 'submit',
                'class' => 'btn btn-secondary',
            ],
        ]);
        return $this;
    }
}