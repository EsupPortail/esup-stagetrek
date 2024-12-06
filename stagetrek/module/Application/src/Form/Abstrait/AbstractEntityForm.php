<?php


namespace Application\Form\Abstrait;


use Application\Form\Abstrait\Interfaces\AbstractFormConstantesInterface;
use Application\Form\Abstrait\Traits\FormElementTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Csrf;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\View\Helper\FlashMessenger;
use UnicaenApp\View\Helper\Messenger;

abstract class AbstractEntityForm extends Form implements AbstractFormConstantesInterface, ObjectManagerAwareInterface
{
    use FormElementTrait;
    use ProvidesObjectManager;

    protected ?string $title = null;

    /**
     * @return string|null
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \Application\Form\Abstrait\AbstractEntityForm
     */
    public function setTitle(string $title) : static
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
        $this->setAttribute('class', 'loadingForm');
        $this->title = ($name) ?: "";
    }

    public function init(): void
    {
        parent::init();
        $this
            ->setAttribute('method', 'post')
            ->setAttribute('action', $this->getCurrentUrl())
            ->setAttribute('class', 'loadingForm')
            ->setInputFilter(new InputFilter());

        $this->initSubmit();
        $this->initCSRF();
    }

    protected function initSubmit() : void
    {
        $this->add([
            'type' => Button::class,
            'name' => self::SUBMIT,
            'options' => [
                'label' => self::LABEL_SUBMIT_CONFIRM,
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'id' => self::SUBMIT,
                'type' => 'submit',
                'class' => 'btn btn-success',
            ],
        ]);
    }

    protected function initCSRF() : void
    {
        $this->add(new Csrf(self::CSRF));
    }

    /** @var Fieldset|null $entityFieldset */
    protected ?Fieldset $entityFieldset = null;

    /**
     * @return Fieldset
     */
    public function getEntityFieldset(): Fieldset
    {
        return $this->entityFieldset;
    }

    /**
     * @param Fieldset $entityFieldset
     */
    public function setEntityFieldset(Fieldset $entityFieldset): void
    {
        $this->entityFieldset = $entityFieldset;
        $this->entityFieldset->setOptions([
            'use_as_base_fieldset' => true,
        ]);
        $this->add($this->entityFieldset);
    }

    //On regarde si l'élément demandé appartient au fieldset
    public function get($elementOrFieldset) : ElementInterface
    {
        if (!$this->has($elementOrFieldset) && isset($this->entityFieldset)) {
            return $this->entityFieldset->get($elementOrFieldset);
        }

        return parent::get($elementOrFieldset);
    }

    protected function addStrategies() : void
    {
    }

    protected ?FlashMessenger $flashMessenger = null;

    protected function getFlashMessenger()
    {
        if (!$this->flashMessenger) {
            $this->flashMessenger = $this->getViewHelperManager()->get('flashMessenger');
        }
        return $this->flashMessenger;
    }

    const FORM_MESSAGE_NAMESPACE = "form-messages";

    public function getMessageNamespace(): string
    {
        return self::FORM_MESSAGE_NAMESPACE;
    }

    public function addInfoMessage($message): static
    {
        $namespace = self::FORM_MESSAGE_NAMESPACE . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::INFO;
        $this->getFlashMessenger()->addMessage($message, $namespace);
        return $this;
    }

    public function addSuccessMessage($message): static
    {
        $namespace = self::FORM_MESSAGE_NAMESPACE . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::SUCCESS;
        $this->getFlashMessenger()->addMessage($message, $namespace);
        return $this;
    }

    public function addWarningMessage($message): static
    {
        $namespace = self::FORM_MESSAGE_NAMESPACE . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::WARNING;
        $this->getFlashMessenger()->addMessage($message, $namespace);
        return $this;
    }

    public function addErrorMessage($message): static
    {
        $namespace = self::FORM_MESSAGE_NAMESPACE . Messenger::NAMESPACED_SEVERITY_SEPARATOR . Messenger::ERROR;
        $this->getFlashMessenger()->addMessage($message, $namespace);
        return $this;
    }

}