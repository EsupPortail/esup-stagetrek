<?php

namespace Application\Form\Contrainte\Factory;

use Application\Form\Contrainte\Validator\ContrainteCursusValidator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContrainteCursusValidatorFactory
 * @package Application\Form\ContraintesCursus\Factory
 */
class ContrainteCursusValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusValidator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusValidator
    {
        return new ContrainteCursusValidator($options);
    }
}