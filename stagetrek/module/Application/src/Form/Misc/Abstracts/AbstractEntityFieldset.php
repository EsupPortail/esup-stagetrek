<?php


namespace Application\Form\Misc\Abstracts;

use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

abstract class AbstractEntityFieldset extends Fieldset
    implements
    InputFilterProviderInterface,
    DefaultInputKeyInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use InputFilterProviderTrait;
}