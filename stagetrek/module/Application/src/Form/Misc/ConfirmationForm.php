<?php

namespace Application\Form\Misc;

use Application\Form\Abstrait\Traits\FormElementTrait;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Form;
use UnicaenApp\View\Helper\Messenger;

/**
 * Class ConfirmationForm
 * @package Application\Form\Default
 * @author Thibaut Vallée <thibaut.vallee at unicaen.fr>
 * Formulaire permettant de demandé la confirmation avant d'effectuer une action
 * Fonctionnement assez Simple : 2 Boutons Oui/Non
 * Possibilité rapide de rajouter des éléments a la volé pour fournir en post des élément
 */
class ConfirmationForm extends Form
{
    use FormElementTrait;

    //Const
    const CSRF ="csrf";
    const INPUT_CONFIRM = "confirmBtn";
    const CONFIRM_VALUE = "confirmer";
    const INPUT_RESPONSE = "reponse";
    const INPUT_CANCEL = "cancelBtn";
    const CANCEL_VALUE = "annuler";

    const CANCEL_EVENT = "cancel-form";

    protected ?string $questionConfirmation = null;

    /**
     * @return string|null
     */
    public function getQuestionConfirmation() : ?string
    {
        return $this->questionConfirmation;
    }

    /**
     * @param mixed $questionConfirmation
     */
    public function setConfirmationQuestion(string $questionConfirmation) : static
    {
        $this->questionConfirmation = $questionConfirmation;
        return $this;
    }

    /**
     * @param string $label
     * @return \Application\Form\Misc\ConfirmationForm
     */
    public function setConfirmationBtnLabel(string $label) : static
    {
        $this->get(self::INPUT_CONFIRM)->setLabel($label);
        return $this;
    }

    /**
     * @param string $label
     * @return \Application\Form\Misc\ConfirmationForm
     */
    public function setCancelBtnLabel(string $label) : static
    {
        $this->get(self::INPUT_CANCEL)->setLabel($label);
        return $this;
    }

    protected bool $hasBeenConfirmed = false;

    /** @return boolean */
    public function hasBeenConfirmed() : bool
    {
        return $this->hasBeenConfirmed;
    }

    /**
     * @param boolean $confirmation
     * @return \Application\Form\Misc\ConfirmationForm
     */
    public function setHasBeenConfirmed(bool $confirmation) : static
    {
        $this->hasBeenConfirmed = $confirmation;
        return $this;
    }

    /**
     * EtudiantForm constructor.
     */
    public function __construct()
    {
        parent::__construct('ConfirmationForm');
    }

    public function init(): void
    {
        parent::init();
        $this->setAttribute("id", uniqid('confirmation-form_'));
        $this->setAttribute("action", $this->getCurrentUrl());
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'loadingForm');
        $this->setConfirmationQuestion("Voulez-vous vraiment effectuer cette action ?");
        $this->hasBeenConfirmed = false;
        $this->addElements();
        $this->setData([]); //Pour que la confirmation par défaut soit possible
    }

    protected function addElements(): static
    {
        $this->hiddenElements = [];
        $this->othersInputs = [];

        $this->add([ //Utilisé pour distinguer les appels de l'actions avec des paramètres POST autres que "reponse"
            'name' => self::INPUT_RESPONSE,
            'type' => Hidden::class,
            'attributes' => [
                'id' => self::INPUT_RESPONSE,
                'value' => self::CANCEL_VALUE,//par défaut
            ],
        ]);

        $this->add([
            'type' => Button::class,
            'name' => self::INPUT_CONFIRM,
            'options' => [
                'label' => "<i class='fas fa-check'></i> Oui",
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-success form-reponse',
                'id' => self::INPUT_CONFIRM,
                'value' => self::CONFIRM_VALUE,
            ],
        ]);
        $this->add([
            'type' => Button::class,
            'name' => self::INPUT_CANCEL,
            'options' => [
                'label' => "<i class='fas fa-times'></i> Non",
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'type' => 'button',
                'class' => 'btn btn-danger form-reponse',
                'id' => self::INPUT_CANCEL,
                'value' => self::CANCEL_VALUE,
            ],
        ]);

        $this->add(new Csrf(self::CSRF));
        return $this;
    }

    /** @var Hidden[] $hiddenElements */
    protected array $hiddenElements = [];

    /**
     * @return Hidden[]
     */
    public function getHiddenElements() : array
    {
        return $this->hiddenElements;
    }

    /**
     * @param Hidden[] $hiddenElements
     */
    public function setHiddenElement(array $hiddenElements) : static
    {
        $this->hiddenElements = $hiddenElements;
        return $this;
    }

    /**
     * @param String $name
     * @param String $value
     * @return \Application\Form\Misc\ConfirmationForm
     */
    public function addHiddenElement(string $name, mixed $value) : static
    {
        $this->add([
            'name' => $name,
            'type' => Hidden::class,
            'attributes' => [
                'id' => $name,
                'value' => $value,
            ],
        ]);
        $this->hiddenElements[$name] = $this->get($name);
        return $this;
    }

    protected array $othersInputs = [];
    public function getOthersInputs():  array
    {
        return $this->othersInputs;
    }
    public function addInputs($name, $inputData) : static
    {
        //Pour la vérification
        $inputData['name'] = $name;
        $this->add($inputData);
        $this->othersInputs[$name] = $this->get($name);
        return $this;
    }


    //Permet d'avoir des messages dans le formulaire de confirmation
    public function addMessage($msg, $priority=Messenger::INFO) : static
    {
        $msg = sprintf("<div class='alert alert-%s'>%s</div>",$priority, $msg);
        $this->setMessages([$msg]);
        return $this;
    }


//
//    /** @var array $messages */
//    protected $messages;
//    public function hasMessage(){return sizeof($this->messages) > 0;}
//    public function getMessages(){return $this->messages;}
//
//    public function addInfoMessage($message)
//    {
//        $this->messages[Messenger::INFO][] = $message;
//        $this->hasMessage = true;
//    }
//
//    public function addSuccessMessage($message)
//    {
//        $this->messages[Messenger::SUCCESS][] = $message;
//        $this->hasMessage = true;
//    }
//
//    public function addWarningMessage($message)
//    {
//        $this->messages[Messenger::WARNING][] = $message;
//        $this->hasMessage = true;
//    }
}